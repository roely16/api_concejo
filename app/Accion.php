<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accion extends Model
{
    protected $table = 'cnj_accion';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function registros_bitacora(){

        return $this->hasMany('App\Bitacora_Punto');

    }
}
