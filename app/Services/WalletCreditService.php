<?php
namespace App\Services;

use App\Models\TransactionChargesLog;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletCreditService
{
    public function creditWallet(Wallet $wallet, float $amount): bool
    {
        try {
            return DB::transaction(function () use ($wallet, $amount) {
                // Lock the wallet record for update to prevent race conditions
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

                if ($this->checkLimits($wallet, $amount) && $this->checkMaxBalance($wallet, $amount)) {
                    $wallet->balance += $amount;
                    $wallet->save();
                    Log::info("Wallet credited for user {$wallet->user->email} with amount {$amount}");
                    return true;
                } else {
                    // Log and credit the amount to the pending balance
                    $wallet->pending_balance += $amount;
                    $wallet->save();

                    Log::warning("Failed to credit wallet for user {$wallet->user->email} with amount {$amount}. Amount added to pending balance.");
                    return false;
                }
            });
        } catch (Exception $e) {
            Log::error("Error crediting wallet for user {$wallet->user->email}: " . $e->getMessage());
            return false;
        }
    }

    public function movePendingToBalance(Wallet $wallet): bool
    {
        try {
            return DB::transaction(function () use ($wallet) {
                // Lock the wallet record for update to prevent race conditions
                $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

                if ($wallet->pending_balance > 0) {
                    $wallet->balance += $wallet->pending_balance;
                    $wallet->pending_balance = 0;
                    $wallet->save();

                    Log::info("Pending balance moved to main balance for user {$wallet->user_id}");
                    return true;
                } else {
                    Log::warning("No pending balance to move for user {$wallet->user_id}");
                    return false;
                }
            });
        } catch (Exception $e) {
            Log::error("Error moving pending balance to main balance for user {$wallet->user_id}: " . $e->getMessage());
            return false;
        }
    }

    private function checkLimits(Wallet $wallet, float $amount): bool
    {
        $limits = $wallet->getEffectiveCreditLimits();
        $result = checkTransactionLimits($wallet, $amount, 'credit', $limits);
        return $result['can_transact'];
    }

    private function checkMaxBalance(Wallet $wallet, float $amount): bool
    {
        $limits = $wallet->getEffectiveCreditLimits();
        $limits = (object) $limits;             // Convert array to object
        Log::info(json_encode($limits));        // Convert object to JSON string for logging
        $maxBalance = $limits->maximum_balance; // Accessing as an object
        return ($wallet->balance + $amount) <= $maxBalance;
    }

    private function logCharge(string $transactionReference, int $walletId, string $transactionType, float $chargeAmount, ?float $profitAmount): void
    {
        TransactionChargesLog::create([
            'transaction_reference' => $transactionReference,
            'wallet_id'             => $walletId,
            'transaction_type'      => $transactionType,
            'charge_amount'         => $chargeAmount,
            'profit_amount'         => $profitAmount,
        ]);
    }
}