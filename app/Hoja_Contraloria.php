<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hoja_Contraloria extends Model
{
    protected $table = 'cnj_hoja_contraloria';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'registrado_por');
    }

    public function historial(){

        return $this->hasMany('App\Historial_Impresion', 'id_lote', 'id');

    }

}
