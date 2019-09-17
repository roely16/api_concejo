<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    
    protected $table = 'cnj_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function puntos_agenda()
    {
        return $this->hasMany('App\Punto_Agenda', 'id_acta')->orderBy('orden');
    }

}
