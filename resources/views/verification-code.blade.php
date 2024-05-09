<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #000;
            /* Color de texto negro */
            text-align: center;
            /* Centrar todo el contenido */
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 15px;
            justify-content: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 25px;
            text-align: center;
        }

        h5{
            font-size: 14px;
            margin-top: -25px;
            text-align: center;
        }
        h2{
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
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }



        strong {
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Código de Verificación</h1> <!-- Texto del título centrado -->
        <h5>Estimado/a Usuario/a,</h5>
        <h5 >Su código de verificación es:</h5>
        <div class="code-container">
            @foreach (str_split($verificationCode) as $number)
                <h2 class="code-box">{{ $number }}</h2>
            @endforeach
        </div>
    </div>
</body>

</html>