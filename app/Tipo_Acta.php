<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tipo_Acta extends Model
{
    
    protected $table = 'cnj_tipo_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

}
