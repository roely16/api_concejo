<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;
use App\Puesto;
use App\Agenda;
use App\Asistencia;
use DB;

class asistenciaController extends Controller
{
    
    public function listaAsistencia($id){

        $data = [];

        $personas = Puesto::with('persona')->where('toma_asistencia', 'S')->get();

        $asistencia = new Asistencia();

        foreach ($personas as &$persona) {
            
            $result = $asistencia->where('id_agenda', $id)->where('id_persona', $persona->persona[0]->id)->get(['id_agenda', 'id_persona', DB::raw("to_char(hora, 'HH24:MI') as hora"), 'motivo_falta']);

            if (!$result->isEmpty()) {

                // Si tiene registro en asistencia validar si es llegada tarde, ausencia o asistencia normal

                $persona->asistencia = true;
                $persona->hora = $result[0]->hora;
                $persona->motivo_falta = $result[0]->motivo_falta;
                // $persona->arrayData = $result;

                if ($persona->hora != null) {
                    
                    // Llego tarde
                    $persona->color = 'warning';

                }elseif($persona->hora == null && $persona->motivo_falta != null){

                    // No se presento
                    $persona->color = 'danger';

                }elseif($persona->hora == null && $persona->motivo_falta == null){

                    // Se presento a tiempo
                    $persona->color = 'success';
                }
                
            }else{
                $persona->asistencia = false;
                $persona->color = 'secondary';
            }

        }

        $agenda = Agenda::find($id, ['id', 'id_tipo', 'asistencia_congelada', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha")]);
        $agenda->tipo_agenda;

        $data["detalle_agenda"] = $agenda;
        $data["personas"] = $personas;
        
        return response()->json($data);

    }

    public function registrarAsistencia(Request $request){

        $result = DB::table('cnj_asistencia')->insert(
            ['id_persona' => $request->id_persona, 'id_agenda' => $request->id_agenda]
        );

        return response()->json($result);

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

}
