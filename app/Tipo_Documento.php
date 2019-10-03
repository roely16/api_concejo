<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_Documento extends Model
{
    protected $table = 'cnj_tipo_documento';
    protected $primary_key = 'id';

    public $timestamps = false;

    public function documentos()
    {
        return $this->hasMany('App\Documento');
    }
}
