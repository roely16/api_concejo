<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'cnj_documento';
    protected $primary_key = 'id';

    public $timestamps = false;

    // protected $casts = [
    //     'fecha_creacion' => 'datetime:d/m/Y H:i:s',
    // ];

    public function tipo_documento()
    {
        return $this->belongsTo('App\Tipo_Documento', 'id_tipo');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'subido_por');
    }
    
}
