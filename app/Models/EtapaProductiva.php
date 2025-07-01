<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Fichas;
use App\Models\Instructores;

class EtapaProductiva extends Model
{
    protected $fillable = [
        'numero_documento',
        'nombre',
        'apellidos',
        'celular',
        'correo',
        'tipo_alternativa',
        'tipo_documento',
        'estado_sofia',
        'fichas_id',
        'programa_formacion',
        'instructores_id',
        'numero_bitacoras',
        'fecha_asignacion',
        'fecha_inicio_ep',
        'estado_ficha',
        'fecha_inicio_alternativa',
        'fecha_fin_alternativa',
        'observaciones',
        'momentos',
        'paz_salvo',
        'fecha_corte',
        'fecha_17_meses',
        'juicios_evaluativos',
    ];

    protected $casts = [
        'momentos' => 'string',
        'numero_bitacoras' => 'string',
        'tipo_alternativa' => 'array',
        'tipo_documento' => 'array',
        'fecha_inicio_ep' => 'datetime',
        'fecha_corte' => 'datetime',
        'fecha_asignacion' => 'datetime',
        'fecha_fin_ep' => 'datetime',
        'fecha_17_meses' => 'datetime',
        'fecha_inicio_alternativa' => 'datetime',
        'fecha_fin_alternativa' => 'datetime'
    ];

    
    public function fichas()
    {
        return $this->belongsTo(Fichas::class, 'fichas_id');
    }

    public function instructores()
    {
        return $this->belongsTo(Instructores::class, 'instructores_id');
    }
}
