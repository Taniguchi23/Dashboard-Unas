@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Filtros</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
                <li class="breadcrumb-item">Configuraciones</li>
                <li class="breadcrumb-item active">Filtros</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <h5 class="card-title mt-2 ml-2">Lista de filtros</h5>
                                </div>

                                <div class="mt-4 col-2">
                                    <button class="btn btn-success btnCrear"  data-bs-toggle="modal" data-bs-target="#modalDatos">Crear Filtro</button>
                                </div>

                            </div>
                        </div>

                        <!-- Table with stripped rows -->
                        <table class="table datatable" id="tabla">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Filtro</th>
                                <th scope="col">Orden</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($filtros as $key => $filtro)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{$filtro->nombre}}</td>
                                    <td>{{$filtro->orden}}</td>
                                    <td><span class="rounded-3 p-1 bg-{{Util::estadoColor($filtro->estado)}} text-white">{{Util::estadoTexto($filtro->estado)}}</span></td>
                                    <td>
                                        <button type="button" data-bs-target="#modalDatos" data-bs-toggle="modal" class="btn btn-m text-primary btnEditar" data-id="{{$filtro->id}}"><i class="bi bi-pencil-square"></i></button>
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

    <!-- Modal -->
    <div class="modal fade" id="modalDatos" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('admin.filtro.store')}}" method="post" id="formulario">
                    @csrf
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="validationDefault01" class="form-label">Filtro</label>
                                    <input type="text" class="form-control" id="nombre"  name="nombre" value="" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="validationDefault01" class="form-label">Orden</label>
                                    <input type="number" class="form-control" id="orden"  name="orden" value="" required>
                                </div>

                                <div id="divEstado" class="col-md-6" style="display: none">
                                    <label for="validationDefault05" class="form-label">Estado</label>
                                    <select class="form-select" name="estado" id="estado">
                                        <option value="A">Activo</option>
                                        <option value="I">Inactivo</option>
                                    </select>
                                </div>
                                <input type="hidden" value="" name="rol" id="rol">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary btnGuardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">

        $('#tabla').on('click','.btnEditar',function (){
            let val_id = $(this).data('id');
            let val_url = '/admin/filtro/edit/'+val_id;
            let val_url_update = '/admin/filtro/update/'+val_id;
            $.get(val_url, function (res){
                console.log(res)
                $('#formulario').attr('action',val_url_update);
                $('#modalTitulo').html('Editar Filtro');
                $('#nombre').val(res.nombre);
                $('#orden').val(res.orden);
                $('#divEstado').css('display','block');
                $('#estado').val(res.estado);
                $('.btnGuardar').html('Editar');
                $('#modalDatos').modal('show');
            });
        });


        $('.btnCrear').click(function (){
            let val_url_store = '/admin/filtro/store';
            $('#formulario').attr('action',val_url_store);
            $('#modalTitulo').html('Crear Filtro');
            $('#nombre').val('');
            $('#orden').val('');
            $('#divEstado').css('display','none');
            $('.btnGuardar').html('Guardar');
            $('#modalDatos').modal('show');
        });
    </script>
@endsection
