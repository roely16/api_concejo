<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Punto_Agenda_Sesion extends Model
{
    protected $table = 'cnj_punto_agenda_sesion';
    protected $primary_key = 'id';

    public $timestamps = false;
}
