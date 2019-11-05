<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Agenda</title>
    
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
	
    <div class="titulo">
        <strong>SESIÓN {{ $tipo_agenda }} DEL CONCEJO DEL MUNICIPIO DE GUATEMALA, PARA CELEBRAR EL DÍA {{ Str::upper($string_fecha) }}</strong>        
    </div>

    <br><br>

    <div class="titulo_agenda">
        <strong>AGENDA</strong>
    </div>
    
    <br><br>

    <ol>
        @foreach ($puntos_agenda as $punto)
            <li style="page-break-inside: avoid;">{{ $punto->descripcion }}</li>
        @endforeach
    </ol>

</body>
</html>