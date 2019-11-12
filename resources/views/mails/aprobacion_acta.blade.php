<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    <h1>Sistema de Control de Actas</h1>
    <h3>El Acta número {{ $data->acta->no_acta }} - {{ $data->acta->year }} ha sido aprobada por {{ $data->responsable_aprobacion->nombre }} {{ $data->responsable_aprobacion->apellido }}.</h3>

    <h3>Para acceder al acta siga los siguientes pasos: </h3>

    <ul>
        <li>Dar clic en el siguiente enlace <a href="https://udicat.muniguate.com/apps/appConcejo/">Sistema de Control de Actas.</a></li>
        <li>Ingrese su usuario y contraseña.</li>
        <li>Dar clic en el botón Actas.</li>
        <li>Ubicar el acta número {{ $data->acta->no_acta }} - {{ $data->acta->year }} y dar clic en Detalles.</li>
    </ul>

    <br>
    <img src="https://greendevelopment.com.gt/wp-content/uploads/2016/02/logo-muni-guate.png" alt="">

</body>
</html>