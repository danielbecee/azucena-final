<?php
// database/seeders/StatesTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;

class StatesTableSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            // pedidos
            ['type'=>'order',      'name'=>'Pendiente',       'description'=>'Pedido recibido, pendiente de taller'],
            ['type'=>'order',      'name'=>'En taller',       'description'=>'En proceso de arreglo'],
            ['type'=>'order',      'name'=>'Listo',           'description'=>'Listo para recoger'],

            // pagos
            ['type'=>'payment',    'name'=>'Sin pagar',       'description'=>'Debe abonar importe pendiente'],
            ['type'=>'payment',    'name'=>'Pagado parcial',  'description'=>'Ha abonado parte del importe'],
            ['type'=>'payment',    'name'=>'Pagado',          'description'=>'Importe completo abonado'],

            // tickets
            ['type'=>'ticket',     'name'=>'Pendiente print', 'description'=>'Debe imprimir el ticket'],
            ['type'=>'ticket',     'name'=>'Impreso',         'description'=>'Ticket ya impreso'],

            // asignaciones
            ['type'=>'assignment', 'name'=>'Asignado',        'description'=>'Tarea asignada a modista'],
            ['type'=>'assignment', 'name'=>'En progreso',     'description'=>'Taller trabajando en ello'],
            ['type'=>'assignment', 'name'=>'Completado',      'description'=>'Trabajo finalizado'],

            // facturación electrónica
            ['type'=>'e_invoice',  'name'=>'Pendiente envío','description'=>'Aún no enviado a AEAT'],
            ['type'=>'e_invoice',  'name'=>'Enviada',         'description'=>'Enviado a plataforma FACE'],
            ['type'=>'e_invoice',  'name'=>'Aceptada AEAT',   'description'=>'Factura aceptada por AEAT'],
            ['type'=>'e_invoice',  'name'=>'Rechazada',       'description'=>'Factura rechazada por AEAT'],
        ];

        foreach ($states as $s) {
            State::updateOrCreate(
                ['type' => $s['type'], 'name' => $s['name']],
                ['description' => $s['description']]
            );
        }
    }
}
