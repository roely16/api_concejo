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

        return $this->belongsTo('App\Agenda', 'id_agenda')->select('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'id_estado', 'asistencia_congelada', 'descripcion', 'eliminada');

    }

}
