<?php
namespace App\Services;

use App\Models\TransactionChargesConfig;
use App\Models\TransactionChargesLog;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WalletDepositService
{
    public function creditWallet(User $user, string $walletKey, float $amount): bool
    {
        $wallet = $user->wallets()->where('key', $walletKey)->first();

        if (! $wallet) {
            Log::warning("Wallet with key {$walletKey} not found for user {$user->id}");
            return false;
        }

        if ($this->checkLimits($wallet, $amount) && $this->checkMaxBalance($wallet, $amount)) {
            $charges   = $this->calculateCharges($amount);
            $netAmount = $amount - $charges;

            $wallet->balance += $netAmount;
            $wallet->save();

            Log::info("Wallet credited for user {$user->id} with amount {$netAmount}");
            return true;
        }

        Log::warning("Failed to credit wallet for user {$user->id} with amount {$amount}");
        return false;
    }

    public function checkLimits(Wallet $wallet, float $amount): bool
    {
        $limits = $wallet->getEffectiveCreditLimits();
        $result = $this->checkTransactionLimits($wallet, $amount, 'credit', $limits);
        Log::alert($result);
        return $result['can_transact'];
    }

    private function checkMaxBalance(Wallet $wallet, float $amount): bool
    {
        // Implement your max balance check logic here
        $limits     = $wallet->getEffectiveCreditLimits();
        $maxBalance = $limits->maximum_balance; //50000.0; // Example: maximum wallet balance

        return ($wallet->balance + $amount) <= $maxBalance;
    }

    public function getTransactionChargeConfig(string $transactionType, float $amount, int $walletId): array
    {
        Log::info("Fetching charge config for type: $transactionType and amount: $amount");

        // Define a cache key based on the transaction type
        $cacheKey = 'transaction_charge_config_' . $transactionType;

        // Attempt to retrieve the charge configuration from the cache
        $charge = Cache::remember($cacheKey, 60, function () use ($transactionType) {
            return TransactionChargesConfig::where('transaction_type', $transactionType)->first();
        });

        // Get charge amount and charge percent, defaulting to 0 if not set
        $chargeAmount  = $charge ? $charge->charge_amount : 0;
        $chargePercent = $charge ? $charge->charge_percent : 0;

        // Check for custom wallet charges
        if ($walletId) {
            Log::alert("Found Custom Charge");
            $wallet = Wallet::find($walletId);
            Log::alert($wallet);
            if ($wallet) {
                Log::info("Wallet found with ID: $walletId");
                if ($wallet->customDepositCharge) {
                    Log::info("Custom wallet charge found with charge amount: " . $wallet->customDepositCharge->charge_amount . " and charge percent: " . $wallet->customDepositCharge->charge_percent);
                    $chargeAmount  = $wallet->customDepositCharge->charge_amount ?? $chargeAmount;
                    $chargePercent = $wallet->customDepositCharge->charge_percent ?? $chargePercent;
                } else {
                    Log::info("No custom wallet charge found for wallet with ID: $walletId");
                }
            } else {
                Log::info("No wallet found with ID: $walletId");
            }
        }

        Log::info("Charge Amount: $chargeAmount, Charge Percent: $chargePercent");

        return [
            'charge_amount'     => $chargeAmount,
            'type'              => $transactionType,
            'calculated_charge' => $chargeAmount + ($chargePercent / 100) * $amount,
            'charge_percent'    => $chargePercent,
        ];
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

    public function canCredit(Wallet $wallet, float $amount): array
    {
        $limits = $wallet->getEffectiveCreditLimits();
        return $this->checkTransactionLimits($wallet, $amount, 'credit', $limits);
    }

    private function checkTransactionLimits(Wallet $wallet, float $amount, string $type, array $limits): array
    {

        Log::alert("Here Now");
        $dailyTotal = $wallet->transactions()
            ->where('type', $type)
            ->whereDate('created_at', today())
            ->sum('amount');

        if ($dailyTotal + $amount > $limits['daily_limit']) {
            return [
                'can_transact'    => false,
                'message'         => ucfirst($type) . ' daily limit exceeded',
                'limit_type'      => 'daily',
                'spent'           => number_format($dailyTotal, 2),
                'remaining_limit' => number_format($limits['daily_limit'] - $dailyTotal, 2),
            ];
        }

        $weeklyTotal = $wallet->transactions()
            ->where('type', $type)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        if ($weeklyTotal + $amount > $limits['weekly_limit']) {
            return [
                'can_transact'    => false,
                'message'         => ucfirst($type) . ' weekly limit exceeded',
                'limit_type'      => 'weekly',
                'spent'           => number_format($weeklyTotal, 2),
                'remaining_limit' => number_format($limits['weekly_limit'] - $weeklyTotal, 2),
            ];
        }

        $monthlyTotal = $wallet->transactions()
            ->where('type', $type)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        if ($monthlyTotal + $amount > $limits['monthly_limit']) {
            return [
                'can_transact'    => false,
                'message'         => ucfirst($type) . ' monthly limit exceeded',
                'limit_type'      => 'monthly',
                'spent'           => number_format($monthlyTotal, 2),
                'remaining_limit' => number_format($limits['monthly_limit'] - $monthlyTotal, 2),
            ];
        }

        return [
            'can_transact' => true,
            'message'      => 'Transaction within ' . $type . ' limits',
        ];
    }

    public function checkTransactionCharges(float $amount, string $serviceType, int $walletId): array
    {
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'The amount must be greater than zero.',
            ];
        }

        // Get the transaction charge configuration
        $chargeConfig = $this->getTransactionChargeConfig($serviceType, $amount, $walletId);

        // Prepare the response
        return [
            'success'           => true,
            'service_type'      => $serviceType,
            'fixed_charge'      => $chargeConfig['charge_amount'],
            'charge_percent'    => $chargeConfig['charge_percent'],
            'calculated_charge' => $chargeConfig['calculated_charge'],
            'amount'            => $amount,
            'net_amount'        => $amount - $chargeConfig['calculated_charge'], // Total amount including charges
        ];
    }
}
