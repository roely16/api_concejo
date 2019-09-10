<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'cnj_agenda';
    protected $primary_key = 'id';

    public $timestamps = false;

}
