<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Agenda;

use DB;
use Storage;

use App\Mail\Test;
use Illuminate\Support\Facades\Mail;


class mailController extends Controller
{
    
    public function __construct()
    {
        DB::setDateFormat('DD/MM/YYYY');
    }

    public function pdfActa($id){

        $puntos = Agenda::find($id)->puntos_agenda;
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

    public function enviarCorreo(){

        Mail::to('gerson.roely@gmail.com')->send(new Test());

        // return response()->json($request);

    }

    public function enviarAgendaAprobacion(Request $request){

    //try {
            
            $id = $request->id_acta;
            $destinos = $request->destinos;

            $puntos = Agenda::find($id)->puntos_agenda;
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

            $content = $pdf->download()->getOriginalContent();

            $nombre_archivo = $acta->numero_acta . '-' . $acta->year;
            $fecha = date('d-m-Y H:i:s');
            $file_name = "Acta No. " . $nombre_archivo . '.pdf';

            $unique_id_file = time();

            Storage::put('agendas/'.$unique_id_file, $content);

            $objDemo = new \stdClass();
            $objDemo->nombre_archivo =  $unique_id_file;
            $objDemo->etiqueta_archivo = $file_name;

            $resultados_envio = [];

            foreach ($destinos as $destino) {
            
                // Se envia el correo

                Mail::to($destino["email"])->send(new Test($objDemo));

                // Se registra en la bitacora

                $id_persona = $destino["id"];

                $insert = DB::table('cnj_bitacora_correo')->insert(['id_acta' => $id, 'id_persona' => $id_persona, 'archivo' => $unique_id_file, 'enviado' => 'S', 'fecha_envio' => DB::raw('SYSDATE')]);    

                if(Mail::failures()){
                    $resultados_envio [] = 'Failed to send mail.';
                }

            }

            

        // } catch (\Exception $e) {
            
        //     return response()->json($e);

        // }

        return response()->json($resultados_envio);

    }

}
