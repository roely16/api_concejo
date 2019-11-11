<?php

namespace App\Http\Controllers;

use App\Hoja_Contraloria;
use App\Historial_Impresion;

use DB;
use Storage;

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

    public function detalleLote($id){

        $detalle_lote = Hoja_Contraloria::find($id, ['id', 'lote', 'inicia', 'finaliza', 'observacion', DB::raw("to_char(fecha, 'dd/mm/yyyy hh24:mi:ss') as fecha"), 'registrado_por']);
        $detalle_lote->persona->usuario;

        return response()->json($detalle_lote);

    }

    public function hojasUsadasLote($id){

        $data = [];

        $lote = Hoja_Contraloria::find($id);

        $hojas_usadas = Historial_Impresion::where('id_lote', $id)->select('id', 'id_impresion', 'id_lote', 'pagina', 'pagina_documento', 'estado', DB::raw("to_char(fecha_impresion, 'dd/mm/yyyy hh24:mi:ss') as fecha_impresion"), 'comentario_error')->orderBy('id', 'desc')->get();

        foreach ($hojas_usadas as &$hoja) {
            $hoja->impresion->acta;
        }

        // Calcúlo de restantes
        $cantidad_lote = $lote->finaliza - $lote->inicia + 1;
        $restantes = $cantidad_lote - count($hojas_usadas);

        $data["items"] = $hojas_usadas;

        $data["fields"] = [
            [
                "label" => "No. Hoja",
                "key" => "pagina",
                "sortable" => true
            ],
            [
                "label" => "No. Acta",
                "key" => "acta",
                "sortable" => true
            ],
            [
                "label" => "Hoja Acta",
                "key" => "pagina_documento",
                "sortable" => true
            ],
            [
                "label" => "Fecha de Impresión",
                "key" => "fecha_impresion",
                "sortable" => true
            ],
            [
                "label" => "Estado",
                "key" => "estado",
                "sortable" => true
            ],
            [
                "label" => "Acciones",
                "key" => "actions",
                "class" => "text-right"
            ]
        ];

        $data["lote"] = $lote;
        $data["restantes"] = $restantes;

        return response()->json($data);

    }

    public function vistaPrevia($documento){

        return Storage::get('actas/'.$documento);

        // return response()->json($documento);

    }
}
