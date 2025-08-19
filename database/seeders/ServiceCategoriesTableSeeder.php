<?php
// database/seeders/ServiceCategoriesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCategory;

class ServiceCategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Corte y arreglo',
                'description' => 'Servicios de corte y arreglo de prendas'
            ],
            [
                'name' => 'Tintorería',
                'description' => 'Servicios de limpieza y cuidado de prendas'
            ],
            [
                'name' => 'Bordado',
                'description' => 'Servicios de bordado personalizado'
            ],
            [
                'name' => 'General',
                'description' => 'Otros servicios generales'
            ]
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
