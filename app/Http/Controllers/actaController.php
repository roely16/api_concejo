<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;

use App\Tipo_Acta;
use App\Agenda;
use App\Acta;
use App\Punto_Agenda;
use App\Punto_Acta;
use App\Bitacora_Punto_Acta;
use App\Bitacora_Agenda;

use DB;

class actaController extends Controller
{

    public function __construct()
    {
        // DB::setDateFormat('DD/MM/YYYY');
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
        // $agendas = Agenda::all('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'id_estado', 'asistencia_congelada', 'descripcion', 'eliminada');

        $agendas = Agenda::select('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'asistencia_congelada', 'descripcion', 'eliminada')->doesntHave('acta')->where('asistencia_congelada', 'S')->get();

        // Buscar solo las agendas con estado finalizado
        $agendas_finalizadas = [];

        foreach ($agendas as &$agenda) {
            
            $bitacora = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->first();
            
            if ($bitacora->id_estado == 5) {
                # code...
                $agenda->bitacora = $bitacora;
                $agendas_finalizadas [] = $agenda;
            }
            
        }

        $data = [
            "numero_acta" => $no_acta,
            "agendas" => $agendas_finalizadas
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

        $actas = Acta::where('eliminada', null)->orderBy('id', 'desc')->get();

        foreach ($actas as &$acta) {
            
            $acta->agenda->tipo_agenda;
        }

        $data["items"] = $actas;
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

        $agendas = Agenda::all('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'asistencia_congelada', 'descripcion', 'eliminada');

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
        $acta->agenda->estado;
        $acta->agenda->puntos_agenda;
        $acta->agenda->tipo_agenda;

        return response()->json($acta);

    }

    public function detallePuntoActa(Request $request){

        $id_acta = $request->id_acta;
        $id_punto_agenda = $request->id_punto_agenda;

        $data = [];

        // Punto de la agenda
        $punto_agenda = Punto_Agenda::find($id_punto_agenda);
        $punto_agenda->agenda->tipo_agenda;

        // Punto de acta
        $punto_acta = Punto_Acta::where('id_punto_agenda', $id_punto_agenda)->where('eliminado', null)->first();

        // Datos de la agenda

        $data["punto_agenda"] = $punto_agenda;
        $data["punto_acta"] = $punto_acta;

        return response()->json($data);

    }

    public function registroPuntoActa(Request $request){

        $punto_acta = new Punto_Acta();
        $punto_acta->id_acta = $request->id_acta;
        $punto_acta->id_punto_agenda = $request->id_punto_agenda;
        $punto_acta->descripcion = $request->descripcion;
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 1);

        return response()->json($punto_acta);

    }

    public function editarPuntoActa(Request $request){

        $punto_acta = Punto_Acta::find($request->id);
        $punto_acta->descripcion = $request->descripcion;
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 2, $request->original, $request->descripcion, '');

        return response()->json($punto_acta);

    }

    public function registrarBitacora($id_punto, $id_accion, $original = '', $edited = '', $motivo_eliminacion = ''){

        $bitacora_punto = new Bitacora_Punto_Acta();
        $bitacora_punto->id_punto = $id_punto;
        $bitacora_punto->id_accion = $id_accion;
        $bitacora_punto->original = $original;
        $bitacora_punto->modificado = $edited;
        $bitacora_punto->motivo_eliminacion = $motivo_eliminacion;
        $bitacora_punto->fecha = DB::raw('SYSDATE');
        $bitacora_punto->usuario = 1;

        $bitacora_punto->save();

    }

    public function bitacoraPunto($id){

        $bitacora_punto = Bitacora_Punto_Acta::where('id_punto', $id)->with('accion')->with('persona')->orderBy('id')->get();

        foreach ($bitacora_punto as &$item) {
            
            $item->_showDetails = false;
        }

        $fields = [
            [
                "key" => "fecha",
                "label" => "Fecha"
            ],
            [
                "key" => "accion",
                "label" => "Acción"
            ],
            [
                "key" => "persona",
                "label" => "Usuario"
            ],
            [
                "key" => "show_details",
                "label" => "Detalles"
            ]
            
        ];

        $data = [];
        $data["items"] = $bitacora_punto;
        $data["fields"] = $fields;

        return response()->json($data);

        // return response()->json($id);

    }

    public function eliminarPuntoActa(Request $request){

        $punto_acta = Punto_Acta::find($request->id_punto_acta);
        $punto_acta->eliminado = 'S';
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 3, $request->descripcion, '', $request->motivo_eliminacion);

        return response()->json($punto_acta);

    }

}
