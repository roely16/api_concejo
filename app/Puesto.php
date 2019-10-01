<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $table = 'cnj_puesto';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function persona(){

        return $this->hasMany('App\Persona', 'id_puesto', 'id');

    }
}
