<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'cnj_usuario';
    protected $primary_key = 'id';

    public $timestamps = false;
}
