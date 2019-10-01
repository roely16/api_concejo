<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Punto extends Model
{
    protected $table = 'cnj_bitacora_punto';
    protected $primary_key = 'id';

    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime:d/m/Y H:i:s',
    ];

    public function punto()
    {
        return $this->belongsTo('App\Punto', 'id_punto');
    }

    public function accion(){

        return $this->belongsTo('App\Accion', 'id_accion');

    }

    public function persona(){

        return $this->belongsTo('App\Persona', 'usuario');

    }
}
