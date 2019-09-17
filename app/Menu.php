<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'cnj_menu';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function personas()
    {
        return $this->belongsToMany('App\Persona', 'cnj_acceso', 'id_menu', 'id_persona');
    }
}
