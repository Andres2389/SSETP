<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BitacoraUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'etapa_productiva_id',
        'numero_bitacora',
        'momento',
        'file_path',
        'file_name',
        'estado_revision',
        'observaciones_revision',
        'uploaded_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'numero_bitacora' => 'integer',
    ];

    public function etapaProductiva(): BelongsTo
    {
        return $this->belongsTo(EtapaProductiva::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeAttribute(): string
    {
        if (Storage::exists($this->file_path)) {
            $bytes = Storage::size($this->file_path);
            return $this->formatBytes($bytes);
        }
        return 'N/A';
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado_revision) {
            'pendiente' => 'warning',
            'aceptado' => 'success',
            'devuelto' => 'danger',
            default => 'gray',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado_revision) {
            'pendiente' => 'Pendiente',
            'aceptado' => 'Aceptado',
            'devuelto' => 'Devuelto',
            default => 'Desconocido',
        };
    }
}