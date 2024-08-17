<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Emprendedores por Municipio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Reporte de Emprendedores por Municipio</h1>
    <table>
        <thead>
            <tr>
                <th>Municipio</th>
                <th>Total Emprendedores</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($emprendedores as $emprendedor)
                <tr>
                    <td>{{ $emprendedor['municipio'] }}</td>
                    <td>{{ $emprendedor['total_emprendedores'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
