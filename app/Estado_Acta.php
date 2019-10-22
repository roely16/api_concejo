<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Estado_Acta extends Model
{
    protected $table = 'cnj_estado_acta';
    protected $primary_key = 'id';

    public $timestamps = false;
}
