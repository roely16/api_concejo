<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'cnj_persona';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function menus(){

        return $this->belongsToMany('App\Menu', 'cnj_acceso', 'id_persona', 'id_menu');

    }

    public function rol(){

        return $this->belongsTo('App\Rol', 'id_rol');

    }

    public function puesto(){

        return $this->belongsTo('App\Puesto', 'id_puesto');
    }

    public function usuario(){
        return $this->hasOne('App\Usuario', 'id_persona', 'id');
    }
}
