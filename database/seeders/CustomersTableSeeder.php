<?php
// database/seeders/CustomersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class CustomersTableSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email','cliente@example.com')->first();

        Customer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name'  => 'María',
                'last_name'   => 'López',
                'email'       => 'cliente@example.com',
                'phone'       => '600123456',
                'tax_id'      => 'X1234567L',
                'address'     => 'C/ Falsa 123',
                'city'        => 'Madrid',
                'province'    => 'Madrid',
                'postal_code' => '28001',
                'country'     => 'España',
            ]
        );
    }
}
