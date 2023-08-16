<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        .content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Recuperación de Contraseña</h1>
    </div>
    <div class="content">
        <p>Hola {{$mailData['nombre']}},</p>
        <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Si no realizaste esta solicitud, puedes ignorar este correo electrónico.</p>
        <p>Si deseas restablecer tu contraseña, por favor haz clic en el siguiente enlace:</p>
        <a href="{{$mailData['url']}}" target="_blank">Restablecer Contraseña</a>
        <p>Si el enlace no funciona, puedes copiar y pegar la siguiente URL en tu navegador:</p>
        <p>{{$mailData['url']}}</p>
        <p>Si tienes alguna pregunta, no dudes en ponerte en contacto con nuestro equipo de soporte.</p>
    </div>
    <div class="footer">
        <p>Este correo electrónico es generado automáticamente. Por favor, no respondas a este mensaje.</p>
    </div>
</div>
</body>
</html>
