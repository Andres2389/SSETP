<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@ssetp.com'],
            [
                'name' => 'Administrador SSETP',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        // Crear usuario instructor de ejemplo
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@ssetp.com'],
            [
                'name' => 'Instructor de Ejemplo',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'instructor',
            ]
        );
        $instructor->assignRole('instructor');

        // Los usuarios aprendiz se crearán después de crear las etapas productivas
    }
}