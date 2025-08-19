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
        // Definir servicios por categorías
        $services = [
            'Arreglos pantalón' => [
                ['name' => 'Acortar bajos pantalón', 'price' => 14.00, 'description' => 'Acortar el largo del pantalón'],
                ['name' => 'Arreglar cintura pantalón', 'price' => 16.00, 'description' => 'Ajustar la cintura del pantalón'],
                ['name' => 'Cambiar cremallera pantalón', 'price' => 12.00, 'description' => 'Sustituir cremallera rota o dañada'],
                ['name' => 'Estrechar piernas pantalón', 'price' => 18.00, 'description' => 'Reducir el ancho de las piernas del pantalón'],
                ['name' => 'Arreglo personalizado pantalón', 'price' => 0.00, 'description' => 'Arreglo a medida según necesidades del cliente']
            ],
            
            'Arreglos falda' => [
                ['name' => 'Acortar falda', 'price' => 12.00, 'description' => 'Acortar el largo de la falda'],
                ['name' => 'Ajustar cintura falda', 'price' => 14.00, 'description' => 'Modificar la cintura de la falda'],
                ['name' => 'Cambiar forro falda', 'price' => 20.00, 'description' => 'Reemplazar el forro interior de la falda'],
                ['name' => 'Arreglo personalizado falda', 'price' => 0.00, 'description' => 'Arreglo a medida según necesidades del cliente']
            ],
            
            'Arreglos camisa' => [
                ['name' => 'Acortar mangas camisa', 'price' => 10.00, 'description' => 'Acortar las mangas de la camisa'],
                ['name' => 'Ajustar costados camisa', 'price' => 15.00, 'description' => 'Estrechar o ajustar el cuerpo de la camisa'],
                ['name' => 'Cambiar cuello camisa', 'price' => 18.00, 'description' => 'Sustituir el cuello desgastado'],
                ['name' => 'Arreglo personalizado camisa', 'price' => 0.00, 'description' => 'Arreglo a medida según necesidades del cliente']
            ],
            
            'Arreglos vestido' => [
                ['name' => 'Acortar largo vestido', 'price' => 18.00, 'description' => 'Modificar el largo del vestido'],
                ['name' => 'Ajustar costados vestido', 'price' => 20.00, 'description' => 'Estrechar o ajustar el cuerpo del vestido'],
                ['name' => 'Arreglo personalizado vestido', 'price' => 0.00, 'description' => 'Arreglo a medida según necesidades del cliente']
            ],
            
            'Arreglos abrigo' => [
                ['name' => 'Acortar mangas abrigo', 'price' => 16.00, 'description' => 'Acortar las mangas del abrigo'],
                ['name' => 'Cambiar forro abrigo', 'price' => 45.00, 'description' => 'Sustituir el forro interior completo'],
                ['name' => 'Arreglo personalizado abrigo', 'price' => 0.00, 'description' => 'Arreglo a medida según necesidades del cliente']
            ],
            
            'Tintorería' => [
                ['name' => 'Limpieza en seco', 'price' => 12.00, 'description' => 'Limpieza profesional en seco'],
                ['name' => 'Lavado especial', 'price' => 15.00, 'description' => 'Lavado especial para prendas delicadas'],
                ['name' => 'Desmanchado', 'price' => 8.00, 'description' => 'Tratamiento para eliminar manchas difíciles'],
                ['name' => 'Servicio personalizado tintorería', 'price' => 0.00, 'description' => 'Servicio a medida según necesidades del cliente']
            ],
            
            'Bordado' => [
                ['name' => 'Bordado pequeño', 'price' => 15.00, 'description' => 'Bordado de pequeño tamaño'],
                ['name' => 'Bordado mediano', 'price' => 25.00, 'description' => 'Bordado de tamaño medio'],
                ['name' => 'Bordado grande', 'price' => 40.00, 'description' => 'Bordado de gran tamaño o complejidad'],
                ['name' => 'Bordado personalizado', 'price' => 0.00, 'description' => 'Bordado personalizado según diseño del cliente']
            ],
            
            'Personalización' => [
                ['name' => 'Aplicación de parches', 'price' => 8.00, 'description' => 'Colocar parches decorativos o funcionales'],
                ['name' => 'Personalización con apliques', 'price' => 12.00, 'description' => 'Añadir apliques decorativos'],
                ['name' => 'Personalización completa', 'price' => 0.00, 'description' => 'Servicio completo de personalización a medida']
            ],
            
            'Reparación' => [
                ['name' => 'Arreglo de descosidos', 'price' => 6.00, 'description' => 'Reparación de costuras abiertas'],
                ['name' => 'Reparación de rotos', 'price' => 10.00, 'description' => 'Arreglo de tejidos rasgados o rotos'],
                ['name' => 'Cambio de cremalleras', 'price' => 15.00, 'description' => 'Sustitución de cremalleras en cualquier prenda'],
                ['name' => 'Reparación compleja', 'price' => 0.00, 'description' => 'Reparación personalizada de alta dificultad']
            ],
            
            'General' => [
                ['name' => 'Presupuesto personalizado', 'price' => 0.00, 'description' => 'Evaluación y presupuesto para servicios no catalogados'],
                ['name' => 'Servicio urgente', 'price' => 10.00, 'description' => 'Recargo por servicio prioritario (24-48h)'],
                ['name' => 'Recogida a domicilio', 'price' => 5.00, 'description' => 'Servicio de recogida de prendas a domicilio']
            ]
        ];

        // Crear los servicios
        foreach ($services as $categoryName => $items) {
            // Obtener el ID de la categoría
            $category = ServiceCategory::where('name', $categoryName)->first();
            
            if ($category) {
                foreach ($items as $item) {
                    ServiceItem::updateOrCreate(
                        ['name' => $item['name']],
                        [
                            'service_category_id' => $category->id,
                            'price' => $item['price'],
                            'description' => $item['description']
                        ]
                    );
                }
            }
        }
    }
}
