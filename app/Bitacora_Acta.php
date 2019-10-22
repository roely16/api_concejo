<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Acta extends Model
{
    protected $table = 'cnj_bitacora_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function estado(){

        return $this->belongsTo('App\Estado_Acta', 'id_estado');

    }

    public function persona(){

        return $this->belongsTo('App\Persona', 'id_usuario');

    }

    public function acta(){
        return $this->belongsTo('App\Acta', 'id_ata');
    }

    public function historial_correos(){

        return $this->hasMany('App\Bitacora_Correo', 'id_bitacora_acta', 'id')->orderBy('id_persona');

    }
}
