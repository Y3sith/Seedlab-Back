<!-- resources/views/emails/notificacion-asesoria.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asesoría Asignada</title>
    <style>
        body {
            font-family: 'Courier New', monospace ;
            line-height: 1.6;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 15px;
            border: 3px solid #00ccff;
        }

        h1 {
            text-align: center;
        }

        ul {
            padding-left: 20px;
        }
        li{
            font-size: 14px;
        }

        .footer {
            margin-top: 20px;
            font-style: italic;
            color: #7f8c8d;
        }

        .contenedor-boton {
            text-align: center;
            /* Centra el botón horizontalmente */
        }

        .boton-personalizado {
            background-color: #00ccff;
            /* Color naranja */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: inline-block;
            /* El botón se ajusta al contenido */
        }

        .boton-personalizado a {
            color: white;
            text-decoration: none;
        }

        .boton-personalizado:hover {
            background-color: #00ccff;
            /* Un poco más oscuro al pasar el ratón */
        }

    </style>
</head>

<body>
    <div class="container">
        <h1>Asesoría Asignada</h1>
        <h3>Hola, {{$emprendedor->nombre}} {{$emprendedor->apellido}}</h3>
        <h3>Tu asesoria ya fue asignada con los siguientes detalles:</h3>

        <ul>
            <li>Nombre de la solicitud: {{ $asesoria->Nombre_sol }}</li>
            <li>Fecha de solicitud: {{ $asesoria->fecha }}</li>
            <li>Fecha y hora de encuentro: {{ $horarioAsesoria->fecha }}</li>
            <li>Nombre del asesor: {{ $asesor->nombre }} {{ $asesor->apellido }}</li>
            <li>Detalles de la asesoria: {{ $horarioAsesoria->observaciones }}</li>

        </ul>

        <h3 >¡Prepárate para tu asesoría! Asegúrate de tener todas tus preguntas listas para aprovechar al máximo la sesión y resolver cualquier duda que tengas. Estamos aquí para ayudarte a alcanzar tus objetivos.</h3>

        <div class="contenedor-boton">
            <button class="boton-personalizado">
                <a href="https://ruta.adsocidm.com/login">Ir a pagina web</a>
            </button>
        </div>

        <p class="footer">Gracias por tu colaboración.</p>
    </div>
</body>

</html>
