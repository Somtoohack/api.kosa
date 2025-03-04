<?php
namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::create([
            'name'          => 'US Dollar',
            'code'          => 'USD',
            'symbol'        => '$',
            'country_code'  => 'USA',
            'reference'     => '4JU0VFITXPJ9PH3TKULNLYOWE',
            'country_name'  => 'United States of America',
            'flag'          => 'https://www.countryflags.io/US/flat/64.png',
            'dial_code'     => '+1',
            'exchange_rate' => 1.00,
            'is_active'     => true,
        ]);
        Currency::create([
            'name'          => 'Naira',
            'code'          => 'NGN',
            'symbol'        => '₦',
            'country_code'  => 'NGA',
            'reference'     => 'DPOFJBZSPL7HI661CCFW1S8QM',
            'country_name'  => 'Nigeria',
            'flag'          => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Flag_of_Nigeria.svg/2000px-Flag_of_Nigeria.svg.png',
            'dial_code'     => '+234',
            'exchange_rate' => 0.00067,
            'is_active'     => true,
        ]);
        Currency::create([
            'name'          => 'Pound Sterling',
            'code'          => 'GBP',
            'symbol'        => '£',
            'country_code'  => 'UK',
            'reference'     => 'RPMOVMQ8SJ9HRVAQI41VVO5DL',
            'country_name'  => 'United Kingdom',
            'flag'          => 'https://www.countryflags.io/GB/flat/64.png',
            'dial_code'     => '+44',
            'exchange_rate' => 0.79,
            'is_active'     => true,
        ]);

    }
}
