<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificacion por Correo</title>

    <style type="text/css">
    body {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        background-color: #f6f6f6;
    }
    .content {
        background-color: white;
        padding: 25px;
        width: 90%;
        margin-left: auto;
        margin-right: auto;
        border-radius: 10px;

    }
    .body{
        display: inline-block;
        line-height: 1.7;
        border-top: 1px solid #ccc;
        margin-top: 15px;
        padding-top: 15px;
    }
    .body p {
        text-align: justify;
    }

    .footer {
        color: #868686;
        font-size: 12px;
        margin-top: 20px;
    }
    </style>
</head>
<body>
    <div class="content">
        <div class="body">
            Estimado(a) <b>{{$nombres}}</b>
            <br>
            <p>Bienvenido al servicio de GSuite:</p>
            <p>Para acceder a su cuenta debera usar el siguiente acceso:</p>
            <p><b>usuario</b></p>
            <p>{{ $usuario }}</p>
            <p><b>contraseña</b></p>
            <p>{{ $password }}</p>

            <b>Nota</b>:
            <p>Se recomienda cambiar su contraseña por defecto por una mas segura.</p>
        </div>
    </div>
    <div class="footer"><h5> <i>"El Mejor Regalo es Cuidarnos"</i></h5></div>
</body>
</html>
