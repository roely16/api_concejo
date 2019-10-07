<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Agenda;
// use App\Acta;
use DB;
use PDF;

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

        $data = [
            "acta" => $acta,
            "puntos" => $puntos
        ];

        $data = [
            'title' => 'Welcome to HDTuto.com',
            'acta' => $acta,
            'puntos_agenda' => $puntos,
            'no_acta_letras' => $no_acta_letras
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
            $acta->id_estado = 1;

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

    public function obtenerAgendas(){

        $data = [];

        $agendas = Agenda::where('eliminada', null)->orderBy('id', 'desc')->with('estado')->get();

        $data["items"] = $agendas;
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

    public function detalleAgenda($id){

        $acta = Agenda::find($id);
        $acta->estado;
        $acta->tipo_agenda;

        return response()->json($acta);

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
}
