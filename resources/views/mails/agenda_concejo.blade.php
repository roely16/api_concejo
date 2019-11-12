<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agenda</title>
</head>
<body>
    
    <!-- <h1>Sistema de Control de Actas</h1> -->

    <p>{{ $data->persona->titulo_acta }} {{ $data->persona->nombre }} {{ $data->persona->apellido }}</p>

    <p>Por este medio se hace de su conocimiento la agenda ha tratar en la Sesión {{ $data->agenda->tipo_agenda->nombre }} del Concejo Municipal con fecha {{ $data->agenda->fecha }}.</p>

    <p>Saludos cordinales.</p>

    <br>
    <img src="https://greendevelopment.com.gt/wp-content/uploads/2016/02/logo-muni-guate.png" alt="">

</body>
</html>