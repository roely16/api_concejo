<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concejo extends Model
{
    protected $table = 'cnj_concejo';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function puesto(){

        return $this->belongsTo('App\Puesto', 'id_puesto');

    }

}
