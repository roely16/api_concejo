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
        }
    </style>
	
    <div class="no_acta">
        <div>
            Acto No. {{ $acta->numero_acta }} - {{ $acta->year }}
        </div>
        <div>
            -1-
        </div>
    </div>
        
    <div class="titulo">
        <strong>ACTA NÚMERO {{ $no_acta_letras }}</strong>
    </div>
    
    <div class="titulo">
        <strong>SESIÓN ORDINARIA DEL CONCEJO DEL MUNICIPIO DE GUATEMA, CELEBRADA EL DÍA LUNES DIEZ DE JUNIO DEL AÑO DOS MIL DIECINUEVE</strong>
        
    </div>

    <br><br>

    <!-- {{ $acta }} -->

    <div class="titulo_agenda">
        <strong>AGENDA</strong>
    </div>
    
    <br><br>

    <ol style="padding-left: 1.8em;">
        @foreach ($puntos_agenda as $punto)
            <li>{{ $punto->descripcion }}</li>
        @endforeach
    </ol>

</body>
</html>