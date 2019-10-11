<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Agenda extends Model
{
    protected $table = 'cnj_bitacora_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function estado(){

        return $this->belongsTo('App\Estado_Agenda', 'id_estado');

    }

    public function persona(){

        return $this->belongsTo('App\Persona', 'id_usuario');

    }

    public function agenda(){
        return $this->belongsTo('App\Agenda', 'id_agenda');
    }

    public function historial_correos(){

        return $this->hasMany('App\Bitacora_Correo', 'id_bitacora', 'id')->orderBy('id_persona');

    }
}
