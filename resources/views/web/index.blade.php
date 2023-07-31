@extends('layouts.app')
@section('content')

    <section class="section">
        <div class="row">
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CVSS V3 </h5>

                    <!-- Pie Chart -->
                    <div id="pieChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            let cv3 = {!! $cveTres!!};
                            console.log(cv3);
                            echarts.init(document.querySelector("#pieChart")).setOption({
                                title: {
                                    text: 'Score',
                                    subtext: 'Distribution',
                                    left: 'center'
                                },
                                tooltip: {
                                    trigger: 'item'
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
                                            color: '#000000' // Cambia el color para la sección CRITICAL
                                        }
                                    },
                                        {
                                            value: cv3[1],
                                            name: 'HIGH',
                                            itemStyle: {
                                                color: '#e30a0a' // Cambia el color para la sección CRITICAL
                                            }
                                        },
                                        {
                                            value: cv3[2],
                                            name: 'MEDIUM',
                                            itemStyle: {
                                                color: '#fff200' // Cambia el color para la sección CRITICAL
                                            }
                                        },
                                        {
                                            value: cv3[3],
                                            name: 'LOW',
                                            itemStyle: {
                                                color: '#605c5c' // Cambia el color para la sección CRITICAL
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
                    <!-- End Pie Chart -->

                </div>
            </div>
        </div>
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CVSS V2 </h5>

                    <!-- Pie Chart -->
                    <div id="pieChart2" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            let cv2 = {!! $cveDos !!};
                            console.log(cv2);
                            echarts.init(document.querySelector("#pieChart2")).setOption({
                                title: {
                                    text: 'Score',
                                    subtext: 'Distribution',
                                    left: 'center'
                                },
                                tooltip: {
                                    trigger: 'item'
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
                                                color: '#605c5c' // Cambia el color para la sección CRITICAL
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
                    <!-- End Pie Chart -->

                </div>
            </div>
        </div>
        </div></section>



    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Last 20 Scored Vulnerability IDs & Summaries</h5>

            <!-- Table with hoverable rows -->
            <table class="table table-hover">
                <thead>
                <tr>

                    <th scope="col">Código</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Versión</th>
                    <th scope="col">CVSS Severity</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cves as $cve)
                <tr>

                    <td>{{$cve->codigo}}</td>
                    <td>{{$cve->descriptions->first()->value}}</td>
                    <td>{{$cve->metrics->isNotEmpty() ?  $cve->metrics->first()->cvssData_version : '#' }}</td>
                    <td class="{{$cve->metrics->isNotEmpty() ? (Util::valorColor( $cve->metrics->first()->cvssData_baseScore)) :'text-secondary'}}">
                        <label class="btn {{$cve->metrics->isNotEmpty() ? (Util::valorColorButton( $cve->metrics->first()->cvssData_baseScore)) :'btn-secondary'}}">
                            {{$cve->metrics->isNotEmpty() ?  $cve->metrics->first()->cvssData_baseScore :''}}
                            {{$cve->metrics->isNotEmpty() ? (Util::valorTexto( $cve->metrics->first()->cvssData_baseScore)) :'Unknown'}}
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

@endsection
