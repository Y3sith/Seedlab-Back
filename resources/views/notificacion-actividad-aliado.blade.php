<!-- resources/views/emails/notificacion-asesoria.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Actividad Asignada</title>
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
        <h1>Nueva Actividad</h1>



        <h3>Se te ha asignado una nueva actividad con el nombre: {{ $nombreActividad }}</h3>

        <h3 >Te invitamos a ingresar al sistema y aprovecha la oportunidad para completar el 
            contenido de la ruta, que incluye niveles, lecciones y el material de cada lección.
            Si lo prefieres, también puedes invitar a un asesor para que te ayude a finalizar
            estos detalles.

</h3>

        <div class="contenedor-boton">
            <button class="boton-personalizado">
                <a href="https://ruta.adsocidm.com/login">Ir a pagina web</a>
            </button>
        </div>

        <p class="footer">Gracias por tu colaboración.</p>
    </div>
</body>

</html>
