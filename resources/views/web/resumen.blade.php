@extends('layouts.app')
@section('link')
    <!-- Agrega Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

@endsection
@section('content')
    <div class="pagetitle">
        <h1>Vulnerabilidades</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>

                <li class="breadcrumb-item active">Vulnerabilidades</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->


    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Lista de vulnerabilidades</h5>
                        <form action="{{route('web.vulnerabilidadesPost')}}" method="post">
                            @csrf
                            <input type="text" name="filtro" value="{{ session('filtro') }}">
                            <button type="submit" class="  btn btn-primary" value=""> Buscar</button>
                        </form>

                        <p></p>

                        <!-- Table with stripped rows -->
                        <table class="table tablaDatos ">
                            <thead>
                            <tr>

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

                                <td>{{$cve->codigo}}</td>
                                <td>{{$cve->sourceIdentifier}}</td>
                                <td class="{{ $cve->metrics->isNotEmpty() ? Util::valorColor( $cve->metrics->first()->cvssData_baseScore) : 'text-secondary' }}">  <i class="fas fa-circle circle-icon " id="bolita"></i> {{ $cve->metrics->isNotEmpty() ? Util::valorTexto( $cve->metrics->first()->cvssData_baseScore) : 'Unknown' }}</td>
                                <td>{{Util::formatoFechaPersonalizado($cve->published)}}</td>
                                <td>
                                    <button type="button" data-bs-target="#largeModal" data-bs-toggle="modal" class="btn btn-m text-primary btnVer" data-id="{{$cve->id}}"><i class="bi bi-eye-fill"></i></button>

                                </td>
                            </tr>
                            @endforeach
                            </tbody>

                        </table>

                        {!! $cves->appends(['filtro' => $filtro])!!}
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

    </div>
@endsection
@section('script')
    <script>
        $('.tablaDatos').on('click','.btnVer',function (){
            let id = $(this).data('id');
            let url = `/service/vulnerabilidad/${id}`;
            $.get(url, function (response){
                console.log(response)
                if (response.estado == 'ok'){
                    $('#tituloModal').html('Vulnerabilidad : '+ response.code );
                    $('#accordionFlushExample').html(response.html);
                }
            });
            $('#modalDatos').modal('show');
        });
    </script>
@endsection

