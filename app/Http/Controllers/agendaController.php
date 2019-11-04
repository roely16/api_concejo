<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Storage;

use App\Agenda;
// use App\Acta;
use DB;
use PDF;
use App\Bitacora_Agenda;
use App\Bitacora_Correo;
use App\Estado_Agenda;
use App\Persona;

// Envio de correos
use App\Mail\AprobacionAgenda;

// Envio de correo con la agenda
use App\Mail\AgendaConcejo;

class AgendaController extends Controller
{

    public function __construct()
    {
        DB::setDateFormat('DD/MM/YYYY');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data = [];

        $agenda = Agenda::all();

        $data["items"] = $agenda;
        $data["fields"] = [
            [
                "label" => "Acto No.",
                "key" => "no_agenda",
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $agenda = new Agenda;
        $agenda->fecha = $request->fecha;
        $agenda->id_tipo  = $request->id_tipo;
        $agenda->no_agenda = $request->no_agenda;

        $agenda->save();

        return response()->json($agenda);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $agenda = Agenda::find($id);

        return response()->json($agenda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function pdfAgenda($id){

        $puntos = Agenda::find($id)->puntos_agenda()->where('eliminado', null)->get();
        $acta = Agenda::find($id);

        $number_formatter = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
        $no_acta_letras = strtoupper($number_formatter->format($acta->numero_acta));

        // Fecha de la agenda
        setlocale(LC_ALL, 'es_ES');

        $agenda = Agenda::find($id);
        $array_fecha = preg_split("#/#", $agenda->fecha);
        $day = $array_fecha[0];
        $month = $array_fecha[1];
        $year = $array_fecha[2];

        $f = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        $string_fecha = strtoupper(strftime('%A', strtotime($year.'/'.$month.'/'.$day)) . ' ' . intval($day) . ' DE ' . strftime('%B', strtotime($year.'/'.$month.'/'.$day)) . ' DEL AÑO ' . $f->format($year));

        $data = [
            "acta" => $acta,
            "puntos" => $puntos
        ];

        $tipo = $agenda->tipo_agenda->nombre;

        $data = [
            'title' => 'Welcome to HDTuto.com',
            'acta' => $acta,
            'puntos_agenda' => $puntos,
            'no_acta_letras' => $no_acta_letras,
            'string_fecha' => $string_fecha,
            'tipo_agenda' =>  strtoupper($tipo)
        ];

        $pdf = PDF::loadView('myPDF', $data);
        $pdf->setPaper('legal', 'portrait');

        // return response()->json('mail');
        // return $pdf->open('itsolutionstuff.pdf');

        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }

    public function eliminarAgenda(Request $request){

        $id_agenda = $request->id_agenda;

        $agenda = Agenda::find($id_agenda);
        $agenda->eliminada = 'S';
        $agenda->save();

        return response()->json($request);

    }

    public function registrarAgenda(Request $request){

        try {

            $acta = new Agenda();
            $acta->id_tipo = $request->id_tipo;
            // $acta->numero_acta = $request->no_agenda;
            // $acta->year = $request->year;
            $acta->fecha = $request->fecha;
            $acta->descripcion = $request->descripcion;
            // $acta->id_estado = 1;

            $result = $acta->save();

            $this->registrarBitacora($acta->id, 1, $request->id_usuario);

            if (!$result) {
                return response()->json('Error al registrar');
            }

            // Registrar en la bitacora

        } catch (\Exception $e) {
            
            $error = [
                "code" => 100,
                "message" => "Problema al registrar el acta.",
                "codigo_error" => $e->getCode()
            ];

            return response()->json($error);

        }
        
        return response()->json($result);

    }

    public function obtenerAgendas(){

        $data = [];

        $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->get();

        // foreach ($agendas as &$agenda) {
        //     $agenda->whereHas('bitacora_agenda', function($query){
        //         $query->where('id_estado', 1);
        //     })->get();
        // }

        foreach ($agendas as &$agenda) {
            
            $bitacora = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->first();

            $agenda->bitacora = $bitacora;

            $agenda->bitacora->estado;

            // $estado_agenda = Estado_Agenda::whre('id_estado', $bitacora->id_estado)->get();
            
            // $bitacora->estado;
        }

        $data["items"] = $agendas;
        $data["fields"] = [
            [
                "label" => "ID",
                "key" => "id",
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

    public function detalleAgenda($id){

        $agenda = Agenda::find($id);
        // $acta->estado;
        $agenda->tipo_agenda;

        $bitacora = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->first();
        $agenda->bitacora = $bitacora;
        $agenda->bitacora->estado;

        return response()->json($agenda);

    }

    public function editarAgenda(Request $request){

        try {
            
            $id = $request->id;

            $acta = Agenda::find($id);

            $acta->id_tipo = $request->id_tipo;
            // $acta->numero_acta = $request->numero_acta;
            // $acta->year = $request->year;
            $acta->fecha = $request->fecha;
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

    public function registrarBitacora($id_agenda, $id_estado, $id_usuario = 1){

        $bitacora_agenda = new Bitacora_Agenda();
        $bitacora_agenda->id_agenda = $id_agenda;
        $bitacora_agenda->id_estado = $id_estado;
        $bitacora_agenda->fecha = DB::raw('SYSDATE');
        $bitacora_agenda->id_usuario = $id_usuario;

        $bitacora_agenda->save();

    }

    public function bitacoraCambios($id){

        $data = [];

        // $bitacora_agenda = Bitacora_Agenda::where('id_agenda', $id)->get('id', 'id_agenda', 'id_estado',);

        $agenda = Agenda::find($id);
        
        $bitacora_agenda = $agenda->bitacora_agenda;

        foreach ($bitacora_agenda as &$item) {
            $item->estado;
            $item->persona->usuario;

            // Si el estado es 4
            if ($item->id_estado == 4) {
                
                $item->correos = true;
                $historial = $item->historial_correos;

                foreach ($historial as &$registro) {
                    
                    $registro->persona->puesto;

                }
            }
        }

        $data["items"] = $bitacora_agenda;

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

        $bitacora_correo = Bitacora_Correo::where('id_bitacora', $id)->first();

        return Storage::download('agendas/'.$bitacora_correo->archivo, $bitacora_correo->nombre_archivo);

        // return response()->json($bitacora_correo);

    }

    public function finalizarAgenda(Request $request){

        $bitacora_agenda = new Bitacora_Agenda();
        $bitacora_agenda->id_agenda = $request->id_agenda;
        $bitacora_agenda->id_estado = 5;
        $bitacora_agenda->fecha = DB::raw('SYSDATE');
        $bitacora_agenda->id_usuario = $request->id_usuario;
        $bitacora_agenda->save();

        return response()->json($bitacora_agenda);

    }

    // Revisor de Agendas
    public function agendasEnAnalisis(){

        $data = [];

        // $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->get();

        // foreach ($agendas as &$agenda) {
            
        //     $bitacora = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->first();

        //     $agenda->bitacora = $bitacora;

        //     $agenda->bitacora->estado;

        // }

        // $data["items"] = $agendas;

        // $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->get();

        // foreach ($agendas as &$agenda) {
            
        //     $agenda->bitacora_agenda;
        // }

        $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->get();

        $agendas_analisis = [];

        foreach ($agendas as &$agenda) {
            
            $bitacora_agenda = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->get();

            // $agenda->bitacora = $bitacora_agenda[0];

            if ($bitacora_agenda[0]->id_estado == 2) {
                
                $agenda->bitacora = $bitacora_agenda[0];

                $agenda->bitacora->estado;

                $agendas_analisis [] = $agenda;

            }

        }

        $data["items"] = $agendas_analisis;

        $data["fields"] = [
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

    public function aprobarAgenda(Request $request){

        $agenda = Agenda::find($request->id);

        // Buscar en la bitacora quien envio la agenda para analisis por ultima vez para que ha esta persona se le envie el correo
        $bitacora_agenda = Bitacora_Agenda::where('id_agenda', $request->id)->where('id_estado', 2)->orderBy('id', 'desc')->first();

        // Responsable de la aprobacion
        $persona = Persona::find($request->id_persona);


        $data = new \stdClass();
        $data->fecha = $agenda->fecha;
        $data->responsable_aprobacion = $persona->nombre . ' ' . $persona->apellido;

        // Enviar el correo de aprobación de la agenda
        Mail::to($bitacora_agenda->persona->email)->send(new AprobacionAgenda($data));
        
        // Escribir en la bitacora de la agenda quien lo esta aprobando
        $bitacora_agenda = new Bitacora_Agenda();
        $bitacora_agenda->id_agenda = $request->id;
        $bitacora_agenda->id_estado = 3;
        $bitacora_agenda->fecha = DB::raw('SYSDATE');
        $bitacora_agenda->id_usuario = $request->id_persona;
        $bitacora_agenda->save();

        return response()->json($persona);

    }


    // Envio de la agenda al concejo

    public function obtenerConcejo(){

        $concejo = Persona::where('enviar_agenda', 'S')->with('puesto')->orderBy('id', 'asc')->get();

        foreach ($concejo as &$item) {
            $item->enviar = true;
        }

        $data = [];
        $data["items"] = $concejo;

        $data["fields"] = [
            [
                "label" => "Persona",
                "key" => "nombre",
                "sortable" => true
            ],
            [
                "label" => "Puesto",
                "key" => "puesto",
                "sortable" => true
            ],
            [
                "label" => "Email",
                "key" => "email",
                "sortable" => true
            ],
            [
                "label" => "Enviar Agenda",
                "key" => "enviar_agenda",
                "class" => "text-right"
            ]
        ];

        return response()->json($data);

    }

    public function enviarAgendaConcejo(Request $request){

        $personas = $request->personas;
        $id_agenda = $request->id_agenda;
        $id_usuario = $request->id_usuario;

        // Construcción del archivo a enviar
        $puntos = Agenda::find($id_agenda)->puntos_agenda;
        $agenda = Agenda::find($id_agenda);

        // Fecha de la agenda
        setlocale(LC_ALL, 'es_ES');
        $array_fecha = preg_split("#/#", $agenda->fecha);
        $day = $array_fecha[0];
        $month = $array_fecha[1];
        $year = $array_fecha[2];

        $f = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

        $string_fecha = strtoupper(strftime('%A', strtotime($year.'/'.$month.'/'.$day)) . ' ' . intval($day) . ' DE ' . strftime('%B', strtotime($year.'/'.$month.'/'.$day)) . ' DEL AÑO ' . $f->format($year));
        
        // Tipo de Agenda
        $tipo = $agenda->tipo_agenda->nombre;

        $data = [
            'title' => 'Agenda',
            'agenda' => $agenda,
            'puntos_agenda' => $puntos,
            'string_fecha' => $string_fecha,
            'tipo_agenda' =>  strtoupper($tipo)
        ];

        // Creación del PDF
        $pdf = PDF::loadView('pdf.agenda', $data);
        $pdf->setPaper('legal', 'portrait');

        // Se almace la agenda
        $content = $pdf->download()->getOriginalContent();
        $unique_id_file = time();
        Storage::put('agendas/'.$unique_id_file, $content);
        $file_name = "Agenda.pdf";

        $correo_agenda = new \stdClass();
        $correo_agenda->nombre_archivo =  $unique_id_file;
        $correo_agenda->etiqueta_archivo = $file_name;

        $correos = [];

        foreach ($personas as $persona) {
            
            $correos [] = $persona["email"];

        }

        Mail::to($correos)->send(new AgendaConcejo($correo_agenda));

        // Cambiar el estado de la agenda a enviado
        $bitacora_agenda = new Bitacora_Agenda();
        $bitacora_agenda->id_agenda = $id_agenda;
        $bitacora_agenda->id_estado = 4;
        $bitacora_agenda->fecha = DB::raw('SYSDATE');
        $bitacora_agenda->id_usuario = $id_usuario;
        $bitacora_agenda->save();

        // Escribir en la bitacora de correos
        
        foreach ($personas as $persona) {

            $bitacora_correo = new Bitacora_Correo();

            $bitacora_correo->id_bitacora = $bitacora_agenda->id;
            $bitacora_correo->id_persona = $persona["id"];
            $bitacora_correo->archivo = $unique_id_file;
            $bitacora_correo->enviado = 'S';
            $bitacora_correo->fecha_envio = DB::raw('SYSDATE');
            $bitacora_correo->enviado_por = $id_usuario;
            $bitacora_correo->nombre_archivo = $file_name;
            $bitacora_correo->save();

        }

        return response()->json($request);

    }

    // Asistente a la sesión
    public function agendasSesion(){

        $data = [];

        $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->get();

        $agendas_sesion = [];

        foreach ($agendas as $agenda) {
        
            $bitacora_agenda = Bitacora_Agenda::where('id_agenda', $agenda->id)->orderBy('id', 'desc')->first();

            if ($bitacora_agenda->id_estado == 4) {
                
                $agenda->bitacora = $bitacora_agenda;

                $agenda->bitacora->estado;

                $agendas_sesion [] = $agenda;

            }

        }

        $data["items"] = $agendas_sesion;

        $data["fields"] = [
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

}
