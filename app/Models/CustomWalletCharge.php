<?php
namespace App\Models;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Model;

class CustomWalletCharge extends Model
{
    //
    protected $fillable = [
        'wallet_id',
        'transaction_type',
        'charge_amount',
        'charge_currency',
        'charge_percent',
    ];

    protected $casts = [
        'charge_amount'  => 'decimal:4',
        'charge_percent' => 'decimal:4',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

}
