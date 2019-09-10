<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Punto_Agenda;

class puntoAgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
        // $puntos_agenda = DB::select('select * from cnj_punto_agenda where id_agenda = ?', [40]);
        $puntos_agenda = Punto_Agenda::where('id_agenda', $id)->orderBy('orden')->get();

        return response()->json($puntos_agenda);

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
        $punto_agenda = new Punto_Agenda;
        $punto_agenda->descripcion = $request->descripcion;
        $punto_agenda->id_agenda = $request->id_agenda;
        $punto_agenda->orden  = $request->orden;

        $punto_agenda->save();

        return response()->json($punto_agenda);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
