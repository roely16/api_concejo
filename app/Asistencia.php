<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{

    protected $table = 'cnj_asistencia';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function tipo(){

        return $this->belongsTo('App\Tipo_Asistencia', 'id_tipo');

    }

    public function persona_registra(){

        return $this->belongsTo('App\Persona', 'registrado_por');

    }

}
