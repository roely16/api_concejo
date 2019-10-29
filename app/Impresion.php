<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Impresion extends Model
{
    protected $table = 'cnj_impresion';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function acta(){

        return $this->belongsTo('App\Acta', 'id_acta');

    }


}
