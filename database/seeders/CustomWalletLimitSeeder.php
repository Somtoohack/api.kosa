<?php

namespace Database\Seeders;

use App\Models\CustomWalletLimit;
use Illuminate\Database\Seeder;

class CustomWalletLimitSeeder extends Seeder
{
    public function run()
    {
        // Sample data for CustomWalletLimit
        $walletLimits = [
            [
                'wallet_id' => 1,
                'credit_daily_limit' => 1000000.00,
                'credit_weekly_limit' => 10000000.00,
                'credit_monthly_limit' => 50000000.00,
                'debit_daily_limit' => 1000000.00,
                'debit_weekly_limit' => 10000000.00,
                'debit_monthly_limit' => 50000000.00,
            ],
            // Add more sample data as needed
        ];

        foreach ($walletLimits as $limit) {
            CustomWalletLimit::create($limit);
        }
    }
}