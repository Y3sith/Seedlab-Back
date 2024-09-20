<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>forgot password</title>
    <style>
        body {
            font-family: 'Courier New', monospace ;
            line-height: 1.6;
            color: #000;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 3px solid #00ccff;
            border-radius: 15px;
            justify-content: center;
        }

        .password-box {
            text-align: center;
        }
        .code-container {
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .password{
            font-family: Arial, sans-serif;
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="password-box">
            <h1>Restablecimiento de contraseña</h1>
            <h3>Estimado usuario,recibes este correo electrónico porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.</h3>
            <h3>Tu nueva contraseña temporal es:</h3>
            <div class="password">{{$temporaryPassword}}
                <h5>Este código expirará en 20 minutos.</h5>
                <h5>Una vez dentro debes cambiarla en la opción de "Mi perfil".</h5>
            </div>
        </div>
    </div>
</body>
</html>