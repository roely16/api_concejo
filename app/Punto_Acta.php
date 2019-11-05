<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Punto_Acta extends Model
{
    protected $table = 'cnj_punto_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function acta(){

        return $this->belongsTo('App\Acta', 'id_acta');

    }

    public function punto_agenda()
    {
        return $this->belongsTo('App\Punto_Agenda_Sesion', 'id_punto_agenda');
    }

}
