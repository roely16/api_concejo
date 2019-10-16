<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Acta No. </title>
</head>
<body>
    
<style>
        .titulo{
            line-height: 2;
            font-size: 14pt
        }
        .titulo_agenda{
            font-size: 14pt;
            text-align: center;
        }
        .no_acta{
            font-size: 9pt;
            text-align: right;
            margin-bottom: 1cm
        }
        body{
            text-align: justify;
            margin-top: 4cm;
            margin-right: 2cm;
            margin-left: 3cm
        }
        li{
            margin-bottom: 10px
        }
        .page_break { page-break-before: always; }
        
    </style>

    <!-- <div class="no_acta">
        <div>
            Acto No.  - 
        </div>
        <div>
            -1-
        </div>
    </div> -->

    <div class="titulo">
        <strong>ACTA NÚMERO {{ $str_no_acta }}</strong>
        <br>
        <strong>SESIÓN {{ Str::upper($acta->agenda->tipo_agenda->nombre) }}  DEL CONCEJO DEL MUNICIPIO DE GUATEMALA, CELEBRADA EL DÍA {{ Str::upper($str_fecha) }}.</strong>        
    </div>
    
    <br><br>

    <div class="titulo_agenda">
        <strong>AGENDA</strong>
    </div>
    
    <ol>
        @foreach ($acta->agenda->puntos_agenda as $punto)
            <li style="page-break-inside: avoid;">{{ $punto->descripcion }}</li>
        @endforeach
    </ol>

    <!-- Nueva Paginda -->
    <!-- <div class="page_break"></div> -->
    
    <div class="titulo_agenda">
        <strong>ASISTENCIA</strong>
    </div>

    <!-- Listado de la asistencia -->
    <br>
    <div>
        <table>
            @foreach($asistencia as $persona)
            <tr>
                <td style="width:150px">{{ $persona->puesto->nombre }}</td>
                <td style="width:50px"></td>
                <td style="width:75px">{{ $persona->titulo_acta }}</td>
                <td style="width:300px">{{ $persona->nombre }} {{ $persona->apellido }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <br>
    <div>
        <p>En la ciudad de Guatemala, siendo las diecisiete horas con diez minutos, del día lunes diez de junio del año dos mil diecinueve, reunidos los miembros del Concejo del Municipio de Guatemala, en el salón de sesiones "Miguel Ángel Asturias" del Palacio Municipal, con el objeto de celebrar sesión ordinaria, se procedió de la manera siguiente: </p>
    </div>

    <!-- Puntos del Acta -->
    @foreach($puntos_acta as $punto_acta)
        <div style="page-break-inside: avoid;">
            <!-- <p><strong>{{  Str::upper($punto_acta->ordinal) }}: </strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam accumsan libero neque. Fusce neque nibh, tincidunt sit amet condimentum sit amet, varius non risus. Etiam et congue nisl. Morbi ullamcorper purus mauris. Donec efficitur arcu lacus, fermentum finibus orci molestie nec. Nam id est pulvinar, porttitor sem sed, tincidunt quam. Pellentesque vitae dictum sapien. Fusce nec efficitur nibh. Donec blandit metus nec sem egestas dignissim. Phasellus pellentesque, quam in egestas feugiat, sapien magna convallis purus, sit amet porta mauris est ac risus.</p> <p>Morbi non justo vitae sapien vulputate dictum eget ut lorem. Etiam in dignissim ipsum. Nullam quis dignissim ex. Sed non diam ultricies purus porta luctus sed non nibh. Nulla interdum tristique nibh, vel aliquet arcu congue in. Suspendisse lacus neque, volutpat in dui vitae, rutrum fermentum nisl. Sed id sollicitudin diam, id vulputate justo. Fusce elementum magna odio, at gravida urna fermentum non. Proin eget consectetur nisi. Nam viverra dictum sem at imperdiet. Ut condimentum nulla dolor.</p> -->
            
            @if($punto_acta->punto_acta)
               
                <?php echo $punto_acta->punto_acta->texto ?>

            @else

                <p><strong>{{  Str::upper($punto_acta->ordinal) }}: </strong></p>

            @endif
        </div>
    @endforeach

    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_text(455, 80, "Acta No. {{ $acta->no_acta }} - {{ $acta->year }}", null, 9, array(0,0,0));
            $pdf->page_text(505, 90, "- {PAGE_NUM} -", null, 9, array(0,0,0));
        }
    </script>
</body>
</html>