<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;

use App\Tipo_Acta;
use App\Agenda;
use App\Acta;
use App\Punto_Agenda;

use DB;

class actaController extends Controller
{

    public function __construct()
    {
        DB::setDateFormat('DD/MM/YYYY');
    }
    
    public function datosModalCreacion(){

        // Obtener el la ultima acta para asignar el número correspondiente
        $year = date('Y');

        $acta = Acta::where('year', $year)->orderBy('id', 'desc')->first();

        // Si aún no existe un acta asignar el número 1
        if (!$acta) {
            $no_acta = 1;
        }else{
            $no_acta = $acta->no_acta + 1;
        }

        $no_acta = [
            "numero" => $no_acta,
            "year" => $year
        ];

        // Obtener agendas sin acta
        $agendas = Agenda::all();

        $data = [
            "numero_acta" => $no_acta,
            "agendas" => $agendas
        ];

        return response()->json($data);

    }

    public function registrarActa(Request $request){

        try {

            $acta = new Acta();
            $acta->id_agenda = $request->id_agenda;
            $acta->no_acta = $request->numero;
            $acta->year = $request->year;
            $acta->descripcion = $request->descripcion;

            $result = $acta->save();

            if (!$result) {
                return response()->json('Error al registrar');
            }

        } catch (\Exception $e) {
            
            $error = [
                "code" => 100,
                "message" => "Problema al registrar el acta.",
                "codigo_error" => $e->getCode()
            ];

            return response()->json($error);

        }
        
        return response()->json($request);

    }

    public function obtenerActas(){

        $data = [];

        $agendas = Acta::where('eliminada', null)->orderBy('id', 'desc')->get();

        $data["items"] = $agendas;
        $data["fields"] = [
            [
                "label" => "No.",
                "key" => "no_acta",
                "sortable" => true
            ],
            [
                "label" => "Año",
                "key" => "year",
                "sortable" => true
            ],
            [
                "label" => "Agenda",
                "key" => "agenda",
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

        return response()->json($data);

    }

    public function detalleActa($id){

        $data = [];

        $acta = Acta::find($id);
        $acta->agenda;

        $agendas = Agenda::all();

        $data["acta"] = $acta;
        $data["agendas"] = $agendas;

        return response()->json($data);

    }

    public function editarActa(Request $request){

        try {
            
            $id = $request->id;

            $acta = Acta::find($id);

            $acta->no_acta = $request->no_acta;
            $acta->year = $request->year;
            $acta->id_agenda = $request->id_agenda;
            $acta->descripcion = $request->descripcion;

            $acta->save();

        } catch (\Exception $e) {
            
            if ($e->getCode() == 1) {
                
                $message = "Ya existe un acta con el mismo número.";

            }else{
                $message = "Problema al editar el acta.";
            }

            $error = [
                "code" => 100,
                "message" => $message,
                "codigo_error" => $e->getCode()
            ];

            return response()->json($error);

        }

        return response()->json($request);

    }

    public function puntosAgenda($id){

        $acta = Acta::find($id);
        $acta->agenda->estado->puntos_agenda;
        $acta->agenda->puntos_agenda;

        return response()->json($acta);

    }

    public function detallePuntoActa(Request $request){

        $id_acta = $request->id_acta;
        $id_punto_agenda = $request->id_punto_agenda;

        $data = [];

        // Punto de la agenda
        $punto_agenda = Punto_Agenda::find($id_punto_agenda);

        $data["punto_agenda"] = $punto_agenda;

        return response()->json($data);

    }

}
