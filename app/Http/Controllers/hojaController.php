<?php

namespace App\Http\Controllers;

use App\Hoja_Contraloria;

use DB;

use Illuminate\Http\Request;

class hojaController extends Controller
{
    public function registroLote(Request $request){

        $hoja_contraloria = new Hoja_Contraloria();
        $hoja_contraloria->lote = $request->lote;
        $hoja_contraloria->inicia = $request->inicia;
        $hoja_contraloria->finaliza = $request->finaliza;
        $hoja_contraloria->observacion = $request->observacion;
        $hoja_contraloria->fecha = DB::raw('SYSDATE');
        $hoja_contraloria->registrado_por = $request->id_usuario;
        $hoja_contraloria->save();

        return response()->json($request);

    }

    public function obtenerLotes(){

        $data = [];

        $lotes = Hoja_Contraloria::select('id', 'lote', 'inicia', 'finaliza', 'observacion', DB::raw("to_char(fecha, 'dd/mm/yyyy hh24:mi:ss') as fecha"), 'registrado_por')->get();

        foreach ($lotes as &$lote) {
            
            $lote->persona->usuario;

        }

        $data["items"] = $lotes;

        $data["fields"] = [
            [
                "label" => "No. Lote",
                "key" => "lote",
                "sortable" => true
            ],
            [
                "label" => "Inicio",
                "key" => "inicia",
                "sortable" => true
            ],
            [
                "label" => "Fin",
                "key" => "finaliza",
                "sortable" => true
            ],
            [
                "label" => "Fecha",
                "key" => "fecha",
                "sortable" => true
            ],
            [
                "label" => "Registrado Por",
                "key" => "registrado_por",
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
}
