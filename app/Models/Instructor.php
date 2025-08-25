<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructores';

    protected $fillable = [
        'nombre_completo',
        'correo',
        'celular',
    ];

    public function etapaProductivas(): HasMany
    {
        return $this->hasMany(EtapaProductiva::class, 'instructores_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'instructor_id');
    }

    public function getAprendicesAsignadosCountAttribute(): int
    {
        return $this->etapaProductivas()->count();
    }
}