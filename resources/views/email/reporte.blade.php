<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Actualización:</h2>
    <table>
        <thead>
        <tr>
            <th> Código CVE</th>
            <th>Descripción</th>
        </tr>
        </thead>
        <tbody>


        @foreach($mailData['listas'] as $lista)
            <tr>
                <td>{{$lista['id']}}</td>
                <td>{{$lista['descripcion']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

</body>
</html>
