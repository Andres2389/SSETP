<?php

namespace App\Exports;

use App\Models\EtapaProductiva;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EtapaProductivaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return EtapaProductiva::with(['ficha', 'instructor'])->get();
    }

    public function headings(): array
    {
        return [
            'ficha_numero',
            'programa_formacion',
            'instructor_nombre',
            'instructor_correo',
            'instructor_celular',
            'tipo_documento',
            'numero_documento',
            'nombre',
            'apellidos',
            'celular',
            'correo',
            'estado_sofia',
            'fecha_inicio_ep',
            'fecha_17_meses',
            'fecha_asignacion',
            'tipo_alternativa',
            'fecha_inicio_alternativa',
            'fecha_fin_alternativa',
            'fecha_corte',
            'observaciones',
            'juicios_evaluativos',
            'momentos',
            'numero_bitacoras',
            'paz_salvo'
        ];
    }

    public function map($etapaProductiva): array
    {
        return [
            $etapaProductiva->ficha->numero,
            $etapaProductiva->ficha->programa_formacion,
            $etapaProductiva->instructor->nombre_completo,
            $etapaProductiva->instructor->correo,
            $etapaProductiva->instructor->celular,
            $etapaProductiva->tipo_documento,
            $etapaProductiva->numero_documento,
            $etapaProductiva->nombre,
            $etapaProductiva->apellidos,
            $etapaProductiva->celular,
            $etapaProductiva->correo,
            $etapaProductiva->estado_sofia,
            $etapaProductiva->fecha_inicio_ep,
            $etapaProductiva->fecha_17_meses,
            $etapaProductiva->fecha_asignacion,
            $etapaProductiva->tipo_alternativa,
            $etapaProductiva->fecha_inicio_alternativa,
            $etapaProductiva->fecha_fin_alternativa,
            $etapaProductiva->fecha_corte,
            $etapaProductiva->observaciones,
            $etapaProductiva->juicios_evaluativos,
            $etapaProductiva->momentos,
            $etapaProductiva->numero_bitacoras,
            $etapaProductiva->paz_salvo ? 'Sí' : 'No'
        ];
    }
}