<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Correo extends Model
{
    protected $table = 'cnj_bitacora_correo';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function persona(){

        return $this->belongsTo('App\Persona', 'id_persona');

    }
}
