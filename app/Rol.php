<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'cnj_rol';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function personas(){

        return $this->hasMany('App\Persona', 'id_rol', 'id');

    }

}
