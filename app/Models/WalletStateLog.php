<?php

namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletStateLog extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'state', 'reason', 'metadata', 'applied_at'];

    protected $casts = [
        'metadata' => 'array',
        'applied_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}