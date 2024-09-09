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
    <h1>Reporte Asesorias</h1>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; text-align: left;">ID</th>
                <th style="padding: 10px; text-align: left;">Nombre Emprendedor</th>
                <th style="padding: 10px; text-align: left;">Documento Emprendedor</th>
                <th style="padding: 10px; text-align: left;">Nombre Asesoria</th>
                <th style="padding: 10px; text-align: left;">Descripción</th>
                <th style="padding: 10px; text-align: left;">Fecha</th>
                <th style="padding: 10px; text-align: left;">Nombre Aliado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $dato)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">{{ $dato['id'] }}</td>
                    <td style="padding: 10px;">{{ $dato['nombre_emprendedor'] }}</td>
                    <td style="padding: 10px;">{{ $dato['documento'] }}</td>
                    <td style="padding: 10px;">{{ $dato['Nombre_sol'] }}</td>
                    <td style="padding: 10px;">{{ $dato['notas'] }}</td>
                    <td style="padding: 10px;">{{ $dato['fecha'] }}</td>
                    <td style="padding: 10px;">{{ $dato['nombre_aliado'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
