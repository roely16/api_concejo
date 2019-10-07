<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    
    protected $table = 'cnj_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

    // protected $casts = [
    //     'fecha' => 'datetime:d/m/Y',
    // ];

    public function puntos_agenda()
    {
        return $this->hasMany('App\Punto_Agenda', 'id_acta')->orderBy('orden');
    }

    public function tipo_agenda(){

        return $this->belongsTo('App\Tipo_Acta', 'id_tipo');

    }

    public function estado(){

        return $this->belongsTo('App\Estado_Agenda', 'id_estado');

    }

    public function acta(){

        return $this->hasOne('App\Acta', 'id_agenda', 'id');

    }

}
