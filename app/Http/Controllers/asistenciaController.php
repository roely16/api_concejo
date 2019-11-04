<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;
use App\Puesto;
use App\Agenda;
use App\Asistencia;
use App\Concejo;
use DB;

class asistenciaController extends Controller
{
    
    public function listaAsistencia($id){

        $data = [];

        $personas = Concejo::all();

        foreach ($personas as &$persona) {
            
            $persona->puesto;

            // Por cada persona buscar registro en la asistencia
            $asistencia = Asistencia::where('id_persona', $persona->id)->where('id_agenda', $id)->orderBy('id', 'desc')->first();

            if ($asistencia) {
                
                $persona->asistencia = $asistencia;

                $persona->asistencia->tipo;
                
            }

        }

        $asistencia = new Asistencia();

        // foreach ($personas as &$persona) {
            
        //     $result = $asistencia->where('id_agenda', $id)->where('id_persona', $persona->persona[0]->id)->get(['id_agenda', 'id_persona', 'hora', 'motivo']);

        //     if (!$result->isEmpty()) {

        //         // Si tiene registro en asistencia validar si es llegada tarde, ausencia o asistencia normal

        //         $persona->asistencia = true;
        //         $persona->hora = $result[0]->hora;
        //         $persona->motivo_falta = $result[0]->motivo;

        //         if ($persona->hora != null) {
                    
        //             // Llego tarde
        //             $persona->color = 'warning';

        //         }elseif($persona->hora == null && $persona->motivo_falta != null){

        //             // No se presento
        //             $persona->color = 'danger';

        //         }elseif($persona->hora == null && $persona->motivo_falta == null){

        //             // Se presento a tiempo
        //             $persona->color = 'success';
        //         }
                
        //     }else{
        //         $persona->asistencia = false;
        //         $persona->color = 'secondary';
        //     }

        // }

        $agenda = Agenda::find($id, ['id', 'id_tipo', 'asistencia_congelada', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha")]);
        $agenda->tipo_agenda;

        $data["detalle_agenda"] = $agenda;
        $data["personas"] = $personas;
        
        return response()->json($data);

    }

    public function registrarAsistencia(Request $request){

        $asistencia = new Asistencia();
        $asistencia->id_persona = $request->id_persona;
        $asistencia->id_agenda = $request->id_agenda;
        $asistencia->id_tipo = $request->tipo;
        $asistencia->hora = $request->hora;
        $asistencia->motivo = $request->motivo;
        $asistencia->fecha_registro = DB::raw('SYSDATE');
        $asistencia->registrado_por = $request->id_usuario;
        $asistencia->save();

        return response()->json($asistencia);

    }

    public function eliminarAsistencia(Request $request){

        $result = DB::table('cnj_asistencia')->where('id_persona', '=', $request->id_persona)->where('id_agenda', '=', $request->id_agenda)->delete();

        return response()->json($result);

    }

    public function registrarAsistenciaEspecial(Request $request){

        $tipo = $request->tipo;
        $id_persona = $request->id_persona;
        $id_agenda = $request->id_agenda;
        $motivo = $request->motivo;

        if ($tipo == 1) {
            
            // Registro de llegada tarde
            $hora = $request->hora;
            
            $txt_hora = $hora["HH"] . ':' . $hora["mm"];

            // Si ya esta marcado como asistente pero se desea agregar la hora de llegada tarde
            if ($request->asistencia == true) {
                


            }else{

                $result = DB::table('cnj_asistencia')->insert(
                    ['id_persona' => $request->id_persona, 'id_agenda' => $request->id_agenda, 'hora' => DB::raw("TO_DATE('21/09/2019 $txt_hora', 'DD/MM/YYYY HH24:MI')"), 'motivo_falta' => $motivo]
                );

            }

        }else{

            // Se registra la ausencia y el motivo
            $result = DB::table('cnj_asistencia')->insert(
                ['id_persona' => $request->id_persona, 'id_agenda' => $request->id_agenda, 'motivo_falta' => $motivo]
            );

        }

        return response()->json($request);

    }

    public function congelarAsistencia(Request $request){

        $id_agenda = $request->id_agenda;

        $agenda = Agenda::find($id_agenda);
        $agenda->asistencia_congelada = 'S';
        $agenda->save();

        return response()->json($request);

    }

    public function detalleAsistencia(Request $request){

        $data = [];

        $registros = Asistencia::where('id_persona', $request->id_persona)->where('id_agenda', $request->id_agenda)->select('id', 'id_persona', 'id_agenda', 'id_tipo', 'hora', 'motivo', DB::raw("to_char(fecha_registro, 'DD/MM/YYYY HH24:MI:SS') as fecha_registro"), 'registrado_por')->orderBy('id', 'desc')->get();

        foreach ($registros as &$registro) {
            
            $registro->tipo;
            $registro->persona_registra->usuario;

        }

        $data["items"] = $registros;

        $data["fields"] = [
            [
                "label" => "Tipo",
                "key" => "tipo",
                "sortable" => true
            ],
            [
                "label" => "Hora",
                "key" => "hora",
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
