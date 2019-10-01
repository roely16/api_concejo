<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Acta extends Model
{
    protected $table = 'cnj_acta';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function agenda(){

        return $this->belongsTo('App\Agenda', 'id_agenda');

    }

}
