<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Punto_Agenda;

class buscadorController extends Controller
{
    //
    public function buscarTexto(Request $request){

        $puntos_agenda = Punto_Agenda::where(DB::raw('UPPER(descripcion)'), 'like', '%'.strtoupper($request->busqueda).'%')->get();

        // Por cada resultado buscar el acta y la agenda
        foreach ($puntos_agenda as &$punto_agenda) {
            
            $punto_agenda->agenda;

        }

        return response()->json($puntos_agenda);

    }
}
