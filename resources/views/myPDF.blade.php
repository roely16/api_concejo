<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
	<title>Acta</title>
    
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
            margin-top: 2cm;
            margin-right: 2cm;
            margin-left: 3cm
        },
        li{
            margin-bottom: 10px
        },
        
    </style>
	
    <!-- <div class="no_acta">
        <div>
            Acto No. {{ $acta->numero_acta }} - {{ $acta->year }}
        </div>
        <div>
            -1-
        </div>
    </div> -->
        
    <!-- <div class="titulo">
        <strong>ACTA NÚMERO {{ $no_acta_letras }}</strong>
    </div> -->
    
    <div class="titulo">
        <strong>SESIÓN {{ $tipo_agenda }} DEL CONCEJO DEL MUNICIPIO DE GUATEMALA, PARA CELEBRAR EL DÍA {{ $string_fecha }}</strong>        
    </div>

    <br><br>

    <!-- {{ $acta }} -->

    <div class="titulo_agenda">
        <strong>AGENDA</strong>
    </div>
    
    <br><br>

    <ol>
        @foreach ($puntos_agenda as $punto)
            <li style="page-break-inside: avoid;">{{ $punto->descripcion }}</li>
        @endforeach
    </ol>

    <!-- @foreach($puntos_agenda as $key=>$punto)
        <div class="row">
            <div class="column">
                {{ ++$key }}.
            </div>
            <div class="column2">
                <p style="page-break-inside: avoid;">{{ $punto->descripcion }}</p>
            </div>
        </div>
    @endforeach -->

</body>
</html>