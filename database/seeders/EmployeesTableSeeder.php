<?php
// database/seeders/EmployeesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;

class EmployeesTableSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email','taller@example.com')->first();

        Employee::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'     => 'Ana Pérez',
                'position' => 'Modista',
            ]
        );
    }
}
