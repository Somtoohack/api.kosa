<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{

    protected $fillable = [
        'wallet_id',
        'virtual_bank_account_id',
        'provider_reference',
        'amount',
        'charge',
        'net_amount',
        'payload',
        'status',
        'sender_name',
        'sender_account_number',
        'sender_bank_name',
        'reference',
    ];

    protected $casts = [
        'metadata'   => 'array',
        'applied_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}