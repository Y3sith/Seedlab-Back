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
    <h1>Reporte Empresas</h1>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; text-align: left;">Documento</th>
                <th style="padding: 10px; text-align: left;">Nombre Empresa</th>
                <th style="padding: 10px; text-align: left;">Razon Social</th>
                <th style="padding: 10px; text-align: left;">Pagína Web</th>
                <th style="padding: 10px; text-align: left;">Celular</th>
                <th style="padding: 10px; text-align: left;">Dirección</th>
                <th style="padding: 10px; text-align: left;">Correo</th>
                <th style="padding: 10px; text-align: left;">Departamento</th>
                <th style="padding: 10px; text-align: left;">Documento Emprendedor</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $dato)
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;">{{ $dato['documento'] }}</td>
                    <td style="padding: 10px;">{{ $dato['nombre'] }}</td>
                    <td style="padding: 10px;">{{ $dato['razonSocial'] }}</td>
                    <td style="padding: 10px;">{{ $dato['url_pagina'] }}</td>
                    <td style="padding: 10px;">{{ $dato['celular'] }}</td>
                    <td style="padding: 10px;">{{ $dato['direccion'] }}</td>
                    <td style="padding: 10px;">{{ $dato['correo'] }}</td>
                    <td style="padding: 10px;">{{ $dato['name'] }}</td>
                    <td style="padding: 10px;">{{ $dato['documento_emprendedor'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
