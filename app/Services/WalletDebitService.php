<?php
namespace App\Services;

use App\Mail\TransactionNotification;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WalletDebitService
{
    public function debitWallet(Wallet $wallet, float $amount): bool
    {
        try {
            return DB::transaction(function () use ($wallet, $amount) {
                // Lock the wallet record for update to prevent race conditions
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

                if ($this->checkLimits($wallet, $amount)) {
                    $wallet->balance -= $amount;
                    $wallet->save();
                    Log::info("Wallet debited for user {$wallet->user->email} with amount {$amount}");
                    return true;
                } else {
                    Log::warning("Transaction Limit Exceeded.");
                    return false;
                }
            });
        } catch (Exception $e) {
            Log::error("Error debiting wallet for user {$wallet->user->email}: " . $e->getMessage());
            return false;
        }
    }

    public function checkLimits(Wallet $wallet, float $amount): bool
    {
        $limits = $wallet->getEffectiveDebitLimits();
        $result = checkTransactionLimits($wallet, $amount, 'debit', $limits);
        return $result['can_transact'];
    }

    public function sendWithdrawalNotification($deposit)
    {
        try {
            $wallet          = Wallet::find($deposit->wallet_id);
            $currencyCode    = $wallet->currency->code;
            $formattedAmount = $currencyCode . ' ' . number_format($deposit->amount, 2);
            $message         = "<p>Dear {$wallet->user->name},</p>
<p>We are pleased to inform you that you have received <strong>{$formattedAmount}</strong> in your <strong>{$currencyCode}</strong> wallet from <strong>{$deposit->sender_name}</strong>.</p>
<p>Your transaction reference is <strong>{$deposit->provider_reference}</strong>.</p>";
            Mail::to($wallet->user->email)->queue(new TransactionNotification(
                'Transaction Notification', $message
            ));
            Log::info("Deposit notification sent to user {$wallet->user->email} for amount {$formattedAmount} \n {$message}");
        } catch (Exception $e) {
            Log::error("Failed to send deposit notification to user {$wallet->user->email}: " . $e->getMessage());
        }
    }

}