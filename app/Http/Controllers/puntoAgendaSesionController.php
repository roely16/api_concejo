<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Punto_Agenda_Sesion;
use App\Bitacora_Punto_Sesion;
use App\Punto_Agenda;

use DB;

class puntoAgendaSesionController extends Controller
{
    public function registrarBitacora($id_punto, $id_accion, $original = '', $edited = '', $motivo_eliminacion = '', $usuario){

        $bitacora_punto = new Bitacora_Punto_Sesion();
        $bitacora_punto->id_punto = $id_punto;
        $bitacora_punto->id_accion = $id_accion;
        $bitacora_punto->original = $original;
        $bitacora_punto->modificado = $edited;
        $bitacora_punto->motivo_eliminacion = $motivo_eliminacion;
        $bitacora_punto->fecha = DB::raw('SYSDATE');
        $bitacora_punto->usuario = $usuario;

        $bitacora_punto->save();

    }

    public function obtenerBitacora($id){

        $bitacora_punto = Bitacora_Punto_Sesion::where('id_punto', $id)->with('accion')->orderBy('id')->get();

        foreach ($bitacora_punto as &$item) {
            
            $item->persona->usuario;
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

    public function obtenerPuntos($id){

        $data = [];

        $puntos = Punto_Agenda_Sesion::where('id_agenda', $id)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        $puntos_eliminados = Punto_Agenda_Sesion::where('id_agenda', $id)->where('eliminado', '!=', null)->orderBy('orden', 'asc')->get();

        $data["puntos"] = $puntos;
        $data["puntos_eliminados"] = $puntos_eliminados;

        return response()->json($data);

    }

    public function copiarPuntos(Request $request){

        $puntos_agenda = Punto_Agenda::where('id_agenda', $request->id_agenda)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        // Copiar cada punto en la tabla de puntos de sesión
        foreach ($puntos_agenda as $punto_agenda) {
            
            $punto_agenda_s = new Punto_Agenda_Sesion();
            $punto_agenda_s->descripcion = $punto_agenda->descripcion;
            $punto_agenda_s->orden = $punto_agenda->orden;
            $punto_agenda_s->id_agenda = $punto_agenda->id_agenda;
            $punto_agenda_s->id_punto_agenda = $punto_agenda->id;
            $punto_agenda_s->save();

            $this->registrarBitacora($punto_agenda_s->id, 1, '', '', '', $request->id_usuario);

        }

        return response()->json($puntos_agenda);

    }

    public function editarPunto(Request $request){

        $punto_agenda = Punto_Agenda_Sesion::find($request->id);
        $punto_agenda->descripcion = $request->descripcion;
        $punto_agenda->save();

        // Registrar en la bitácora
        $this->registrarBitacora($punto_agenda->id, 2, $request->original, $request->descripcion, '', $request->id_persona);

        return response()->json($punto_agenda);

    }

    public function reordenarPuntos(Request $request){

        $puntos = $request->all();
        $i = 0;

        foreach ($puntos as $punto) {
            
            $i++;
            $punto_agenda = Punto_Agenda_Sesion::find($punto["id"]);
            $punto_agenda->orden = $i;
            $punto_agenda->save();

        }

        return response()->json(["code" => 200]);

    }

    public function eliminarPunto(Request $request){

        $punto_agenda = Punto_Agenda_Sesion::find($request->id_punto);
        $punto_agenda->eliminado = 'S';
        $punto_agenda->save();

        // Registrar en la bitácora
        $this->registrarBitacora($punto_agenda->id, 3, '', '', $request->motivo, $request->id_persona);

        return response()->json($punto_agenda);

    }
}
