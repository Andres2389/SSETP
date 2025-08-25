<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descripcion',
    ];

    public function etapaProductivas()
    {
        return $this->hasMany(EtapaProductiva::class, 'tipo_documento', 'codigo');
    }
}