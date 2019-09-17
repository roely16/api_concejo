<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Persona;
use DB;

class usuarioController extends Controller
{
    public function login(Request $request){

        $usuario = $request->userName;
        $password = $request->userPass;

        $result = DB::table('cnj_usuario')->select('id', 'usuario', 'id_persona')->where([
            ['usuario', '=', $usuario],
            ['password', '=', $password]
        ])->get();

        if ($result->isEmpty()) {
        
            return response()->json(['code' => 100, 'message' => 'Usuario o contraseña incorrectos']);

        }

        $persona = Persona::find($result[0]->id_persona);

        $result[0]->persona = $persona;

        return response()->json(['code' => 200, 'data' => $result]);

    }

    public function createAccount(Request $request){

    }
}
