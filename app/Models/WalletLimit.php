<?php
namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;

class WalletLimit extends Model
{
    protected $fillable = [
        'wallet_id',
        'maximum_balance',
        'credit_daily_limit',
        'credit_weekly_limit',
        'credit_monthly_limit',
        'debit_daily_limit',
        'debit_weekly_limit',
        'debit_monthly_limit',
    ];

    protected $casts = [
        'maximum_balance'      => 'decimal:2',
        'credit_daily_limit'   => 'decimal:2',
        'credit_weekly_limit'  => 'decimal:2',
        'credit_monthly_limit' => 'decimal:2',
        'debit_daily_limit'    => 'decimal:2',
        'debit_weekly_limit'   => 'decimal:2',
        'debit_monthly_limit'  => 'decimal:2',

    ];

    public function getCurrencyAttribute()
    {
        return Wallet::find($this->wallet_id)->currency->code;
    }

}