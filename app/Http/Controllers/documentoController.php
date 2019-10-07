<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Tipo_Documento;

use Illuminate\Support\Facades\Storage;
use DB;

class documentoController extends Controller
{
    public function registrarDocumento(Request $request){

        $path = $request->file('file')->store('documentos');
        $tipo = $request->tipo;
        $autor = $request->autor;

        if ($request->descripcion === null) {
            
            $descripcion = '';

        }else{

            // return response()->json('no es null');

            $descripcion = $request->descripcion;

        }   

        $documento = new Documento();
        $documento->id_tipo = 1;
        $documento->archivo = $path;
        $documento->nombre = $request->file->getClientOriginalName();
        $documento->autor = $autor;
        $documento->descripcion = $descripcion;
        $documento->id_agenda = $request->id_agenda;
        $documento->fecha_creacion = DB::raw('SYSDATE');
        $documento->subido_por = $request->id_usuario;
        $documento->save();

        // $fileName = $request->file->getClientOriginalName();

        return response()->json($request);

    }

    public function obtenerDocumentos($id){

        $data = [];

        $documentos = Documento::where('id_agenda', $id)->with('tipo_documento')->get();

        foreach ($documentos as &$documento) {
            
            $documento->persona->usuario;
            
        }

        $data["items"] = $documentos;

        $data["fields"] = [
            [
                "label" => "Nombre",
                "key" => "nombre",
                "sortable" => true
            ],
            [
                "label" => "Autor",
                "key" => "autor",
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

    public function detalleDocumento($id){

        $documento = Documento::find($id);

        // Tipos de documentos 
        $tipos_documentos = Tipo_Documento::all();

        $data = [];
        $data["detalle"] = $documento;
        $data["tipos_documentos"] = $tipos_documentos;

        return response()->json($data);

    }

    public function datosModal(){

        $tipos_documentos = Tipo_Documento::all();

        return response()->json($tipos_documentos);

    }

    public function editarDocumento(Request $request){

        $documento = (json_decode($request->documento, true));
        
        $obj_documento = Documento::find($documento["id"]);
        $obj_documento->id_tipo = $documento["id_tipo"];
        $obj_documento->autor = $documento["autor"];
        $obj_documento->descripcion = $documento["descripcion"];
        
        if ($documento["changeDocument"]) {
            
            // Eliminar el archivo anterior
            Storage::delete($obj_documento->archivo);
            $path = $request->file('archivo')->store('documentos');
            $obj_documento->archivo = $path;
            $obj_documento->nombre = $request->archivo->getClientOriginalName();
        }

        $obj_documento->save();

        return response()->json($obj_documento);

    }

    public function eliminarDocumento($id){

        $documento = Documento::find($id);        

        // Eliminar el archivo
        Storage::delete($documento->archivo);
        $documento->delete();

        return response()->json($documento);

    }

    public function descargarArchivo($id){

        $documento = Documento::find($id);

        return Storage::download($documento->archivo, $documento->nombre);

    }
}
