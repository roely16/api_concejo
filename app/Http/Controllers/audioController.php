<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Audio;
use Illuminate\Support\Facades\Storage;

use DB;

class audioController extends Controller
{
    
    public function registrarAudio(Request $request){

        // $path = $request->file('file')->store('audios');

        $file = $request->file;

        $uniqueid=uniqid();
        $original_name=$file->getClientOriginalName();
        $size=$file->getSize();
        $extension=$file->getClientOriginalExtension();
        $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
        $audiopath=url('/storage/public/audios/'.$filename);
        $path=$file->storeAs('public/audios',$filename);

        $datos_audio = (json_decode($request->datos_audio, true));

        $audio = new Audio();
        $audio->nombre = $datos_audio["nombre"];
        $audio->descripcion = $datos_audio["descripcion"];
        $audio->archivo = $path;
        $audio->id_agenda = $datos_audio["id_agenda"];
        $audio->fecha_creacion = DB::raw('SYSDATE');
        $audio->subido_por = $datos_audio['subido_por'];
        $audio->nombre_archivo = $file->getClientOriginalName();
        $audio->save();

        return response()->json($request);

    }

    public function obtenerAudios($id){

        $data = [];

        $audios = Audio::where('id_agenda', $id)->get();

        foreach ($audios as &$audio) {
            
            $audio->persona->usuario;
            
        }

        foreach ($audios as &$audio) {
            
            $audio->link = Storage::url($audio->archivo);

        }

        $data["items"] = $audios;

        $data["fields"] = [
            [
                "label" => "Nombre",
                "key" => "nombre",
                "sortable" => true
            ],
            [
                "label" => "Fecha",
                "key" => "fecha",
                "sortable" => true
            ],
            [
                "label" => "Subido por",
                "key" => "subido_por",
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

    public function eliminarAudio($id){

        $audio = Audio::find($id);

        Storage::delete($audio->archivo);
        $audio->delete();

        return response()->json($audio);

    }

    public function detalleAudio($id){

        $audio = Audio::find($id);

        return response()->json($audio);

    }

    public function editarAudio(Request $request){

        $datos_audio = (json_decode($request->datos_audio, true));

        $obj_audio = Audio::find($datos_audio["id"]);

        $obj_audio->nombre = $datos_audio["nombre"];
        $obj_audio->descripcion = $datos_audio["descripcion"];

        if ($datos_audio["changeAudio"]) {
            
            // Eliminar el archivo anterior
            Storage::delete($obj_audio->archivo);
            // $path = $request->file('archivo')->store('documentos');

            $file = $request->file;

            $uniqueid = uniqid();
            $extension = $file->getClientOriginalExtension();
            $filename = Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $audiopath = url('/storage/public/audios/'.$filename);
            $path = $file->storeAs('public/audios',$filename);

            $obj_audio->archivo = $path;
            $obj_audio->nombre_archivo = $file->getClientOriginalName();
        }

        $obj_audio->save();

        return response()->json($obj_audio);

    }

}
