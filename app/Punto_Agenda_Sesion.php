<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Punto_Agenda_Sesion extends Model
{
    protected $table = 'cnj_punto_agenda_sesion';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function agenda()
    {
        return $this->belongsTo('App\Agenda', 'id_agenda')->select('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'asistencia_congelada', 'descripcion', 'eliminada');
    }

    public function bitacora(){

        return $this->hasMany('App\Bitacora_Punto', 'id_punto');
        
    }

    public function punto_acta(){
        return $this->hasOne('App\Punto_Acta', 'id_punto_agenda', 'id');
    }
}
