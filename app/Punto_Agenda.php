<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Punto_Agenda extends Model
{
    protected $table = 'cnj_punto_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function acta()
    {
        return $this->belongsTo('App\Acta');
    }

}