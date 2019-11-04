<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_Asistencia extends Model
{
    protected $table = 'cnj_tipo_asistencia';
    protected $primary_key = 'id';

    public $timestamps = false;
}
