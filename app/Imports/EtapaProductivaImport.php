<?php

namespace App\Imports;

use App\Models\EtapaProductiva;
use App\Models\Ficha;
use App\Models\Instructor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class EtapaProductivaImport implements ToModel
{
    public function model(array $row)
    {
        // Saltar fila si no tiene número de documento
        if (blank($row[6] ?? null)) {
            return null;
        }

        // Buscar ficha por número
        $ficha = !empty($row[2])
            ? Ficha::where('numero', $row[2])->first()
            : null;

        // Buscar instructor por nombre completo
        $instructor = !empty($row[12])
            ? Instructor::where('nombre_completo', $row[12])->first()
            : null;

        return new EtapaProductiva([
            'fecha_inicio_ep'           => $this->parseDate($row[0] ?? null),
            'fecha_17_meses'            => $this->parseDate($row[1] ?? null),
            'fichas_id'                 => optional($ficha)->id,
            'programa_formacion'        => $row[3] ?? null,
            'estado_ficha'              => $row[4] ?? null,
            'tipo_documento'            => $row[5] ?? null,
            'numero_documento'          => $row[6] ?? null,
            'nombre'                    => $row[7] ?? null,
            'apellidos'                 => $row[8] ?? null,
            'celular'                   => $row[9] ?? null,
            'correo'                    => $row[10] ?? null,
            'estado_sofia'              => $this->parseEstadoSofia($row[11] ?? null),
            'instructores_id'           => optional($instructor)->id,
            'fecha_asignacion'          => $this->parseDate($row[13] ?? null),
            'tipo_alternativa'          => $row[14] ?? null,
            'fecha_inicio_alternativa'  => $this->parseDate($row[15] ?? null),
            'fecha_fin_alternativa'     => $this->parseDate($row[16] ?? null),
            'fecha_corte'               => $this->parseDate($row[17] ?? null),
            'observaciones'             => $row[18] ?? null,
            'juicios_evaluativos'       => $row[19] ?? null,
            'momentos'                  => $row[20] ?? null,
            'numero_bitacoras'          => $row[21] ?? null,
            'paz_salvo'                 => $this->toBool($row[22] ?? null),
        ]);
    }

    protected function parseDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
            }
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function toBool($value): bool
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['si', 'sí', '1', 'true', 'x', 'yes', 's'], true);
    }

    protected function parseEstadoSofia($value)
    {
        if ($value === null || trim($value) === '') {
            return null; // NULL si viene vacío
        }

        $value = strtolower(trim((string) $value));

        $validos = [
            'aplazado',
            'en_formacion',
            'por_certificar',
            'certificado',
            'cancelado',
            'trasladado',
            'condicionado'
        ];

        return in_array($value, $validos, true) ? $value : null;
    }
}
