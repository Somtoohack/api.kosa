<?php

namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'amount', 'type', 'reference', 'balance_before', 'post_balance', 'category', 'charge', 'status'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

}