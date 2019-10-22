<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Punto_Agenda;
use App\Punto_Acta;
use App\Acta;

class buscadorController extends Controller
{
    //
    public function buscarTexto(Request $request){

        $resultados = [];

        $ordinales = [
            "PRIMERO", "SEGUNDO", "TERCERO", "CUARTO", "QUINTO", "SEXTO", "SÉPTIMO", "OCTAVO", "NOVENO"
        ];

        $ordinales_centenas = [
            "DÉCIMO", "VIGÉSIMO", "TRIGÉSIMO", "CUADRAGÉISMO", "QUINCUAGÉSIMO", "SEXAGÉSIMO", "SEPTUAGÉSIMO", "OCTOGÉSIMO", "NONAGÉSIMO"
        ];

        // Buscar en puntos de Agenda

        $puntos_agenda = Punto_Agenda::where(DB::raw('UPPER(descripcion)'), 'like', '%'.strtoupper($request->busqueda).'%')->get();

        // Por cada resultado buscar el acta y la agenda
        foreach ($puntos_agenda as &$punto_agenda) {
            
            $punto_agenda->resultado_agenda = true;

            // Buscar el acta
            $acta = Acta::where('id_agenda', $punto_agenda->id_acta)->first();
            $punto_agenda->acta = $acta;

            $punto_agenda->agenda;

            $punto_agenda->punto_acta;
            $punto_agenda->resultado_agenda = true;
            
            if ($punto_agenda->punto_acta) {
                
                // Asignar el ordinal del punto de acta
                $cantidad_digitos = strlen((string)$punto_agenda->orden);

                if ($cantidad_digitos == 1) {
                
                    $punto_agenda->ordinal = $ordinales[$punto_agenda->orden - 1];
    
                }elseif($cantidad_digitos == 2){

                    $digits = (string)$punto_agenda->orden;
                    $primero = $digits[0];
                    $segundo = $digits[1];
    
                    if ($segundo > 0) {
                       
                        $punto_agenda->ordinal = $ordinales_centenas[$primero - 1] . " " . $ordinales[$segundo - 1];
    
                    }else{
    
                        $punto_agenda->ordinal = $ordinales_centenas[$primero - 1];
    
                    }
                   
                }

                $punto_agenda->punto_acta->descripcion = substr_replace( $punto_agenda->punto_acta->descripcion, "<strong>".$punto_agenda->ordinal.": </strong>", 3, 0 );

                $punto_agenda->punto_acta->acta;

            }

            $resultados [] =  $punto_agenda;
        }

        // Buscar en puntos de Acta

        $puntos_acta = Punto_Acta::where(DB::raw('UPPER(descripcion)'), 'like', '%'.strtoupper($request->busqueda).'%')->get();

        // Por cada resultado buscar el acta y la agenda
        foreach ($puntos_acta as &$punto_acta) {
            
            $cantidad_digitos = strlen((string)$punto_acta->punto_agenda->orden);

            if ($cantidad_digitos == 1) {
                
                $punto_acta->ordinal = $ordinales[$punto_acta->punto_agenda->orden - 1];

            }elseif($cantidad_digitos == 2){

                $digits = (string)$punto_acta->punto_agenda->orden;
                $primero = $digits[0];
                $segundo = $digits[1];

                if ($segundo > 0) {
                   
                    $punto_acta->ordinal = $ordinales_centenas[$primero - 1] . " " . $ordinales[$segundo - 1];

                }else{

                    $punto_acta->ordinal = $ordinales_centenas[$primero - 1];

                }
               
            }

            $punto_acta->descripcion = substr_replace( $punto_acta->descripcion, "<strong>".$punto_acta->ordinal.": </strong>", 3, 0 );

            $result = str_ireplace($request->busqueda, '<mark>'. $request->busqueda .'</mark>', $punto_acta->descripcion);
            $punto_acta->descripcion = $result;

            $punto_acta->resultado_acta = true;
            $punto_acta->acta->agenda;
            $punto_acta->punto_agenda;

            $resultados [] =  $punto_acta;

        }

        return response()->json($resultados);

    }
}
