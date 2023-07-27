@extends('layouts.app')
@section('content')
<div class="table-dashboard"></div>

@endsection
@section('script')
    <script>
    $(document).ready(function () {
        $.get('https://nvd.nist.gov:443/rest/public/dashboard/statistics?reporttype=countsperiodic', function (response){
console.log(response)
            var data = response.vulnPeriodicCounts;
// Crear la tabla
            var table = $('<table>').addClass('data-table');
            var headerRow = $('<tr>');
            var headerCol = $('<th>');

// Agregar los títulos de las columnas
            headerRow.append(headerCol.clone().text(''));
            headerRow.append(headerCol.clone().text('TODAY'));
            headerRow.append(headerCol.clone().text('THIS_WEEK'));
            headerRow.append(headerCol.clone().text('THIS_MONTH'));
            headerRow.append(headerCol.clone().text('LAST_MONTH'));
            headerRow.append(headerCol.clone().text('THIS_YEAR'));
            table.append(headerRow);

// Agregar los datos a la tabla
            $.each(data, function(key, values) {
                var row = $('<tr>');
                row.append($('<td>').text(key));

                $.each(values, function(index, value) {
                    row.append($('<td>').text(value.count));
                });

                table.append(row);
            });

// Agregar la tabla al documento
            $('body').append(table);
        });
    });
    </script>
@endsection
