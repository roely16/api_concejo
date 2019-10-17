<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bitacora_Acta extends Model
{
    protected $table = 'cnj_bitacora_acta';
    protected $primary_key = 'id';

    public $timestamps = false;
}
