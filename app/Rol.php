<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'cnj_rol';
    protected $primary_key = 'id';

    public $timestamps = false;
}
