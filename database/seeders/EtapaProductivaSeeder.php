<?php

namespace Database\Seeders;

use App\Models\EtapaProductiva;
use App\Models\Ficha;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtapaProductivaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ficha1 = Ficha::where('numero', '2769515')->first();
        $ficha2 = Ficha::where('numero', '2769516')->first();
        $instructor1 = Instructor::where('correo', 'maria.gonzalez@sena.edu.co')->first();
        $instructor2 = Instructor::where('correo', 'carlos.rodriguez@sena.edu.co')->first();

        $aprendices = [
            [
                'fichas_id' => $ficha1->id,
                'instructores_id' => $instructor1->id,
                'tipo_documento' => 'CC',
                'numero_documento' => '1234567890',
                'nombre' => 'Juan Carlos',
                'apellidos' => 'Pérez Gómez',
                'celular' => '3101234567',
                'correo' => 'juan.perez@gmail.com',
                'estado_sofia' => 'en_formacion',
                'fecha_inicio_ep' => '2024-01-15',
                'momentos' => '1',
            ],
            [
                'fichas_id' => $ficha1->id,
                'instructores_id' => $instructor1->id,
                'tipo_documento' => 'CC',
                'numero_documento' => '0987654321',
                'nombre' => 'Ana María',
                'apellidos' => 'López Rodríguez',
                'celular' => '3207654321',
                'correo' => 'ana.lopez@gmail.com',
                'estado_sofia' => 'en_formacion',
                'fecha_inicio_ep' => '2024-01-15',
                'momentos' => '1',
            ],
            [
                'fichas_id' => $ficha2->id,
                'instructores_id' => $instructor2->id,
                'tipo_documento' => 'CC',
                'numero_documento' => '1122334455',
                'nombre' => 'Luis Fernando',
                'apellidos' => 'Martínez Silva',
                'celular' => '3151122334',
                'correo' => 'luis.martinez@gmail.com',
                'estado_sofia' => 'en_formacion',
                'fecha_inicio_ep' => '2024-01-20',
                'momentos' => '1',
            ],
        ];

        foreach ($aprendices as $data) {
            $etapaProductiva = EtapaProductiva::firstOrCreate(
                [
                    'fichas_id' => $data['fichas_id'],
                    'numero_documento' => $data['numero_documento']
                ],
                $data
            );

            // Crear usuario para el aprendiz
            $user = User::firstOrCreate(
                ['email' => $data['correo']],
                [
                    'name' => trim($data['nombre'] . ' ' . $data['apellidos']),
                    'password' => Hash::make('password'),
                    'etapa_productiva_id' => $etapaProductiva->id,
                    'tipo_usuario' => 'aprendiz',
                ]
            );
            $user->assignRole('aprendiz');

            // Actualizar el etapa_productiva_id en el usuario si ya existía
            if ($user->etapa_productiva_id !== $etapaProductiva->id) {
                $user->update(['etapa_productiva_id' => $etapaProductiva->id]);
            }
        }
    }
}
