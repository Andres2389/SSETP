<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EtapaProductiva extends Model
{
    use HasFactory;

    protected $fillable = [
        'fichas_id',
        'instructores_id',
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
        'paz_salvo',
    ];

    protected $casts = [
        'fecha_inicio_ep' => 'date',
        'fecha_17_meses' => 'date',
        'fecha_asignacion' => 'date',
        'fecha_inicio_alternativa' => 'date',
        'fecha_fin_alternativa' => 'date',
        'fecha_corte' => 'date',
        'paz_salvo' => 'boolean',
    ];

    public function ficha(): BelongsTo
    {
        return $this->belongsTo(Ficha::class, 'fichas_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructores_id');
    }

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento', 'codigo');
    }

    public function bitacoraUploads(): HasMany
    {
        return $this->hasMany(BitacoraUpload::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'etapa_productiva_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellidos);
    }

    public function getBitacorasSubidasCountAttribute(): int
    {
        return $this->bitacoraUploads()->count();
    }

    public function getBitacorasAceptadasCountAttribute(): int
    {
        return $this->bitacoraUploads()->where('estado_revision', 'aceptado')->count();
    }

    public function getBitacorasPendientesCountAttribute(): int
    {
        return $this->bitacoraUploads()->where('estado_revision', 'pendiente')->count();
    }

    public function getBitacorasDevueltasCountAttribute(): int
    {
        return $this->bitacoraUploads()->where('estado_revision', 'devuelto')->count();
    }
}