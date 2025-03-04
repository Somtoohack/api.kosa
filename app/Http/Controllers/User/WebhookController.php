<?php
namespace App\Http\Controllers\User;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Services\KosaBaseMicroservices\RedbillerService;
use App\Services\WalletDepositService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;

class WebhookController extends Controller
{

    protected RedbillerService $redbillerService;
    protected WalletDepositService $walletDepositService;

    public function __construct(RedbillerService $redbillerService, WalletDepositService $walletDepositService)
    {
        $this->redbillerService     = $redbillerService;
        $this->walletDepositService = $walletDepositService;
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

            Log::info("Webhook received for deposit--------: " . $reference);
            Log::info("Verifying  --------  " . $reference);

            $payload = ['reference' => $reference];

            // Verify the deposit using the reference
            $verificationResult = $this->redbillerService->verifyDepositReference($payload);

            // Log the result of the deposit verification
            if ($verificationResult['status'] === 'true' && $verificationResult['details']['status'] === 'Approved') {
                Log::info('Deposit verified successfully for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendResponse(
                    $verificationResult,
                    'Deposit successfully verified.'
                );
            } else {
                Log::warning('Deposit verification failed for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
            }
        } catch (Exception $e) {
            Log::error('Error verifying deposit: ' . $e->getMessage());
            return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
        } finally {
            Log::info("\n Line Break --------------------");
        }
    }

    public function verifyDepositWebhook(Request $request): JsonResponse
    {
        try {
            // $wallet = Wallet::all()->first();

            // $data = $this->walletDepositService->getTransactionChargeConfig('deposit', 40000, 1);

            // return response()->json($data, 200);

            $request->validate([
                'reference' => 'required|string',
            ]);

            $reference = $request->input('reference');

            Log::info("Verifying  --------  " . $reference);

            $payload = ['reference' => $reference];

            // Verify the deposit using the reference
            $verificationResult = $this->redbillerService->verifyDepositReference($payload);

            // Log the result of the deposit verification
            if ($verificationResult['status'] === 'true' && $verificationResult['details']['status'] === 'Approved') {
                Log::info('Deposit verified successfully for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendResponse(
                    $verificationResult,
                    'Deposit successfully verified.'
                );
            } else {
                Log::warning('Deposit verification failed for reference: ' . $reference, [
                    'verification_result' => $verificationResult,
                ]);
                return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
            }
        } catch (Exception $e) {
            Log::error('Error verifying deposit: ' . $e->getMessage());
            return $this->sendError('Webhook verification failed', [], ErrorCodes::FAILED);
        }
    }

}