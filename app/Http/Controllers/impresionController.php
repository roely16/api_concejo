<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Hoja_Contraloria;
use App\Historial_Impresion;
use App\Acta;
use App\Agenda;
use App\Persona;
use App\Punto_Acta;
use App\Impresion;
use App\Punto_Agenda_Sesion;

use DB;
use PDF;
use Storage;

class impresionController extends Controller
{
    public function obtenerImpresiones($id){

        $data = [];

        $impresiones = Impresion::where('id_acta', $id)->select('id', 'id_acta', 'archivo', DB::raw("to_char(fecha_creacion, 'dd/mm/yyyy hh24:mi:ss') as fecha_creacion"), DB::raw("to_char(fecha_impresion, 'dd/mm/yyyy hh24:mi:ss') as fecha_impresion"))->orderBy('id', 'desc')->get();

        $data["items"] = $impresiones;

        $data["fields"] = [
            [
                "label" => "Fecha de Creación",
                "key" => "fecha_creacion",
                "sortable" => true
            ],
            [
                "label" => "Fecha de Impresión",
                "key" => "fecha_impresion",
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

    public function lotesDisponibles(){

        $data = [];

        $lotes = Hoja_Contraloria::select('id', 'lote', 'inicia', 'finaliza', 'observacion', DB::raw("to_char(fecha, 'dd/mm/yyyy hh24:mi:ss') as fecha"), 'registrado_por')->get();

        foreach ($lotes as &$lote) {
            
            $historial = Historial_Impresion::where('id_lote', $lote->id)->orderBy('id', 'desc')->first();

            $lote->ultimo_registro = $historial;

            if ($historial) {

                $lote->text = "Lote No. " . $lote->lote . ' - Restantes ' . ($lote->finaliza - $historial->pagina);

            }else{

                $lote->text = "Lote No. " . $lote->lote . ' - Restantes ' .  ($lote->finaliza - $lote->inicia + 1);

            }

            $lote->value = $lote->id;   

            if ($historial) {
                
                $hojas_disponibles = $lote->finaliza - $historial->pagina;

                if ($hojas_disponibles > 0) {
                    
                    $data [] = $lote;

                }

            }else{

                $data [] = $lote;

            }

        }

        return response()->json($data);

    }

    public function registrarArchivo(Request $request){

        $acta = Acta::find($request->id_acta);
        $acta->puntos_acta;
        $acta->agenda->tipo_agenda;

        $acta->agenda->puntos_agenda = Punto_Agenda_Sesion::where('id_agenda', $acta->agenda->id)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        // Asistencia
        $personas = Persona::has('puesto')->orderBy('id_puesto')->with('puesto')->get();

        // Puntos del Acta
        $puntos_agenda = $acta->agenda->puntos_agenda;

        $ordinales = [
            "primero", "segundo", "tercero", "cuarto", "quinto", "sexto", "séptimo", "octavo", "noveno"
        ];

        $ordinales_centenas = [
            "décimo", "vigésimo", "trigésimo", "cuadragésimo", "quincuagésimo", "sexagésimo", "septuagésimo", "octogésimo", "nonagésimo"
        ];

        foreach ($puntos_agenda as &$punto_agenda) {

            $punto_agenda->punto_acta =  Punto_Acta::where('id_punto_agenda', $punto_agenda->id)->where('eliminado', null)->first();

            $cantidad_digitos = strlen((string)$punto_agenda->orden);

            if ($cantidad_digitos == 1) {
                
                $punto_agenda->ordinal = $ordinales[$punto_agenda->orden - 1];

            }elseif($cantidad_digitos == 2){

                $digits = (string)$punto_agenda->orden;
                $primero = $digits[0];
                $segundo = $digits[1];

                if ($segundo > 0) {
                   
                    $punto_agenda->ordinal = $ordinales_centenas[$primero - 1] . " " . $ordinales[$segundo - 1];

                }else{

                    $punto_agenda->ordinal = $ordinales_centenas[$primero - 1];

                }
               
            }

            $punto_agenda->cantidad_digitos = $cantidad_digitos;
            
            if ($punto_agenda->punto_acta) {

                $punto_agenda->punto_acta->texto = substr_replace( $punto_agenda->punto_acta->descripcion, "<strong>".strtoupper($punto_agenda->ordinal).": </strong>", 3, 0 );

            }
            
        }

        setlocale(LC_ALL, 'es_ES');
        $array_fecha = preg_split("#/#", $acta->agenda->fecha);
        $day = $array_fecha[0];
        $month = $array_fecha[1];
        $year = $array_fecha[2];

        $format_number = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        $str_no_acta = strtoupper($format_number->format($acta->no_acta));
        $str_fecha = strtoupper(strftime('%A', strtotime($year.'/'.$month.'/'.$day)) . ' ' . $format_number->format(intval($day)) . ' DE ' . strftime('%B', strtotime($year.'/'.$month.'/'.$day)) . ' DEL AÑO ' . $format_number->format($year));

        $data = [
            "str_no_acta" => $str_no_acta,
            "str_fecha" => $str_fecha,
            "acta" => $acta,
            "asistencia" => $personas,
            "puntos_acta" => $puntos_agenda
        ];

        $pdf = PDF::loadView('pdf.acta', $data);
        $pdf->setPaper('legal', 'portrait');

        $content = $pdf->download()->getOriginalContent();
        $unique_id_file = time();
        Storage::put('actas/'.$unique_id_file, $content);

        // Registro en la tabla de Impresiones
        $impresion = new Impresion();
        $impresion->id_acta = $request->id_acta;
        $impresion->archivo = $unique_id_file;
        $impresion->fecha_creacion = DB::raw('SYSDATE');
        $impresion->save();

        // Registrar cada pagina utilizada
        foreach ($request->historial as $historial) {
            
            $inicio = $historial["inicio"];
            $fin = $historial["fin"];

            $pag_inicio = $historial["pag_inicio"];

            for ($i=$inicio; $i <= $fin; $i++) { 
                
                // Registrar en el historial
                $historial_impresion = new Historial_Impresion();
                $historial_impresion->id_impresion = $impresion->id;
                $historial_impresion->id_lote = $historial["id_lote"];
                $historial_impresion->pagina = $i;
                $historial_impresion->pagina_documento = $pag_inicio;
                $historial_impresion->save();

                $pag_inicio++;

            }

        }

        return response()->json($request);

    }

    public function imprimirArchivo($id){

        $impresion = Impresion::find($id);

        //return response()->json($impresion);

        return Storage::get('actas/'.$impresion->archivo);

    }

    public function paginasImpresion($id){

        $data = [];

        $hojas_imprimir = Historial_Impresion::where('id_impresion', $id)->select('id', 'pagina', 'pagina_documento', 'estado', 'comentario_error')->orderBy('id', 'asc')->get();

        foreach ($hojas_imprimir as $hoja) {
            
            if ($hoja->estado == 1) {
                $hoja->printStatus = "OK";
            }elseif($hoja->estado == 2){
                $hoja->printStatus = "Error";
            }else{
                $hoja->printStatus = "pending";
            }

        }

        $impresion = Impresion::find($id);
        $impresion->acta;

        $data["impresion"] = $impresion;

        $data["items"] = $hojas_imprimir;

        $data["fields"] = [
            [
                "label" => "No. Hoja Contraloria",
                "key" => "pagina",
                "sortable" => true
            ],
            [
                "label" => "No. Página Documento",
                "key" => "pagina_documento",
                "sortable" => true
            ],
            [
                "label" => "Estado",
                "key" => "estado",
            ],
            [
                "label" => "Acciones",
                "key" => "actions",
                "class" => "text-right"
            ]
        ];

        return response()->json($data);

    }

    public function registrarImpresion(Request $request){

        $hojas = $request->hojas;
        $id_impresion = $request->id_impresion;

        foreach ($hojas as $hoja) {
            
            $registro_hoja = Historial_Impresion::find($hoja["id"]);
            $registro_hoja->estado = $hoja["estado"];
            $registro_hoja->fecha_impresion = DB::raw('SYSDATE');

            if ($hoja["comentario_error"]) {
                $registro_hoja->comentario_error = $hoja["comentario_error"];
            }

            $registro_hoja->save();

        }

        // Registrar que se imprimio el documento
        $impresion = Impresion::find($id_impresion);
        $impresion->fecha_impresion = DB::raw('SYSDATE');
        $impresion->save();

        return response()->json($hojas[0]);

    }
}
