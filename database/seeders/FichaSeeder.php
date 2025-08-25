<?php

namespace Database\Seeders;

use App\Models\Ficha;
use Illuminate\Database\Seeder;

class FichaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fichas = [
            [
                'numero' => '2769515',
                'programa_formacion' => 'Tecnología en Análisis y Desarrollo de Software',

            ],
            [
                'numero' => '2769516',
                'programa_formacion' => 'Tecnología en Gestión de Redes de Datos',
                    
            ],
        ];

        foreach ($fichas as $ficha) {
            Ficha::firstOrCreate(
                ['numero' => $ficha['numero']],
                $ficha
            );
        }
    }
}
