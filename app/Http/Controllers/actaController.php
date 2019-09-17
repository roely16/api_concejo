<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Tipo_Acta;
use App\Acta;

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
            $no_acta = $acta->numero_acta + 1;
        }

        $no_acta = [
            "numero" => $no_acta,
            "year" => $year
        ];

        // Obtener los tipos de acta
        $tipos_actas = Tipo_Acta::all();

        $data = [
            "numero_acta" => $no_acta,
            "tipos_actas" => $tipos_actas
        ];

        return response()->json($data);

    }

    public function registrarActa(Request $request){

        try {

            $acta = new Acta();
            $acta->id_tipo = $request->id_tipo;
            $acta->numero_acta = $request->no_agenda;
            $acta->year = $request->year;
            $acta->fecha = $request->fecha;

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

        $actas = Acta::orderBy('id', 'desc')->get();

        $data["items"] = $actas;
        $data["fields"] = [
            [
                "label" => "Acta No.",
                "key" => "no_acta",
                "sortable" => true
            ],
            [
                "label" => "Fecha",
                "key" => "fecha",
                "sortable" => true
            ],
            [
                "label" => "Tipo",
                "key" => "id_tipo",
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

        $acta = Acta::find($id);

        return response()->json($acta);

    }

    public function editarActa(Request $request){

        try {
            
            $id = $request->id;

            $acta = Acta::find($id);

            $acta->id_tipo = $request->id_tipo;
            $acta->numero_acta = $request->numero_acta;
            $acta->year = $request->year;
            $acta->fecha = $request->fecha;

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

}
