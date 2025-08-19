<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            StatesTableSeeder::class,
            UsersTableSeeder::class,
            CustomersTableSeeder::class,
            EmployeesTableSeeder::class,
            ServiceCategoriesTableSeeder::class,
            ServicesTableSeeder::class,
        ]);
    }
}
