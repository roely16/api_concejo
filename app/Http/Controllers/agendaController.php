<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Agenda;
use DB;

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
                "label" => "No",
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
}
