<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\EtapaProductiva;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Instructores extends Model

{



    protected $fillable = [ 'nombre_completo',  'correo','celular', 'aprendices'];

    public function etapaProductiva()
{
    return $this->hasMany(EtapaProductiva::class, 'instructores_id');
}

protected $withCount = ['etapaProductiva'];

}
