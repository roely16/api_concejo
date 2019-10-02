<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Punto_Acta extends Model
{
    protected $table = 'cnj_bitacora_punto_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    protected $casts = [
        'fecha' => 'datetime:d/m/Y H:i:s',
    ];

    public function accion(){

        return $this->belongsTo('App\Accion', 'id_accion');

    }

    public function persona(){

        return $this->belongsTo('App\Persona', 'usuario');

    }

}
