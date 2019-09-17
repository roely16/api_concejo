<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Acta;

class mailController extends Controller
{
    
    public function sendMail(){

        $puntos = Acta::find(64)->puntos_agenda;
        $acta = Acta::find(64);

        $data = [
            "acta" => $acta,
            "puntos" => $puntos
        ];

        $data = [
            'title' => 'Welcome to HDTuto.com',
            'acta' => $acta,
            'puntos_agenda' => $puntos
        ];
        $pdf = PDF::loadView('myPDF', $data);
        $pdf->setPaper('legal', 'portrait');

        // return response()->json('mail');
        // return $pdf->open('itsolutionstuff.pdf');

        return $pdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }

}
