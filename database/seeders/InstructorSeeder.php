<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructores = [
            [
                'nombre_completo' => 'María González Pérez',
                'correo' => 'maria.gonzalez@sena.edu.co',
                'celular' => '3001234567',
            ],
            [
                'nombre_completo' => 'Carlos Rodríguez López',
                'correo' => 'carlos.rodriguez@sena.edu.co',
                'celular' => '3007654321',
            ],
        ];

        foreach ($instructores as $data) {
            $instructor = Instructor::firstOrCreate(
                ['correo' => $data['correo']],
                $data
            );

            // Crear usuario para el instructor si no existe
            $user = User::firstOrCreate(
                ['email' => $data['correo']],
                [
                    'name' => $data['nombre_completo'],
                    'password' => Hash::make('password'),
                    'instructor_id' => $instructor->id,
                    'tipo_usuario' => 'instructor',
                ]
            );
            $user->assignRole('instructor');

            // Actualizar el instructor_id en el usuario si ya existía
            if ($user->instructor_id !== $instructor->id) {
                $user->update(['instructor_id' => $instructor->id]);
            }
        }
    }
}