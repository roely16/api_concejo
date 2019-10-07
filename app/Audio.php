<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $table = 'cnj_audio';
    protected $primary_key = 'id';

    public $timestamps = false;

    protected $casts = [
        'fecha_creacion' => 'datetime:d/m/Y H:i:s',
    ];


    public function persona()
    {
        return $this->belongsTo('App\Persona', 'subido_por');
    }
}
