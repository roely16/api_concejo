<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historial_Impresion extends Model
{
    protected $table = 'cnj_historial_impresion';
    protected $primary_key = 'id';

    public $timestamps = false;
}
