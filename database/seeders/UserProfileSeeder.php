<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserProfile::create([
            'user_id' => 1, // Assuming user ID 1 exists
            'first_name' => 'Somtoo',
            'middle_name' => 'Promise',
            'last_name' => 'Akosa',
            'phone_number' => '+2348168431219',
            'device_key' => 'device_key_example',
            'device_name' => 'iPhone',
            'state' => 'California',
            'user_tag' => 'hack',
            'city' => 'Los Angeles',
            'lga' => 'LA County',
            'address' => '123 Main St',
            'date_of_birth' => '1999-01-01',
        ]);
    }
}