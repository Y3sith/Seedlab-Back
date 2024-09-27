<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
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

        .verification-box {
            text-align: center;
        }

        .code-box {
            display: inline-block;
            text-align: center;
            width: 40px;
            height: 40px;
            margin: 0 5px;
            display: flex;
            font-size: 20px;
            justify-content: center;
            border: 1px solid #ccc;
            align-items: center;
        }

        .code-container {
            justify-content: center;
            align-items: center;
            text-align: center;
            margin-top: 20px;
            /* Añadido para separar los cuadros del texto */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="verification-box">
            <h1>Código de Verificación</h1>
            <h5>Estimado/a Usuario/a,</h5>
            <h5>Su código de verificación es:</h5>
            <div class="code-container">
                @foreach (str_split($verificationCode) as $number)
                    <div style="display:inline-block" class="code-box">{{ $number }}</div>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
