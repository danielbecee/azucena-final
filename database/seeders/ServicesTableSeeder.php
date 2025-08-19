<?php
// database/seeders/ServicesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceItem;
use App\Models\ServiceCategory;

class ServicesTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener la categoría General existente o crear una por defecto
        $category = ServiceCategory::firstOrCreate(
            ['name' => 'General'],
            ['description' => 'Categoría general de servicios']
        );
        
        $categoryId = $category->id;
        
        $items = [
            ['name'=>'Acortar bajos','price'=>14.00],
            ['name'=>'Bordado','price'=>25.00],
            ['name'=>'Tintorería','price'=>12.00],
        ];

        foreach ($items as $it) {
            ServiceItem::updateOrCreate(
                ['name' => $it['name']],
                [
                    'service_category_id' => $categoryId,
                    'price'       => $it['price'],
                    'description' => "Servicio de {$it['name']}"
                ]
            );
        }
    }
}
