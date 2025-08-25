<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['codigo' => 'CC', 'descripcion' => 'Cédula de Ciudadanía'],
            ['codigo' => 'TI', 'descripcion' => 'Tarjeta de Identidad'],
            ['codigo' => 'CE', 'descripcion' => 'Cédula de Extranjería'],
        ];

        foreach ($tipos as $tipo) {
            TipoDocumento::firstOrCreate(
                ['codigo' => $tipo['codigo']],
                ['descripcion' => $tipo['descripcion']]
            );
        }
    }
}