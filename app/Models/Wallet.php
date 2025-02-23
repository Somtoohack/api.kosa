<?php
namespace App\Models;

use App\Models\Currency;
use App\Models\CustomWalletLimit;
use App\Models\User;
use App\Models\VirtualBankAccount;
use App\Models\WalletStateLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'currency_id',
        'reference',
        'daily_limit',
        'monthly_limit',
        'maximum_balance',
    ];

    protected $casts = [
        'balance'         => 'decimal:4',
        'daily_limit'     => 'decimal:4',
        'monthly_limit'   => 'decimal:4',
        'maximum_balance' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stateLogs()
    {
        return $this->hasMany(WalletStateLog::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getTierLimits()
    {
        $tiers = [
            1 => ['daily_limit' => 50000, 'weekly_limit' => 200000, 'monthly_limit' => 500000],
            2 => ['daily_limit' => 200000, 'weekly_limit' => 1000000, 'monthly_limit' => 5000000],
            3 => ['daily_limit' => 1000000, 'weekly_limit' => 5000000, 'monthly_limit' => 20000000],
            4 => ['daily_limit' => 100000000, 'weekly_limit' => 200000000, 'monthly_limit' => 500000000],
        ];
        return $tiers[$this->tier] ?? [];
    }

    public function getTierMaxBalance()
    {
        $tiers = [
            1 => 500000,
            2 => 2000000,
            3 => 50000000,
            4 => 100000000,
        ];
        return $tiers[$this->tier] ?? 0;
    }

    public function customLimits()
    {
        return $this->hasOne(CustomWalletLimit::class);
    }

    public function virtualBankAccounts(): HasMany
    {
        return $this->hasMany(VirtualBankAccount::class);
    }

    public function getEffectiveLimits()
    {
                                                      // Retrieve the custom limits instance
        $customLimits = $this->customLimits->first(); // Call first() to get the actual CustomWalletLimit instance

        // Check if custom limits exist
        if ($customLimits && $customLimits->is_active) {
            return [
                'daily_limit'   => $customLimits->daily_limit,
                'weekly_limit'  => $customLimits->weekly_limit,
                'monthly_limit' => $customLimits->monthly_limit,
            ];
        }

        // If no custom limits, return tier limits
        $tierLimits = $this->getTierLimits();

        return [
            'daily_limit'   => $tierLimits['daily_limit'],
            'weekly_limit'  => $tierLimits['weekly_limit'],
            'monthly_limit' => $tierLimits['monthly_limit'],
        ];
    }

    public function getEffectiveDebitLimits(): array
    {
        $customLimits = $this->customLimits()->first();

        if ($customLimits && $customLimits->is_active) {
            return [
                'daily_limit'   => $customLimits->debit_daily_limit,
                'weekly_limit'  => $customLimits->debit_weekly_limit,
                'monthly_limit' => $customLimits->debit_monthly_limit,
            ];
        }

        // Use default tier-based limits if no custom limits are set
        $tierLimits = $this->getTierLimits();
        return [
            'daily_limit'   => $tierLimits['debit_daily_limit'] ?? $tierLimits['daily_limit'],
            'weekly_limit'  => $tierLimits['debit_weekly_limit'] ?? $tierLimits['weekly_limit'],
            'monthly_limit' => $tierLimits['debit_monthly_limit'] ?? $tierLimits['monthly_limit'],
        ];
    }

    public function getEffectiveCreditLimits(): array
    {
        $customLimits = $this->customLimits()->first();

        if ($customLimits && $customLimits->is_active) {
            return [
                'daily_limit'   => $customLimits->credit_daily_limit,
                'weekly_limit'  => $customLimits->credit_weekly_limit,
                'monthly_limit' => $customLimits->credit_monthly_limit,
            ];
        }

        // Use default tier-based limits if no custom limits are set
        $tierLimits = $this->getTierLimits();
        return [
            'daily_limit'   => $tierLimits['credit_daily_limit'] ?? $tierLimits['daily_limit'],
            'weekly_limit'  => $tierLimits['credit_weekly_limit'] ?? $tierLimits['weekly_limit'],
            'monthly_limit' => $tierLimits['credit_monthly_limit'] ?? $tierLimits['monthly_limit'],
        ];
    }

    public function isCreditLoggingDisabled(): bool
    {
        $latestCreditDisabledLog = $this->stateLogs()
            ->where('state', 'credit_disabled')
            ->latest('applied_at')
            ->first();

        $latestCreditEnabledLog = $this->stateLogs()
            ->where('state', 'credit_enabled')
            ->latest('applied_at')
            ->first();

        // If there is no "credit_enabled" log or the latest "credit_disabled" is newer
        return $latestCreditDisabledLog &&
            (! $latestCreditEnabledLog || $latestCreditDisabledLog->applied_at > $latestCreditEnabledLog->applied_at);
    }

    public function isDebitLoggingDisabled(): bool
    {
        $latestDebitDisabledLog = $this->stateLogs()
            ->where('state', 'debit_disabled')
            ->latest('applied_at')
            ->first();

        $latestDebitEnabledLog = $this->stateLogs()
            ->where('state', 'debit_enabled')
            ->latest('applied_at')
            ->first();

        // If there is no "debit_enabled" log or the latest "debit_disabled" is newer
        return $latestDebitDisabledLog &&
            (! $latestDebitEnabledLog || $latestDebitDisabledLog->applied_at > $latestDebitEnabledLog->applied_at);
    }

    public function disableDebit(string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'debit_disabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function enableDebit(string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'debit_enabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function disableCredit(string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'credit_disabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function enableCredit(string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'credit_enabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

}
