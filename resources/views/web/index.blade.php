@extends('layouts.app')
@section('content')

    <section class="section">
        <div class="row">
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribución de vulnerabilidades V3</h5>

                    <!-- Pie Chart -->
                    <div id="pieChart" style="min-height: 400px;" class="echart">
                        <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    let cv3 = {!! json_encode($cveTres) !!};
                                    let datosPorCategoria3 = {!! json_encode($datosPorCategoria3) !!} ;

                                    echarts.init(document.querySelector("#pieChart")).setOption({
                                        title: {
                                            text: 'Puntaje',
                                            subtext: 'distribución',
                                            left: 'center'
                                        },
                                        tooltip: {
                                            trigger: 'item',
                                            formatter: function(params) {
                                                let nombreCategoria = params.name;
                                                let valorCategoria = params.value;
                                                let detalles = datosPorCategoria3[nombreCategoria];

                                                let tooltipContent = `${nombreCategoria}: ${valorCategoria}\n`;

                                                if (detalles && detalles.length > 0) {
                                                    tooltipContent += "<br><br><b>Detalles:</b><br>";
                                                    detalles.forEach(detalle => {

                                                        tooltipContent += `${detalle.nombre}: ${detalle.valor}<br>`;
                                                    });
                                                } else {
                                                    tooltipContent += "No hay detalles disponibles";
                                                }

                                                return tooltipContent;
                                            }
                                        },
                                        legend: {
                                            orient: 'vertical',
                                            left: 'left'
                                        },
                                        series: [{
                                            name: 'Severity',
                                            type: 'pie',
                                            radius: '50%',
                                            data: [{
                                                value: cv3[0],
                                                name: 'CRITICAL',
                                                itemStyle: {
                                                    color: '#000000'
                                                }
                                            },
                                                {
                                                    value: cv3[1],
                                                    name: 'HIGH',
                                                    itemStyle: {
                                                        color: '#e30a0a'
                                                    }
                                                },
                                                {
                                                    value: cv3[2],
                                                    name: 'MEDIUM',
                                                    itemStyle: {
                                                        color: '#fff200'
                                                    }
                                                },
                                                {
                                                    value: cv3[3],
                                                    name: 'LOW',
                                                    itemStyle: {
                                                        color: '#0CC759'
                                                    }
                                                }
                                            ],
                                            emphasis: {
                                                itemStyle: {
                                                    shadowBlur: 10,
                                                    shadowOffsetX: 0,
                                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                                }
                                            }
                                        }]
                                    });
                            });
                        </script>
                    </div>

                    <!-- End Pie Chart -->

                </div>
            </div>
        </div>
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribución de vulnerabilidades   V2 </h5>

                    <!-- Pie Chart -->
                    <div id="pieChart2" style="min-height: 400px;" class="echart">
                        <script>
                            document.addEventListener("DOMContentLoaded", () => {
                                let cv2 = {!! json_encode($cveDos) !!};
                                let datosPorCategoria2 = {!! json_encode($datosPorCategoria2) !!};

                                echarts.init(document.querySelector("#pieChart2")).setOption({
                                    title: {
                                        text: 'Puntaje',
                                        subtext: 'distribución',
                                        left: 'center'
                                    },
                                    tooltip: {
                                        trigger: 'item',
                                        formatter: function(params) {
                                            let nombreCategoria = params.name;
                                            let valorCategoria = params.value;
                                            let detalles = datosPorCategoria2[nombreCategoria];

                                            let tooltipContent = `${nombreCategoria}: ${valorCategoria}\n`;

                                            if (detalles && detalles.length > 0) {
                                                tooltipContent += "<br><br><b>Detalles:</b><br>";
                                                detalles.forEach(detalle => {

                                                    tooltipContent += `${detalle.nombre}: ${detalle.valor}<br>`;
                                                });
                                            } else {
                                                tooltipContent += "No hay detalles disponibles";
                                            }

                                            return tooltipContent;
                                        }
                                    },
                                    legend: {
                                        orient: 'vertical',
                                        left: 'left'
                                    },
                                    series: [{
                                        name: 'Severity',
                                        type: 'pie',
                                        radius: '50%',
                                        data: [{
                                            value: cv2[0],
                                            name: 'CRITICAL',
                                            itemStyle: {
                                                color: '#000000' // Cambia el color para la sección CRITICAL
                                            }
                                        },
                                            {
                                                value: cv2[1],
                                                name: 'HIGH',
                                                itemStyle: {
                                                    color: '#e30a0a' // Cambia el color para la sección CRITICAL
                                                }
                                            },
                                            {
                                                value: cv2[2],
                                                name: 'MEDIUM',
                                                itemStyle: {
                                                    color: '#fff200' // Cambia el color para la sección CRITICAL
                                                }
                                            },
                                            {
                                                value: cv2[3],
                                                name: 'LOW',
                                                itemStyle: {
                                                    color: '#0CC759' // Cambia el color para la sección CRITICAL
                                                }
                                            }
                                        ],
                                        emphasis: {
                                            itemStyle: {
                                                shadowBlur: 10,
                                                shadowOffsetX: 0,
                                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                                            }
                                        }
                                    }]
                                });
                            });
                        </script>
                    </div>

                    <!-- End Pie Chart -->

                </div>
            </div>
        </div>
        </div></section>

    <style>
        .date-range-input {
            display: flex;
            align-items: center;
        }

        .date-range-separator {
            margin: 0 10px;
        }
    </style>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Lista de vulnerabilidades</h5>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Filtro</label>
                                <div class="col-sm-2">
                                    <select class="form-select" id="filtro" name="filtro">
                                        @foreach($listaFiltros as $listaFiltro)
                                        <option class="opcionFiltro" value="{{$listaFiltro->nombre}}" {{$filtro == $listaFiltro->nombre ? 'selected' : '' }}>
                                            {{$listaFiltro->nombre}} <i class="fa-solid fa-trash"></i></option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5">

                                    <div class="date-range-input d-flex">
                                        <input type="date"  class="form-control" id="fechaInicio" name="fechaInicio" min="2023-05-01" max="{{ date('Y-m-d') }}">
                                        <span class="date-range-separator"> - </span>
                                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" min="2023-05-01" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <button class="btn btn-outline-success" id="BtnBuscarFiltro">Buscar</button>

                                </div>
                            </div>

                        <p></p>

                        <!-- Table with stripped rows -->
                        <table class="table datatable tablaDatos">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Código</th>
                                <th scope="col">Fuente</th>
                                <th scope="col">Impact</th>
                                <th scope="col">Publicado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cves as $key => $cve)
                                <tr>
                                    <th scope="row">{{$key + 1}}</th>
                                    <td>{{$cve->codigo}}</td>
                                    <td>{{$cve->sourceIdentifier}}</td>
                                    <td class="{{ $cve->metrics->isNotEmpty() ? Util::valorColor( $cve->metrics->first()->cvssData_baseScore) : 'text-secondary' }}">  <i class="fas fa-circle circle-icon " id="bolita"></i> {{ $cve->metrics->isNotEmpty() ? Util::valorTexto( $cve->metrics->first()->cvssData_baseScore) : 'UNKNOWN' }}</td>
                                    <td>{{Util::formatoFechaPersonalizado($cve->published)}}</td>
                                    <td>
                                        <button type="button" data-bs-target="#largeModal" data-bs-toggle="modal" class="btn btn-m text-primary btnVer" data-id="{{$cve->id}}"><i class="bi bi-eye-fill"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <!-- End Table with stripped rows -->

                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Large Modal -->
    <div class="modal fade" id="largeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Vulnerabilidad : #dsdsd</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Accordion without outline borders -->
                    <div class="accordion accordion-flush" id="accordionFlushExample">

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!--button type="button" class="btn btn-primary">Save changes</button-->
                </div>
            </div>
        </div>
    </div><!-- End Large Modal-->

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"> Vulnerabilidades del mes de {{Util::nombreMes($ultimoMes)}}</h5>

                <!-- Bar Chart -->
                <div id="barChart" style="min-height: 400px;" class="echart">
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            let nombreFiltros = {!! json_encode($arregloFiltros) !!};
                            let resultadoFiltros = {!! json_encode($resultadoFiltros) !!};
                            let colores = [
                                '#FF5733', '#FFC300', '#8AFF33', '#33FFD1', '#336BFF', '#FF33A8', '#FF3333', '#33FF33', '#33FFFF', '#FF336B',
                                '#FF33FF', '#FF7733', '#33FFA8', '#33FF77', '#A833FF', '#33A8FF', '#FFD133', '#33D1FF', '#FFD166', '#A833A8'
                            ]; // Paleta de colores

                            let dataSeries  = resultadoFiltros.map((value, index) => {
                                return {
                                    value: value,
                                    itemStyle: {
                                        color: colores[index] // Asignar color según posición en el arreglo
                                    }
                                };
                            });

                            echarts.init(document.querySelector("#barChart")).setOption({
                                xAxis: {
                                    type: 'category',
                                    data: nombreFiltros
                                },
                                yAxis: {
                                    type: 'value'
                                },
                                series: [{
                                    data: dataSeries ,
                                    type: 'bar'
                                }],
                                tooltip: {  // Agrega la configuración del tooltip
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow'
                                    }
                                }
                            });
                        });
                    </script>
                </div>

                <!-- End Bar Chart -->

            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Últimos 20 identificadores de vulnerabilidad puntuados y resúmenes</h5>

            <!-- Table with hoverable rows -->
            <table class="table datatable">
                <thead>
                <tr>

                    <th scope="col">Código</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Versión</th>
                    <th scope="col">CVSS Severity</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cvesUltimos as $cve)
                <tr>

                    <td>{{$cve->codigo}}</td>
                    <td>{{$cve->descriptions->first()->value}}</td>
                    <td>{{$cve->metrics->isNotEmpty() ?  $cve->metrics->first()->cvssData_version : '#' }}</td>
                    <td class="{{$cve->metrics->isNotEmpty() ? (Util::valorColor( $cve->metrics->first()->cvssData_baseScore)) :'text-secondary'}}">
                        <label class="  text-white btn {{$cve->metrics->isNotEmpty() ? (Util::valorColorButton( $cve->metrics->first()->cvssData_baseScore)) :'btn-secondary'}}" >

                                {{$cve->metrics->isNotEmpty() ? (Util::valorTexto( $cve->metrics->first()->cvssData_baseScore)) :'UNKNOWN'}}
                                {{$cve->metrics->isNotEmpty() ?  $cve->metrics->first()->cvssData_baseScore :''}}


                        </label>

                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>

            <!-- End Table with hoverable rows -->

        </div>
    </div>


@endsection
@section('script')
    <script>
        $('.tablaDatos').on('click','.btnVer',function (){
            let id = $(this).data('id');
            let url = `/service/vulnerabilidad/${id}`;
            $.get(url, function (response){
                if (response.estado == 'ok'){
                    $('#tituloModal').html('Vulnerabilidad : '+ response.code );
                    $('#accordionFlushExample').html(response.html);
                }
            });
            $('#modalDatos').modal('show');
        });


        $('#BtnBuscarFiltro').click(function (){
            let opcionSeleccionada = $('#filtro').val();
            var fechaInicio = $('#fechaInicio').val();
            var fechaFin = $('#fechaFin').val();
            let url = '/home?filtro='+opcionSeleccionada;
            if (fechaInicio && fechaFin) {
                // Convertir las fechas en objetos Date
                var dateInicio = new Date(fechaInicio);
                var dateFin = new Date(fechaFin);
                if (dateInicio <= dateFin) {
                    url+=`&&inicio=${fechaInicio}&&fin=${fechaFin}`;
                    window.location.href = url;
                } else {
                    toastr.warning('La fecha fin no puede ser menor que la fecha inicio','Error',{"progressBar":true})
                }
            }else {
                window.location.href = url;
            }
        });

    </script>
@endsection
