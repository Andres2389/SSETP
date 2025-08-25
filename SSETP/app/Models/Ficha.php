<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ficha extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'programa_formacion',

    ];

   

    public function etapaProductivas(): HasMany
    {
        return $this->hasMany(EtapaProductiva::class, 'fichas_id');
    }


}
