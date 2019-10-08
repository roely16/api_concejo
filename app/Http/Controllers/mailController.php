<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Agenda;

use DB;
use Storage;

use App\Mail\Test;
use Illuminate\Support\Facades\Mail;

use \NumberFormatter;

use App\Bitacora_Agenda;

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
            'no_acta_letras' => $no_acta_letras,
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

            // Fecha de la agenda
            setlocale(LC_ALL, 'es_ES');

            $agenda = Agenda::find($id);
            $array_fecha = preg_split("#/#", $agenda->fecha);
            $day = $array_fecha[0];
            $month = $array_fecha[1];
            $year = $array_fecha[2];

            $f = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);

            $string_fecha = strtoupper(strftime('%A', strtotime($year.'/'.$month.'/'.$day)) . ' ' . intval($day) . ' DE ' . strftime('%B', strtotime($year.'/'.$month.'/'.$day)) . ' DEL AÑO ' . $f->format($year));

            $data = [
                "acta" => $acta,
                "puntos" => $puntos
            ];

            $tipo = $agenda->tipo_agenda->nombre;

            $data = [
                'title' => 'Welcome to HDTuto.com',
                'acta' => $acta,
                'puntos_agenda' => $puntos,
                'no_acta_letras' => $no_acta_letras,
                'string_fecha' => $string_fecha,
                'tipo_agenda' =>  strtoupper($tipo)
            ];

            $pdf = PDF::loadView('myPDF', $data);
            $pdf->setPaper('legal', 'portrait');

            $content = $pdf->download()->getOriginalContent();

            $nombre_archivo = $acta->numero_acta . '-' . $acta->year;
            $fecha = date('d-m-Y H:i:s');
            $file_name = "Agenda " . $agenda->fecha . '.pdf';

            $unique_id_file = time();

            Storage::put('agendas/'.$unique_id_file, $content);

            $objDemo = new \stdClass();
            $objDemo->nombre_archivo =  $unique_id_file;
            $objDemo->etiqueta_archivo = $file_name;

            $resultados_envio = [];

            $emails_enviados = [];

            foreach ($destinos as $destino) {
            
                // Se envia el correo

                Mail::to($destino["email"])->send(new Test($objDemo));

                $envio_persona = [];
                $envio_persona["id"] = $destino["id"];
                $envio_persona["archivo"] = $unique_id_file;
                $envio_persona["enviado"] = 'S';
                $envio_persona["fecha_envio"] = date('d/m/Y H:i:s');
                $envio_persona["enviado_por"] = 1;

                $emails_enviados [] = $envio_persona;

                // Se registra en la bitacora

                // $id_persona = $destino["id"];

                // $insert = DB::table('cnj_bitacora_correo')->insert(['id_acta' => $id, 'id_persona' => $id_persona, 'archivo' => $unique_id_file, 'enviado' => 'S', 'fecha_envio' => DB::raw('SYSDATE')]);    

                // if(Mail::failures()){
                //     $resultados_envio [] = 'Failed to send mail.';
                // }

            }

            // Cambiar el estado de la agenda a En Analisis 
            $bitacora_agenda = new Bitacora_Agenda();
            $bitacora_agenda->id_agenda = $id;
            $bitacora_agenda->id_estado = 2;
            $bitacora_agenda->fecha = DB::raw('SYSDATE');
            $bitacora_agenda->id_usuario = 1;
            $bitacora_agenda->save();

            // Registrar en el historial de envio de correos
            foreach ($emails_enviados as $email) {
                
            }

            

        // } catch (\Exception $e) {
            
        //     return response()->json($e);

        // }

        return response()->json($resultados_envio);

    }

    public function fechaAgenda($id){

        setlocale(LC_ALL, 'es_ES');

        $agenda = Agenda::find($id);
        $array_fecha = preg_split("#/#", $agenda->fecha);
        $day = $array_fecha[0];
        $month = $array_fecha[1];
        $year = $array_fecha[2];

        $f = new NumberFormatter("es", NumberFormatter::SPELLOUT);

        $string_fecha = strtoupper(strftime('%A', strtotime($year.'/'.$month.'/'.$day)) . ' ' . intval($day) . ' de ' . strftime('%B', strtotime($year.'/'.$month.'/'.$day)) . ' del año ' . $f->format($year));

        $tipo = $agenda->tipo_agenda->nombre;

        return response()->json($tipo);

    }

}
