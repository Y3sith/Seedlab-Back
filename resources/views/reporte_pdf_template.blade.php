<!DOCTYPE html>
<html>

<head>
    <title>Reporte PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Reportes</h1>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; text-align: left;">ID</th>
                <th style="padding: 10px; text-align: left;">Email</th>
                <th style="padding: 10px; text-align: left;">Fecha Registro</th>
                <th style="padding: 10px; text-align: left;">Estado</th>
                <th style="padding: 10px; text-align: left;">Nombre</th>
                <th style="padding: 10px; text-align: left;">Apellido</th>
                <th style="padding: 10px; text-align: left;">Documento</th>
                <th style="padding: 10px; text-align: left;">N° Celular</th>
                <th style="padding: 10px; text-align: left;">Genero</th>
                <th style="padding: 10px; text-align: left;">Fecha de Nacimiento</th>
                <th style="padding: 10px; text-align: left;">Dirección</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $dato)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">{{ $dato['id'] }}</td>
                    <td style="padding: 10px;">{{ $dato['email'] }}</td>
                    <td style="padding: 10px;">{{ $dato['fecha_registro'] }}</td>
                    <td style="padding: 10px;">{{ $dato['estado'] }}</td>
                    <td style="padding: 10px;">{{ $dato['nombre'] }}</td>
                    <td style="padding: 10px;">{{ $dato['apellido'] }}</td>
                    <td style="padding: 10px;">{{ $dato['documento'] }}</td>
                    <td style="padding: 10px;">{{ $dato['celular'] }}</td>
                    <td style="padding: 10px;">{{ $dato['genero'] }}</td>
                    <td style="padding: 10px;">{{ $dato['fecha_nac'] }}</td>
                    <td style="padding: 10px;">{{ $dato['direccion'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
