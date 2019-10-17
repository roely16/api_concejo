<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Acta extends Model
{
    protected $table = 'cnj_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function agenda(){

        return $this->belongsTo('App\Agenda', 'id_agenda')->select('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'asistencia_congelada', 'descripcion', 'eliminada');

    }

    public function puntos_acta(){

        return $this->hasMany('App\Punto_Acta', 'id_acta', 'id');

    }

    public function bitacora(){

        return $this->hasMany('App\Bitacora_Acta', 'id_acta');

    }

}
