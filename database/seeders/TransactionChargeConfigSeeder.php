<?php

namespace Database\Seeders;

use App\Models\TransactionChargesConfig;
use Illuminate\Database\Seeder;

class TransactionChargeConfigSeeder extends Seeder
{
    public function run()
    {
        TransactionChargesConfig::create([
            'transaction_type' => 'deposit',
            'charge_amount' => 50, // Fixed charge
            'charge_percent' => 0, // Percentage charge (0%)
        ]);

        TransactionChargesConfig::create([
            'transaction_type' => 'withdrawal',
            'charge_amount' => 50.00, // Fixed charge
            'charge_percent' => 1.2, // Percentage charge (1.2%)
        ]);

        TransactionChargesConfig::create([
            'transaction_type' => 'transfer',
            'charge_amount' => 5.00, // Fixed charge
            'charge_percent' => 1.0, // Percentage charge (1.0%)
        ]);
    }
}