<!-- resources/views/emails/notificacion-asesoria.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Asesoría Asignada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
        }
        ul {
            padding-left: 20px;
        }
        .footer {
            margin-top: 20px;
            font-style: italic;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Nueva Asesoría Asignada para {{ $tipoDestinatario }}</h1>
        
        <p>Hola {{ $destinatario->auth->name }},</p>
        
        <p>Se te ha asignado una nueva asesoría con los siguientes detalles:</p>
        
        <ul>
            <li>Nombre de la solicitud: {{ $asesoria->Nombre_sol }}</li>
            <li>Fecha: {{ $asesoria->fecha }}</li>
            <li>Emprendedor: {{ $emprendedor->nombre }}</li>
        </ul>
        
        <p>Por favor, revisa tu agenda y prepárate para la asesoría.</p>
        
        <p class="footer">Gracias por tu colaboración.</p>
    </div>
</body>
</html>