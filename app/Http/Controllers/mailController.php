<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Acta;

use DB;

use App\Mail\Test;
use Illuminate\Support\Facades\Mail;


class mailController extends Controller
{
    
    public function __construct()
    {
        DB::setDateFormat('DD/MM/YYYY');
    }

    public function pdfActa($id){

        $puntos = Acta::find($id)->puntos_agenda;
        $acta = Acta::find($id);

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

    public function enviarCorreo(){

        Mail::to('gerson.roely@gmail.com')->send(new Test());

        // return response()->json($request);

    }

}
