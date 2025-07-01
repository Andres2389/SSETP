<?php

namespace App\Imports;

use App\Models\EtapaProductiva;
use App\Models\Fichas;
use App\Models\Instructores;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MyEtapaProductivaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $fichas = Fichas::all()->keyBy('numero');
        $instructores = Instructores::all();

        foreach ($rows as $row) {
            try {
                $ficha = $fichas->get($row['ficha']);
                $nombreInstructorImportado = $this->normalizarTexto($row['instructor_de_seguimiento']);
                $instructor = $instructores->first(function ($inst) use ($nombreInstructorImportado) {
                    return $this->normalizarTexto($inst->nombre_completo) === $nombreInstructorImportado;
                });

                // Validar campos obligatorios
                if (!$ficha || empty($row['numero_de_documento']) || empty($row['nombre'])) {
                    Log::warning('Fila omitida por datos incompletos: ' . json_encode($row));
                    continue;
                }

                // Buscar si ya existe un registro con el mismo número de documento
                $etapa = EtapaProductiva::where('numero_documento', $row['numero_de_documento'])->first();

                $datos = [
                    'fecha_inicio_ep'          => $this->parsearFecha($row['fecha_inicio_ep']),
                    'fecha_17_meses'           => $this->parsearFecha($row['fecha_17_meses']),
                    'fichas_id'                => $ficha->id,
                    'programa_formacion'       => $row['programa_de_formacion'],
                    'estado_ficha'             => $row['estado_de_la_ficha'],
                    'tipo_documento'           => $row['tipo_de_documento'],
                    'numero_documento'         => $row['numero_de_documento'],
                    'nombre'                   => $row['nombre'],
                    'apellidos'                => $row['apellidos'],
                    'celular'                  => $row['celular'],
                    'correo'                   => $row['correo'],
                    'estado_sofia'             => $row['estado_sofia'],
                    'instructores_id'          => $instructor?->id,
                    'fecha_asignacion'         => $this->parsearFecha($row['fecha_asignacion']),
                    'tipo_alternativa'         => $row['tipo_de_alternativa'],
                    'fecha_inicio_alternativa' => $this->parsearFecha($row['fecha_inicio_alternativa']),
                    'fecha_fin_alternativa'    => $this->parsearFecha($row['fecha_fin_alternativa']),
                    'fecha_corte'              => $this->parsearFecha($row['fecha_corte']),
                    'observaciones'            => $row['observaciones'],
                    'juicios_evaluativos'      => $row['juicios_evaluativos'],
                    'momentos'                 => $row['momento'],
                    'numero_bitacoras'         => $row['numero_de_bitacora'],
                    'paz_salvo'                => in_array(strtolower(trim($row['paz_y_salvo'] ?? '')), ['sí', 'si']),
                ];

                if ($etapa) {
                    $etapa->update($datos);
                } else {
                    EtapaProductiva::create($datos);
                }

            } catch (\Exception $e) {
                Log::error("Error al importar fila: " . json_encode($row) . " - " . $e->getMessage());
                continue;
            }
        }
    }

    private function parsearFecha($fecha)
    {
        if (!$fecha || trim($fecha) === '') {
            return null;
        }

        if (is_numeric($fecha)) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)
                )->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function normalizarTexto($texto)
    {
        $texto = mb_strtolower($texto);
        $texto = preg_replace('/\s+/', ' ', trim($texto));
        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i',
            'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
        ]);
        return $texto;
    }
}
