<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletLimit extends Model
{
    protected $fillable = [
        'wallet_id',
        'daily_limit',
        'weekly_limit',
        'monthly_limit',
    ];
}