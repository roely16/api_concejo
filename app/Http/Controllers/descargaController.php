<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Documento;

use Storage;

class descargaController extends Controller
{
    public function descargarArchivo($id){

        $documento = Documento::find($id);

        return Storage::download($documento->archivo, $documento->nombre);

    }
}
