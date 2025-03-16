<?php
namespace App\Models;

use App\Models\Currency;
use App\Models\CustomWalletCharge;
use App\Models\User;
use App\Models\VirtualBankAccount;
use App\Models\WalletLimit;
use App\Models\WalletStateLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'key',
        'currency_id',
        'reference',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
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

    public function limits()
    {
        return $this->hasOne(WalletLimit::class);
    }

    public function customWalletCharges()
    {
        return $this->hasMany(CustomWalletCharge::class)->where('charge_currency', $this->currency->code);
    }

    public function virtualBankAccounts(): HasMany
    {
        return $this->hasMany(VirtualBankAccount::class);
    }

    public function getEffectiveCreditLimits()
    {
                                                // Retrieve the custom limits instance
        $customLimits = $this->limits->first(); // Call first() to get the actual CustomWalletLimit instance

        if ($customLimits) {
            return [

                'daily_limit'     => $customLimits->credit_daily_limit,
                'weekly_limit'    => $customLimits->credit_weekly_limit,
                'monthly_limit'   => $customLimits->credit_monthly_limit,
                'maximum_balance' => $customLimits->maximum_balance,
            ];
        }
        return null;

    }
    public function getEffectiveDebitLimits()
    {
                                                // Retrieve the custom limits instance
        $customLimits = $this->limits->first(); // Call first() to get the actual CustomWalletLimit instance

        if ($customLimits) {
            return [
                'daily_limit'     => $customLimits->debit_daily_limit,
                'weekly_limit'    => $customLimits->debit_weekly_limit,
                'monthly_limit'   => $customLimits->debit_monthly_limit,
                'maximum_balance' => $customLimits->maximum_balance,
            ];
        }
        return null;

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

    public function disableDebit(?string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'debit_disabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function enableDebit(?string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'debit_enabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function disableCredit(?string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'credit_disabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

    public function enableCredit(?string $reason = null, array $metadata = []): void
    {
        $this->stateLogs()->create([
            'state'      => 'credit_enabled',
            'reason'     => $reason,
            'metadata'   => $metadata,
            'applied_at' => now(),
        ]);
    }

}