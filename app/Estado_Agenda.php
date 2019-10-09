<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado_Agenda extends Model
{
    protected $table = 'cnj_estado_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

    // public function agendas(){

    //     return $this->hasMany('App\Agenda', 'id_tipo', 'id');

    // }
}
