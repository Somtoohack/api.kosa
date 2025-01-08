<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionChargesLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_reference',
        'wallet_id',
        'transaction_type',
        'charge_amount',
        'profit_amount',
    ];
}