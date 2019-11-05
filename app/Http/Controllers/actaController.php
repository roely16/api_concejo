<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Tipo_Acta;
use App\Agenda;
use App\Acta;
use App\Punto_Agenda;
use App\Punto_Acta;
use App\Bitacora_Punto_Acta;
use App\Bitacora_Agenda;
use App\Persona;
use App\Bitacora_Acta;
use App\Bitacora_Correo;
use App\Punto_Agenda_Sesion;

use App\Mail\ActaRevision;

use DB;
use PDF;
use Storage;

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

            $this->registrarBitacoraActa($acta->id, 1, $request->id_usuario);

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
            
            $bitacora = Bitacora_Acta::where('id_acta', $acta->id)->orderBy('id', 'desc')->first();

            $acta->bitacora = $bitacora;

            $acta->bitacora->estado;

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

    public function detalleActa($id, $id_punto = null){

        $data = [];

        $acta = Acta::find($id);
        $acta->agenda;

        $agendas = Agenda::all('id', 'id_tipo', DB::raw("to_char(fecha, 'dd/mm/yyyy') as fecha"), 'asistencia_congelada', 'descripcion', 'eliminada');

        if ($id_punto) {
            # code...
            $punto_agenda = Punto_Agenda::find($id_punto);
            $data["punto_agenda"] = $punto_agenda;
        }
        
        $bitacora = Bitacora_Acta::where('id_acta', $acta->id)->orderBy('id', 'desc')->first();
        $acta->bitacora = $bitacora;
        $acta->bitacora->estado;

        $data["acta"] = $acta;
        $data["agendas"] = $agendas;
        

        return response()->json($data);

    }

    public function detalleActaAgenda($id){

        $acta = Acta::find($id);
        $acta->agenda->tipo_agenda;

        // Bitacora del Acta
        $bitacora = Bitacora_Acta::where('id_acta', $acta->id)->orderBy('id', 'desc')->first();
        $bitacora->estado;

        $acta->ultimo_estado = $bitacora;

        return response()->json($acta);

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

        // Puntos de la Agenda
        $acta->agenda->puntos_agenda = Punto_Agenda_Sesion::where('id_agenda', $acta->agenda->id)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        // $puntos_agenda = $acta->agenda->puntos_agenda;

        foreach ($acta->agenda->puntos_agenda as &$punto_agenda) {
            
            $punto_acta = Punto_Acta::where('id_punto_agenda', $punto_agenda->id)->where('eliminado', null)->first();

            if ($punto_acta) {
                
                // Obtener la bitacora del punto de acta
                $bitacora_punto = Bitacora_Punto_Acta::where('id_punto', $punto_acta->id)->orderBy('id', 'desc')->first();

                // Si la acción es 4 marcar de color verde
                if ($bitacora_punto->id_accion == 4) {
                    
                    $punto_acta->color = 'success';

                }else{

                    $punto_acta->color = "primary";

                }

                

            }

            $punto_agenda->punto_acta = $punto_acta;

        }

        $acta->agenda->tipo_agenda;

        return response()->json($acta);

    }

    public function detallePuntoActa(Request $request){

        $id_acta = $request->id_acta;
        $id_punto_agenda = $request->id_punto_agenda;

        $data = [];

        // Punto de la agenda
        $punto_agenda = Punto_Agenda_Sesion::find($id_punto_agenda);
        $punto_agenda->agenda->tipo_agenda;

        // Punto de acta
        $punto_acta = Punto_Acta::where('id_punto_agenda', $id_punto_agenda)->where('eliminado', null)->first();

        if ($punto_acta) {
            //Bitácora del punto de acta
            $punto_acta->bitacora = Bitacora_Punto_Acta::where('id_punto', $punto_acta->id)->orderBy('id', 'desc')->first();
        }
        
        // Puntos Eliminados
        $puntos_eliminados = Punto_Acta::where('id_punto_agenda', $id_punto_agenda)->where('eliminado', 'S')->orderBy('id', 'desc')->get();

        // Datos de la agenda

        $data["punto_agenda"] = $punto_agenda;
        $data["punto_acta"] = $punto_acta;
        $data["puntos_eliminados"] = $puntos_eliminados;

        return response()->json($data);

    }

    public function registroPuntoActa(Request $request){

        $punto_acta = new Punto_Acta();
        $punto_acta->id_acta = $request->id_acta;
        $punto_acta->id_punto_agenda = $request->id_punto_agenda;
        $punto_acta->descripcion = $request->descripcion;
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 1, '', '', '', $request->id_usuario);

        return response()->json($punto_acta);

    }

    public function editarPuntoActa(Request $request){

        $punto_acta = Punto_Acta::find($request->id);
        $punto_acta->descripcion = $request->descripcion;
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 2, $request->original, $request->descripcion, '', $request->id_usuario);

        return response()->json($punto_acta);

    }

    public function registrarBitacora($id_punto, $id_accion, $original = '', $edited = '', $motivo_eliminacion = '', $id_usuario){

        $bitacora_punto = new Bitacora_Punto_Acta();
        $bitacora_punto->id_punto = $id_punto;
        $bitacora_punto->id_accion = $id_accion;
        $bitacora_punto->original = $original;
        $bitacora_punto->modificado = $edited;
        $bitacora_punto->motivo_eliminacion = $motivo_eliminacion;
        $bitacora_punto->fecha = DB::raw('SYSDATE');
        $bitacora_punto->usuario = $id_usuario;

        $bitacora_punto->save();

    }

    public function bitacoraPunto($id){

        $bitacora_punto = Bitacora_Punto_Acta::where('id_punto', $id)->with('accion')->orderBy('id')->get();

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

    public function eliminarPuntoActa(Request $request){

        $punto_acta = Punto_Acta::find($request->id_punto_acta);
        $punto_acta->eliminado = 'S';
        $punto_acta->save();

        $this->registrarBitacora($punto_acta->id, 3, $request->descripcion, '', $request->motivo_eliminacion, $request->id_usuario);

        return response()->json($punto_acta);

    }

    public function vistaPreviaActa($id){

        $acta = Acta::find($id);
        $acta->puntos_acta;
        $acta->agenda->tipo_agenda;

        $acta->agenda->puntos_agenda =  Punto_Agenda_Sesion::where('id_agenda', $acta->agenda->id)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        // Asistencia
        $personas = Persona::has('puesto')->orderBy('id_puesto')->with('puesto')->get();

        // Puntos del Acta
        $puntos_agenda = $acta->agenda->puntos_agenda;

        $ordinales = [
            "PRIMERO", "SEGUNDO", "TERCERO", "CUARTO", "QUINTO", "SEXTO", "SÉPTIMO", "OCTAVO", "NOVENO"
        ];

        $ordinales_centenas = [
            "DÉCIMO", "VIGÉSIMO", "TRIGÉSIMO", "CUADRAGÉISMO", "QUINCUAGÉSIMO", "SEXAGÉSIMO", "SEPTUAGÉSIMO", "OCTOGÉSIMO", "NONAGÉSIMO"
        ];

        foreach ($puntos_agenda as &$punto_agenda) {

            $punto_agenda->punto_acta = Punto_Acta::where('id_punto_agenda', $punto_agenda->id)->where('eliminado', null)->first();

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

                $punto_agenda->punto_acta->texto = substr_replace( $punto_agenda->punto_acta->descripcion, "<strong>".$punto_agenda->ordinal.": </strong>", 3, 0 );

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

        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }

    public function enviarRevision(Request $request){

        $destinos = $request->destinos;

        $response_file = $this->almacenarActa($request->id_acta);

        $correos = [];

        foreach ($destinos as $destino) {
            
            $correos [] = $destino["email"];

        }

        Mail::to($correos)->send(new ActaRevision($response_file));

        // Registrar en la bitacora del acta
        $bitacora_acta = new Bitacora_Acta();
        $bitacora_acta->id_acta = $request->id_acta;
        $bitacora_acta->id_estado = 2;
        $bitacora_acta->fecha = DB::raw('SYSDATE');
        $bitacora_acta->id_usuario = $request->id_usuario;
        $bitacora_acta->save();

        // Registrar en la bitacora del correo
        foreach ($destinos as $destino) {
            
            $bitacora_correo = new Bitacora_Correo();
            $bitacora_correo->id_persona = $destino["id"];
            $bitacora_correo->archivo = $response_file->unique_id_file;
            $bitacora_correo->enviado = 'S';
            $bitacora_correo->fecha_envio = DB::raw('SYSDATE');
            $bitacora_correo->enviado_por = $request->id_usuario;
            $bitacora_correo->nombre_archivo = $response_file->file_name;
            $bitacora_correo->id_bitacora_acta = $bitacora_acta->id;
            $bitacora_correo->save();

        }

        return response()->json($correos);

    }

    public function almacenarActa($id){

        $acta = Acta::find($id);
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

            $punto_agenda->punto_acta = Punto_Acta::where('id_punto_agenda', $punto_agenda->id)->first();

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
        $file_name = "Acta.pdf";

        $data_file = new \stdClass();
        $data_file->unique_id_file = $unique_id_file;
        $data_file->file_name = $file_name;

        return $data_file;

    }

    public function historialActa($id){

        $data = [];

        $bitacora_acta = Bitacora_Acta::where('id_acta', $id)->select('id', 'id_acta', 'id_estado', DB::raw("to_char(fecha, 'dd/mm/yyyy hh24:mi:ss') as fecha"), 'id_usuario')->orderBy('id')->get();

        foreach ($bitacora_acta as &$item) {
            
            $item->estado;
            $item->persona->usuario;

            if ($item->id_estado == 2) {
                
                $item->correos = true;
                $historial = $item->historial_correos;

                foreach ($historial as &$registro) {
                    
                    $registro->persona->rol;

                }
            }

        }

        $data["items"] = $bitacora_acta;

        $data["fields"] = [
            [
                "label" => "Fecha",
                "key" => "fecha",
                "sortable" => true
            ],
            [
                "label" => "Estado",
                "key" => "estado",
                "sortable" => true
            ],
            [
                "label" => "Usuario",
                "key" => "persona",
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

    public function descargarArchivoCorreo($id){

        $bitacora_correo = Bitacora_Correo::where('id_bitacora_acta', $id)->first();

        return Storage::download('actas/'.$bitacora_correo->archivo, $bitacora_correo->nombre_archivo);

    }

    public function registrarBitacoraActa($id_acta, $id_estado, $id_usuario = 1){

        $bitacora_acta = new Bitacora_Acta();
        $bitacora_acta->id_acta = $id_acta;
        $bitacora_acta->id_estado = $id_estado;
        $bitacora_acta->fecha = DB::raw('SYSDATE');
        $bitacora_acta->id_usuario = $id_usuario;

        $bitacora_acta->save();

    }

    // Perfil de Revisor de Actas

    public function actasRevision(){

        $data = [];

        $actas = Acta::where('eliminada', null)->orderBy('id', 'desc')->get();

        $actas_revision = [];

        foreach ($actas as &$acta) {
            
            $bitacora = Bitacora_Acta::where('id_acta', $acta->id)->orderBy('id', 'desc')->first();

            if ($bitacora->id_estado == 2) {
               
                $acta->bitacora = $bitacora;

                $acta->bitacora->estado;

                $acta->agenda->tipo_agenda;

                $actas_revision [] = $acta;

            }

        }

        $data["items"] = $actas_revision;
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

    public function marcarRevisado(Request $request){

        $this->registrarBitacora($request->id_punto, 4, '', '', '', $request->id_usuario);

        return response()->json($request);

    }

    public function puntosAgendaRevisar($id){

        $acta = Acta::find($id);
        $acta->agenda->estado;
        $acta->agenda->puntos_agenda = Punto_Agenda_Sesion::where('id_agenda', $acta->agenda->id)->where('eliminado', null)->orderBy('orden', 'asc')->get();

        $puntos_agenda_acta = [];

        foreach ($acta->agenda->puntos_agenda as &$punto_agenda) {
            
            $punto_acta = Punto_Acta::where('id_punto_agenda', $punto_agenda->id)->where('eliminado', null)->first();

            if ($punto_acta != null) {
                
                $punto_agenda->punto_acta = $punto_acta;

                // Bitacora del punto del acta
                $bitacora = Bitacora_Punto_Acta::where('id_punto', $punto_acta->id)->orderBy('id', 'desc')->first();

                $punto_agenda->punto_acta->bitacora = $bitacora;

                $puntos_agenda_acta [] = $punto_agenda;

            }
            
        }

        $acta->agenda->puntos_agenda_acta = $puntos_agenda_acta;
        $acta->agenda->tipo_agenda;

        return response()->json($acta);

        // return response()->json($id);

    }

    public function aprobarActa(Request $request){

        $this->registrarBitacoraActa($request->id_acta, 3, $request->id_usuario);

        return response()->json($request);

    }

}
