<?php
namespace App\Http\Controllers\User;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\VirtualBankAccount;
use App\Models\Wallet;
use App\Services\RedbillerService;
use App\Services\WalletCreditService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class WebhookController extends Controller
{

    protected RedbillerService $redbillerService;
    protected WalletCreditService $walletCreditService;

    public function __construct(RedbillerService $redbillerService, WalletCreditService $walletCreditService)
    {
        $this->redbillerService    = $redbillerService;
        $this->walletCreditService = $walletCreditService;
    }

    /**
     * Verify bank transfer status
     */

    public function handleBankTransferWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        try {
            $details   = $request->input('details');
            $reference = $details['reference'] ?? null;
            $result    = $this->redbillerService->verifyBankTransfer($reference);

            // Log the result of the bank transfer verification
            if ($result['status'] === 'true') {
                Log::info('Bank transfer verified successfully for reference: ' . $reference, [
                    'verification_result' => $result,
                ]);
                return response()->json($result);
            } else {
                $this->handlePaymentFailed($data);
                Log::warning('Bank transfer verification failed for reference: ' . $reference, [
                    'verification_result' => $result,
                ]);
                return response()->json(['error' => 'Bank transfer verification failed.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error verifying bank transfer: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleDepositWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        try {
            $details   = $request->input('details');
            $reference = $details['reference'] ?? null;

            $initialBalance = 0;
            Log::info("Webhook received for deposit--------: " . $reference);
            Log::info("Verifying  --------  " . $reference);

            $payload            = ['reference' => $reference];
            $verificationResult = $this->redbillerService->verifyDepositReference($payload);
            if ($verificationResult['status'] === 'true' && $verificationResult['details']['status'] === 'Approved') {
                Log::info('Deposit verified successfully for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);

                //Check if deposit with the same reference already exists
                $existingDeposit = Deposit::where('provider_reference', $verificationResult['details']['reference'])->first();
                if ($existingDeposit) {
                    Log::info('Deposit already exists for reference: ' . $reference);
                    return $this->sendError('Deposit already exists', [], ErrorCodes::FAILED);
                }

                $vbaReference                     = $verificationResult['details']['sub_account']['reference'];
                $transactionReference             = $verificationResult['details']['reference'];
                $vba                              = VirtualBankAccount::where('reference', $vbaReference)->first();
                $wallet                           = Wallet::where('key', $vba->wallet_key)->first()->load('currency');
                $chargeDetails                    = getTransactionCharges($wallet, $verificationResult['details']['settlement'], 'deposit');
                $initialBalance                   = $wallet->balance;
                $deposit                          = new Deposit();
                $deposit->wallet_id               = $wallet->id;
                $deposit->virtual_bank_account_id = $vba->id;
                $deposit->provider_reference      = $transactionReference;
                $deposit->amount                  = $verificationResult['details']['amount'];

                $deposit->charge     = $chargeDetails->calculated_charge;
                $deposit->net_amount = $verificationResult['details']['settlement'] - $deposit->charge;
                $providerCharge      = $deposit->amount - $deposit->net_amount;
                $providerCharge += $deposit->charge;
                $deposit->charge                = $providerCharge;
                $deposit->payload               = json_encode($verificationResult);
                $deposit->status                = 'pending';
                $deposit->sender_name           = $verificationResult['details']['payer']['account_name'];
                $deposit->sender_account_number = $verificationResult['details']['payer']['account_no'];
                $deposit->sender_bank_name      = $verificationResult['details']['payer']['bank_name'];

                $deposit->save();
                $deposit = Deposit::find($deposit->id);

                try {
                    $credit = $this->walletCreditService->creditWallet($wallet, $deposit->net_amount);

                    if ($credit) {
                        $deposit->status = 'successful';
                        $deposit->save();

                        $wallet = Wallet::find($wallet->id);
                        logDepositStatement($wallet, $deposit, $initialBalance);
                        Log::info('Wallet credited successfully for deposit: ' . $transactionReference);
                    } else {
                        Log::error('Error crediting wallet for deposit: ' . $transactionReference);
                    }
                } catch (Exception $e) {
                    Log::error('Error during wallet crediting: ' . $e->getMessage());
                    return $this->sendError('Error during wallet crediting', [], ErrorCodes::FAILED);
                }

                return $this->sendResponse(
                    $deposit,
                    'Deposit successfully verified.'
                );
            } else {
                Log::warning('Deposit verification failed for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
            }
        } catch (Exception $e) {
            Log::error('Error verifying deposit: ' . $e);
            return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
        }
    }

    public function verifyDepositWebhook(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'reference' => 'required|string',
            ]);

            $reference = $request->input('reference');

            $initialBalance = 0;

            Log::info("Verifying  --------  " . $reference);

            $payload            = ['reference' => $reference];
            $verificationResult = $this->redbillerService->verifyDepositReference($payload);
            if ($verificationResult['status'] === 'true' && $verificationResult['details']['status'] === 'Approved') {
                Log::info('Deposit verified successfully for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);

                // Check if deposit with the same reference already exists
                $existingDeposit = Deposit::where('provider_reference', $verificationResult['details']['reference'])->first();
                if ($existingDeposit) {
                    Log::info('Deposit already exists for reference: ' . $reference);
                    return $this->sendError('Deposit already exists', [], ErrorCodes::FAILED);
                }

                $vbaReference         = $verificationResult['details']['sub_account']['reference'];
                $transactionReference = $verificationResult['details']['reference'];
                $vba                  = VirtualBankAccount::where('reference', $vbaReference)->first();

                if (! $vba) {
                    Log::error('Virtual Bank Account not found for reference: ' . $vbaReference);
                    return $this->sendError('Virtual Bank Account not found', [], ErrorCodes::FAILED);
                }

                $wallet                           = Wallet::where('key', $vba->wallet_key)->first()->load('currency');
                $chargeDetails                    = getTransactionCharges($wallet, $verificationResult['details']['settlement'], 'deposit');
                $initialBalance                   = $wallet->balance;
                $deposit                          = new Deposit();
                $deposit->wallet_id               = $wallet->id;
                $deposit->virtual_bank_account_id = $vba->id;
                $deposit->provider_reference      = $transactionReference;
                $deposit->amount                  = $verificationResult['details']['amount'];

                $deposit->charge     = $chargeDetails->calculated_charge;
                $deposit->net_amount = $verificationResult['details']['settlement'] - $deposit->charge;
                $providerCharge      = $deposit->amount - $deposit->net_amount;
                $providerCharge += $deposit->charge;
                $deposit->charge                = $providerCharge;
                $deposit->payload               = json_encode($verificationResult);
                $deposit->status                = 'pending';
                $deposit->sender_name           = $verificationResult['details']['payer']['account_name'];
                $deposit->sender_account_number = $verificationResult['details']['payer']['account_no'];
                $deposit->sender_bank_name      = $verificationResult['details']['payer']['bank_name'];

                $deposit->save();
                $deposit = Deposit::find($deposit->id);

                try {
                    $credit = $this->walletCreditService->creditWallet($wallet, $deposit->net_amount);

                    if ($credit) {
                        $deposit->status = 'successful';
                        $deposit->save();

                        $wallet = Wallet::find($wallet->id);
                        logDepositStatement($wallet, $deposit, $initialBalance);
                        Log::info('Wallet credited successfully for deposit: ' . $transactionReference);
                    } else {
                        Log::error('Error crediting wallet for deposit: ' . $transactionReference);
                    }
                } catch (Exception $e) {
                    Log::error('Error during wallet crediting: ' . $e->getMessage());
                    return $this->sendError('Error during wallet crediting', [], ErrorCodes::FAILED);
                }

                return $this->sendResponse(
                    $deposit,
                    'Deposit successfully verified.'
                );
            } else {
                Log::warning('Deposit verification failed for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
            }
        } catch (Exception $e) {
            Log::error('Error verifying deposit: ' . $e);
            return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
        }
    }

}