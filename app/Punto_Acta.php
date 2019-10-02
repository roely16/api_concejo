<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Punto_Acta extends Model
{
    protected $table = 'cnj_punto_acta';
    protected $primary_key = 'id';

    public $timestamps = false;
}
