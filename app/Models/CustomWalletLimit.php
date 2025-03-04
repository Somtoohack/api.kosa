<?php

namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomWalletLimit extends Model
{
    protected $fillable = [
        'wallet_id',
        'credit_daily_limit',
        'credit_weekly_limit',
        'credit_monthly_limit',
        'debit_daily_limit',
        'debit_weekly_limit',
        'debit_monthly_limit',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}