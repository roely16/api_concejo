<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;

class rolController extends Controller
{
    
    public function obtenerRoles(){

        $roles = Rol::orderBy('id', 'asc')->get(['id as value', 'nombre as text']);

        return response()->json($roles);

    }

}
