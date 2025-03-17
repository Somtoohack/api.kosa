<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionChargesConfig extends Model
{

    use HasFactory;

    protected $table = 'transaction_charges_config'; // Use the correct table name

    protected $fillable = ['transaction_type', 'charge_amount', 'charge_percent', 'currency', 'charge_cap'];

    protected $casts = [
        'charge_amount'  => 'decimal:2',
        'charge_percent' => 'decimal:2',
    ];

}