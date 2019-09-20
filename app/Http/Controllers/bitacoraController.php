<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class bitacoraController extends Controller
{
    
    public function bitacoraCorreo(){

        $bitacora = DB::table('cnj_bitacora_correo')->select('id_acta', 'id_persona', 'archivo', 'enviado', 'fecha_envio')->get();

        return response()->json($bitacora);

    }

}
