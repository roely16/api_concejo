<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Punto_Agenda extends Model
{
    protected $table = 'cnj_punto_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function agenda()
    {
        return $this->belongsTo('App\Agenda', 'id_acta')->select('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'id_estado', 'asistencia_congelada', 'descripcion', 'eliminada');
    }

    public function bitacora(){

        return $this->hasMany('App\Bitacora_Punto', 'id_punto');
        
    }

}
