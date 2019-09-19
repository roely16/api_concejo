<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;
use App\Rol;

class personaController extends Controller
{
    
    public function obtenerPersonas(){

        $data = [];

        $personas = Persona::get();

        $data["items"] = $personas;
        $data["fields"] = [
            [
                "label" => "Nombre",
                "key" => "nombre",
                "sortable" => true
            ],
            [
                "label" => "Rol",
                "key" => "rol",
                "sortable" => true
            ],
            [
                "label" => "Email",
                "key" => "email",
                "sortable" => true
            ],
            [
                "label" => "Acciones",
                "key" => "actions",
                "class" => "text-right"
            ]
        ];

        return response()->json($data);

    }

    public function detallePersona($id){

        $data = [];

        $persona = Persona::find($id);

        $roles = Rol::orderBy('id', 'asc')->get(['id as value', 'nombre as text']);

        $data["persona"] = $persona;
        $data["roles"] = $roles;

        return response()->json($data);

    }

    public function editarPersona(Request $request){

        $persona = Persona::find($request->id);
        $persona->nombre = $request->nombre;
        $persona->apellido = $request->apellido;
        $persona->email = $request->email;
        $persona->save();

        return response()->json($persona);

    }

    public function permisosUsuario($id){

        $persona = Persona::find($id)->menus()->get();

        return response()->json($persona);

    }

}
