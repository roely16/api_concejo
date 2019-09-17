<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'cnj_persona';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function menus()
    {
        return $this->belongsToMany('App\Menu', 'cnj_acceso', 'id_persona', 'id_menu');
    }
}
