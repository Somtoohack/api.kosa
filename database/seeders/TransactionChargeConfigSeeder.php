<?php
namespace Database\Seeders;

use App\Models\TransactionChargesConfig;
use Illuminate\Database\Seeder;

class TransactionChargeConfigSeeder extends Seeder
{
    public function run()
    {

        TransactionChargesConfig::truncate();

        TransactionChargesConfig::insert([

            [
                'transaction_type' => 'deposit',
                'charge_amount'    => 1.5,   // Fixed charge
                'currency'         => 'USD', // Fixed charge
                'charge_percent'   => 1.5,   // Percentage charge (0%)
                'created_at'       => now(),
                'charge_cap'       => 50,
                'updated_at'       => now(),
            ],
            [
                'transaction_type' => 'withdrawal',
                'charge_amount'    => 2,     // Fixed charge
                'currency'         => 'USD', // Fixed charge
                'charge_percent'   => 1.8,   // Percentage charge (0%)
                'charge_cap'       => 50,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'transaction_type' => 'transfer',
                'charge_amount'    => 2,     // Fixed charge
                'currency'         => 'USD', // Fixed charge
                'charge_percent'   => 1.8,   // Percentage charge (0%)
                'charge_cap'       => 50,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'transaction_type' => 'deposit',
                'charge_amount'    => 50,    // Fixed charge
                'currency'         => 'NGN', // Fixed charge
                'charge_percent'   => 1.3,   // Percentage charge (1.3%)
                'charge_cap'       => 1500,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],

            [
                'transaction_type' => 'withdrawal',
                'charge_amount'    => 50.00, // Fixed charge
                'currency'         => 'NGN', // Fixed charge
                'charge_percent'   => 1.2,   // Percentage charge (1.2%)
                'charge_cap'       => 1500,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
            [
                'transaction_type' => 'transfer',
                'charge_amount'    => 5.00,  // Fixed charge
                'currency'         => 'NGN', // Fixed charge
                'charge_percent'   => 1.0,   // Percentage charge (1.0%)
                'charge_cap'       => 1500,
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);

    }
}