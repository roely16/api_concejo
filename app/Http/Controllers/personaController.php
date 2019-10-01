<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;
use App\Rol;

class personaController extends Controller
{
    
    public function obtenerPersonas(){

        $data = [];

        $personas = Persona::with('rol')->get();

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
                "label" => "Puesto",
                "key" => "puesto",
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

    public function registrarPersona(Request $request){

        try {
           
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->apellido = $request->apellido;
            $persona->email = $request->email;
            $persona->id_rol = $request->rol;

            $persona->save();

        } catch (\Exception $e) {
           
            if ($e->getCode() == 1) {
                
                $message = "Ya existe una persona con este correo";

            }else{

                $message = "Problema al registrar a la persona";

            }

            $error = [
                "code" => 100,
                "message" => $message,
                "errorCode" => $e->getCode() 
            ];

            return response()->json($error);

        }

        return response()->json(["code" => 200, "data" => $persona]);

    }

    public function personasCorreo(){

        $personas = Persona::where('id_rol', '!=', null)->with('rol')->get();

        foreach ($personas as $persona) {
        
            $persona->enviar_correo = true;

        }

        return response()->json($personas);

    }

}
