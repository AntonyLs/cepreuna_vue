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
    .head {
        float: right;
    }
    .head img {
        width: 50px;
    }
    .title{
        display: inline-block;
        margin-left: 10px;
        text-align: center;
    }
    .footer{
        text-align: center;
    }
    .btn {
        text-align: center;
        margin-top: 30px;
    }
    .btn-form {
        margin-top: 20px;
        padding: 10px;
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
            Estimado(a) <b>{{$nombres}}</b> para realizar el tramite de su pago debera ingresar a https://app.cepreuna.edu.pe/tramite-pago/home, con los accesos indicados a continuación. En dicha plataforma observará los documentos solicitados por la oficina de Administración que usted deberá adjuntar para su revisión y aprobación o corrección según corresponda, a través de la misma podrá realizar el seguimiento de su expediente y recibir las indicaciones por parte de los responsables.
            <br>
            <p>Para acceder a su cuenta usar el siguiente acceso:</p>
            <p><b>Correo</b></p>
            <p>{{ $usuario }}</p>
            <p><b>contraseña</b></p>
            <p>{{ $password }}</p>

            <b>Nota</b>:
            <p>Se recomienda atender a lo solicitado a la brevedad para agilizar el tramite de su pago.</p>
        </div>
    </div>
    <div class="footer"><h5> <i>"El Mejor Regalo es Cuidarnos"</i></h5></div>
</body>
</html>
