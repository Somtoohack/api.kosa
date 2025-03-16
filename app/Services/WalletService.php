<?php
namespace App\Services;

use App\Exceptions\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\TransactionChargesConfig;
use App\Models\TransactionChargesLog;
use App\Models\User;
use App\Models\Wallet;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Get the user's wallet balance.
     * Optimized to load the wallet with eager loading.
     */
    public function getBalance(User $user, Wallet $wallet): array
    {

        $wallet   = $wallet->load('currency');
        $currency = [
            'code'         => $wallet->currency->code,
            'symbol'       => $wallet->currency->symbol,
            'country_code' => $wallet->currency->country_code,
            'name'         => $wallet->currency->name,
            'flag'         => $wallet->currency->flag,
        ];
        return [
            'balances'         => [
                'available' => floatval($wallet->balance),
                'pending'   => floatval($wallet->pending_balance),
                'dispute'   => floatval($wallet->dispute_balance),
            ],
            'wallet_key'       => $wallet->key,
            'wallet_reference' => $wallet->reference,
            'currency'         => $currency,
        ];
    }

    /**
     * Deposit money into the user's wallet.
     * Checks credit limits, calculates charges, and updates balance.
     */
    public function deposit(User $user, float $amount, $walletKey, $payload): array
    {

        $wallet = $user->wallets()->where('key', $walletKey)->firstOrFail()->load('currency');

        if ($wallet->isCreditLoggingDisabled()) {
            return [
                'success' => false,
                'message' => 'Credits are currently disabled for this wallet. Please contact support.',
            ];
        }

        $creditCheck = $this->canCredit($wallet, $amount);
        if (! $creditCheck['can_transact']) {
            return [
                'success' => false,
                'message' => $creditCheck['message'],
                'reason'  => $creditCheck,
            ];
        }

        // Pre-compute the maximum balance check
        $maxBalance  = $wallet->limits->maximum_balance;
        $postBalance = $wallet->balance + $amount - 15; // Account for transaction charge
        if ($postBalance > $maxBalance) {
            return [
                'success'           => false,
                'message'           => 'Deposit exceeds maximum allowed balance for this wallet tier.',
                'current_balance'   => $wallet->balance,
                'max_balance'       => $maxBalance,
                'amount_receivable' => $postBalance > $maxBalance ? $maxBalance - $wallet->balance : $amount,
                'post_balance'      => $postBalance,
            ];
        }

        $update = $this->updateBalance($user->id, $wallet->key, $amount, 'credit', 'deposit');
        if ($update['success'] == true) {
            $depositLog = new Deposit();
        }
        return $update;
    }

    /**
     * Update wallet balance.
     * Optimized to calculate charges outside the transaction and queue notifications.
     */
    private function updateBalance(int $userId, $walletKey, float $amount, string $type, string $category): array
    {
        // Pre-calculate transaction charge
        $transactionChargeDetails = $this->getTransactionChargeConfig($category, $amount);
        $transactionCharge        = $transactionChargeDetails['calculated_charge'];

        try {
            $result = DB::transaction(function () use ($userId, $walletKey, $amount, $type, $category, $transactionCharge) {
                $wallet = Wallet::where('user_id', $userId)->where('key', $walletKey)->lockForUpdate()->firstOrFail();

                // $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->firstOrFail();

                $initialBalance = $wallet->balance;
                Log::alert($initialBalance);

                if ($amount <= 0) {
                    return [
                        'success' => false,
                        'message' => 'The amount must be greater than zero.',
                    ];
                }

                $transactionReference = $this->generateReference();
                $amountAfterCharge    = $amount - $transactionCharge;

                // Credit or Debit handling
                if ($type === 'credit') {
                    if ($amount <= $transactionCharge) {
                        return [
                            'success' => false,
                            'message' => 'Deposit amount must be greater than the transaction charge.',
                        ];
                    }
                    $wallet->balance += $amountAfterCharge;
                } elseif ($type === 'debit') {
                    if ($wallet->balance < $amount + $transactionCharge) {
                        return [
                            'success' => false,
                            'message' => 'Insufficient funds for withdrawal.',
                        ];
                    }
                    $wallet->balance -= ($amount + $transactionCharge);
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid transaction type.',
                    ];
                }

                $wallet->save();

                // Log transaction and charge
                $transaction = new Transaction([
                    'wallet_id'      => $wallet->id,
                    'user_id'        => $wallet->user_id,
                    'amount'         => $amount,
                    'net_amount'     => $amountAfterCharge,
                    'balance_before' => $initialBalance,
                    'post_balance'   => $wallet->balance,
                    'charge'         => $transactionCharge,
                    'type'           => $type,
                    'status'         => 'success',
                    'service_id'     => 0,
                    'reference'      => $transactionReference,
                    'service'        => $category,
                ]);
                $transaction->save();

                $this->logCharge($transactionReference, $wallet->id, $category . "_" . $type, $transactionCharge, $amountAfterCharge);

                // Dispatch notification
                $notificationService = new NotificationService();
                $notificationService->sendTransactionNotification($wallet->user, [
                    'user_name'   => $wallet->user->name,
                    'amount'      => $amount,
                    'type'        => $type,
                    'category'    => $category,
                    'status'      => 'success',
                    'new_balance' => $wallet->balance,
                    'reference'   => $transaction->reference,
                    'date'        => now()->toDateTimeString(),
                ]);

                return [
                    'success'     => true,
                    'message'     => ucfirst($type) . ' successful',
                    'transaction' => $transaction,
                    'new_balance' => $wallet->balance,
                ];
            });

            return $result;
        } catch (\Exception $e) {
            Log::error('Transaction error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during the transaction.',
            ];
        }
    }

    /**
     * Withdraw money from the user's wallet.
     * Optimized to check debit limits and queue notifications.
     */
    public function withdraw(User $user, float $amount): array
    {
        $wallet = $user->wallet;

        $debitCheck = $this->canDebit($wallet, $amount);
        if (! $debitCheck['can_transact']) {
            return [
                'success'       => false,
                'message'       => $debitCheck['message'],
                'limit_details' => $debitCheck,
            ];
        }

        return $this->updateBalance($user->id, $amount, 'debit', 'withdrawal');
    }

    /**
     * Transfer funds between wallets.
     * Includes additional checks and refactored for better readability.
     */
    public function transfer(int $senderId, int $recipientId, float $amount): array
    {
        if ($senderId === $recipientId) {
            return [
                'success' => false,
                'message' => 'Cannot transfer to yourself.',
            ];
        }

        $senderWallet    = Wallet::where('user_id', $senderId)->firstOrFail();
        $recipientWallet = Wallet::where('user_id', $recipientId)->firstOrFail();

        if ($senderWallet->isDebitLoggingDisabled()) {
            return [
                'success' => false,
                'message' => 'Debits are currently disabled for the sender\'s wallet.',
            ];
        }

        if ($recipientWallet->isCreditLoggingDisabled()) {
            return [
                'success' => false,
                'message' => 'Credits are currently disabled for the recipient\'s wallet.',
            ];
        }

        $debitCheck = $this->canDebit($senderWallet, $amount);
        if (! $debitCheck['can_transact']) {
            return [
                'success'       => false,
                'message'       => "Sender's " . $debitCheck['message'],
                'limit_details' => $debitCheck,
            ];
        }

        $creditCheck = $this->canCredit($recipientWallet, $amount);
        if (! $creditCheck['can_transact']) {
            return [
                'success'       => false,
                'message'       => "Recipient's " . $creditCheck['message'],
                'limit_details' => $creditCheck,
            ];
        }

        try {
            return DB::transaction(function () use ($senderWallet, $recipientWallet, $amount) {
                if ($senderWallet->balance < $amount) {
                    throw new InsufficientFundsException('Insufficient funds for the transfer.');
                }

                $senderWallet->balance -= $amount;
                $recipientWallet->balance += $amount;

                $senderWallet->save();
                $recipientWallet->save();

                $reference          = $this->generateReference();
                $senderReference    = 'trx_sen_' . $reference;
                $recipientReference = 'trx_rec_' . $reference;

                $this->logTransaction($senderWallet, $amount, 'debit', 'transfer', $senderReference);
                $this->logTransaction($recipientWallet, $amount, 'credit', 'transfer', $recipientReference);

                // Notify sender
                $notificationService = new NotificationService();
                $notificationService->sendTransactionNotification($senderWallet->user, [
                    'user_name'   => $senderWallet->user->name,
                    'amount'      => $amount,
                    'type'        => 'debit',
                    'category'    => 'transfer',
                    'status'      => 'success',
                    'new_balance' => $senderWallet->balance,
                    'reference'   => $senderReference,
                    'date'        => now()->toDateTimeString(),
                ]);

                // Notify recipient
                $notificationService->sendTransactionNotification($recipientWallet->user, [
                    'user_name'   => $recipientWallet->user->name,
                    'amount'      => $amount,
                    'type'        => 'credit',
                    'category'    => 'transfer',
                    'status'      => 'success',
                    'new_balance' => $recipientWallet->balance,
                    'reference'   => $recipientReference,
                    'date'        => now()->toDateTimeString(),
                ]);

                return [
                    'success' => true,
                    'message' => 'Transfer successful.',
                ];
            });
        } catch (\Exception $e) {
            Log::error('Transfer error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during the transfer.',
            ];
        }
    }

    /**
     * Log a transaction with the given details.
     */
    private function logTransaction(Wallet $wallet, float $amount, string $type, string $category, string $reference): void
    {
        Transaction::create([
            'wallet_id'      => $wallet->id,
            'amount'         => $amount,
            'balance_before' => $wallet->balance + ($type === 'debit' ? $amount : -$amount),
            'post_balance'   => $wallet->balance,
            'type'           => $type,
            'status'         => 'success',
            'reference'      => $reference,
            'category'       => $category,
            'charge'         => 0, // Add appropriate logic if charges are applied for transfers
        ]);
    }

    /**
     * Generate a unique reference for a transaction.
     * Optimized for simplicity and uniqueness.
     */
    private function generateReference(): string
    {
        return strtoupper(substr(sha1(uniqid('', true)), 0, 10));
    }

    /**
     * Retrieve the transaction charge configuration from cache or database.
     * Caching for performance improvement.
     */
    private function getTransactionChargeConfig(string $transactionType, float $amount, int $walletId = null): array
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
            $wallet = Wallet::find($walletId);
            if ($wallet && $wallet->customDepositCharge) {
                $chargeAmount  = $wallet->customDepositCharge->charge_amount ?? $chargeAmount;
                $chargePercent = $wallet->customDepositCharge->charge_percent ?? $chargePercent;
            }
            if ($wallet && $wallet->custom_wallet_charges) {
                $chargeAmount  = $wallet->custom_wallet_charges->charge_amount ?? $chargeAmount;
                $chargePercent = $wallet->custom_wallet_charges->charge_percent ?? $chargePercent;
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

    /**
     * Log transaction charges for analysis and audit.
     */
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

    /**
     * Check if a debit transaction is within the wallet's limits.
     */
    public function canDebit(Wallet $wallet, float $amount): array
    {
        $limits = $wallet->getEffectiveDebitLimits();

        return $this->checkLimits($wallet, $amount, 'debit', $limits);
    }

    /**
     * Check if a credit transaction is within the wallet's limits.
     */
    public function canCredit(Wallet $wallet, float $amount): array
    {
        $limits = $wallet->getEffectiveCreditLimits();

        return $this->checkLimits($wallet, $amount, 'credit', $limits);
    }

    /**
     * Generalized limit checker for transactions.
     */
    private function checkLimits(Wallet $wallet, float $amount, string $type, array $limits): array
    {
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

    public function checkTransactionCharges(User $user, float $amount, string $serviceType): array
    {
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'The amount must be greater than zero.',
            ];
        }

        // Get the transaction charge configuration
        $chargeConfig = $this->getTransactionChargeConfig($serviceType, $amount);

        // Prepare the response
        return [
            'success'           => true,
            'service_type'      => $serviceType,
            'charge_amount'     => $chargeConfig['charge_amount'],
            'charge_percent'    => $chargeConfig['charge_percent'],
            'calculated_charge' => $chargeConfig['calculated_charge'],
            'total_amount'      => $amount + $chargeConfig['calculated_charge'], // Total amount including charges
        ];
    }

}