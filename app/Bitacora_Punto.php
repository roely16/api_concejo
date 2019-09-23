<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Punto extends Model
{
    protected $table = 'cnj_bitacora_punto';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function punto()
    {
        return $this->belongsTo('App\Punto');
    }
}
