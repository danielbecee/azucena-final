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
                'name' => 'Arreglos pantalón',
                'description' => 'Arreglos específicos para pantalones'
            ],
            [
                'name' => 'Arreglos falda',
                'description' => 'Arreglos específicos para faldas'
            ],
            [
                'name' => 'Arreglos camisa',
                'description' => 'Arreglos específicos para camisas'
            ],
            [
                'name' => 'Arreglos vestido',
                'description' => 'Arreglos específicos para vestidos'
            ],
            [
                'name' => 'Arreglos abrigo',
                'description' => 'Arreglos específicos para abrigos y chaquetas'
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
                'name' => 'Personalización',
                'description' => 'Servicios de personalización de prendas'
            ],
            [
                'name' => 'Reparación',
                'description' => 'Servicios de reparación de prendas y tejidos'
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
