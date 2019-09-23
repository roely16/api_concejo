<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Punto_Agenda;
use App\Acta;
use App\Bitacora_Punto;



class puntoAgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {

        $puntos = Acta::find($id)->puntos_agenda;
        $acta = Acta::find($id);

        $data = [
            "acta" => $acta,
            "puntos" => $puntos
        ];

        return response()->json($data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            
            $punto_agenda = new Punto_Agenda();
            $punto_agenda->descripcion = $request->descripcion;
            $punto_agenda->id_acta = $request->id_acta;
            $punto_agenda->orden  = $request->orden;

            $punto_agenda->save();

            $this->registrarBitacora($punto_agenda->id, 1);

        } catch (\Exception $e) {
           
            return response()->json($e->getMessage());

        }

        // $this->index($puntos_agenda->id_acta);

        return response()->json($punto_agenda);

    }

    public function editar(Request $request){

        $punto_agenda = Punto_Agenda::find($request->id);
        $punto_agenda->descripcion = $request->descripcion;
        $punto_agenda->save();

        return response()->json($punto_agenda);

    }

    public function reordenar(Request $request){

        $puntos = $request->all();
        $i = 0;

        foreach ($puntos as $punto) {
            
            $i++;
            $punto_agenda = Punto_Agenda::find($punto["id"]);
            $punto_agenda->orden = $i;
            $punto_agenda->save();

        }

        return response()->json(["code" => 200]);

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
        
        try {
            
            $punto_agenda = Punto_Agenda::find($id);

            if ($punto_agenda) {
                
                $punto_agenda->eliminado = 'S';
                $punto_agenda->save();

                // Registrar en la bitácora
                $this->registrarBitacora($punto_agenda->id, 3);

            }else{

                $error = [
                    "code" => 100,
                    "message" => "El punto de agenda no existe.",
                    "codigo_error" => 100
                ];

                return response()->json($error);

            }

        } catch (\Exception $e) {
           
            $error = [
                "code" => 100,
                "message" => "Problema al eliminar el punto de agenda.",
                "codigo_error" => $e->getCode()
            ];

            return response()->json($error);

        }
        

        return response()->json([ "code" => 200 ]);

    }

    public function registrarBitacora($id_punto, $id_accion){

        $bitacora_punto = new Bitacora_Punto();
        $bitacora_punto->id_punto = $id_punto;
        $bitacora_punto->id_accion = $id_accion;
        $bitacora_punto->fecha = DB::raw('SYSDATE');
        $bitacora_punto->usuario = 1;

        $bitacora_punto->save();

    }
}
