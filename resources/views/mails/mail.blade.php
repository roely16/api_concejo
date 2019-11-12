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
    <h3>Se le ha asignado la agenda de fecha {{ $data->agenda->fecha }} para su revisión y aprobación.</h3>
    <h3>Para acceder a la agenda siga los siguientes pasos: </h3>

    <ul>
        <li>Dar clic en el siguiente enlace <a href="https://udicat.muniguate.com/apps/appConcejo/">Sistema de Control de Actas.</a></li>
        <li>Ingrese su usuario y contraseña.</li>
        <li>Dar clic en el botón Agendas.</li>
        <li>Ubicar la agenda con fecha {{ $data->agenda->fecha }} y dar clic en Detalles.</li>
        <li>Dar clic en el botón Puntos.</li>
    </ul>

    <br>
    <img src="https://greendevelopment.com.gt/wp-content/uploads/2016/02/logo-muni-guate.png" alt="">

</body>
</html>