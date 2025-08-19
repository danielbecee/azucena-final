<?php
// database/seeders/UsersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id','name');

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Admin Principal',
                'password' => Hash::make('secret'),
                'role_id'  => $roles['admin'],
            ]
        );

        // Empleado de taller
        User::updateOrCreate(
            ['email' => 'taller@example.com'],
            [
                'name'     => 'Modista Taller',
                'password' => Hash::make('secret'),
                'role_id'  => $roles['employee'],
            ]
        );

        // Cliente de prueba
        User::updateOrCreate(
            ['email' => 'cliente@example.com'],
            [
                'name'     => 'Cliente Demo',
                'password' => Hash::make('secret'),
                'role_id'  => $roles['customer'],
            ]
        );
    }
}
