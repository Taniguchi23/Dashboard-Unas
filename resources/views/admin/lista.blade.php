@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Usuarios</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">Inicio</a></li>
                <li class="breadcrumb-item">Configuraciones</li>
                <li class="breadcrumb-item active">Usuarios</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <div class="col-lg-6">
        @if (session('mensaje'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{session('mensaje')}}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row justify-content-between">
                                <div class="col-4">
                                    <h5 class="card-title mt-2 ml-2">Lista de usuarios</h5>
                                </div>

                                    <div class="mt-4 col-2">
                                        <button class="btn btn-success btnCrear" data-bs-toggle="modal" data-bs-target="#modalDatos">Crear Usuario</button>
                                    </div>

                            </div>
                        </div>

                        <!-- Table with stripped rows -->
                        <table class="table datatable" id="tabla">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($usuarios as $key => $usuario)
                                <tr>
                                    <th scope="row">{{$key+1}}</th>
                                    <td>{{$usuario->name}}</td>
                                    <td>{{$usuario->email}}</td>
                                    <td>{{$usuario->getRol()}}</td>
                                    <td><span class="rounded-3 p-1 bg-{{Util::estadoColor($usuario->state)}} text-white">{{Util::estadoTexto($usuario->state)}}</span></td>
                                    <td>
                                        <button type="button" data-bs-target="#modalDatos" data-bs-toggle="modal" class="btn btn-m text-primary btnEditar" data-id="{{$usuario->id}}"><i class="bi bi-pencil-square"></i></button>
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
                <form action="{{route('admin.usuario.store')}}" method="post" id="formulario">
                    @csrf
                    <div class="modal-body">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="validationDefault01" class="form-label">Nombres</label>
                                    <input type="text" class="form-control" id="name"  name="name" value="" required>
                                </div>
                                <div class="col-md-12">
                                    <label for="validationDefaultUsername" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text" id="inputGroupPrepend2">@</span>
                                        <input type="email" class="form-control" id="email" name="email" aria-describedby="inputGroupPrepend2" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="validationDefault05" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationDefault05" class="form-label">Rol</label>
                                    <select class="form-select" name="rol" id="rol">
                                        <option value="A">Administrador</option>
                                        <option value="U">Usuario</option>
                                    </select>
                                </div>
                                <div id="divEstado" class="col-md-6" style="display: none">
                                    <label for="validationDefault05" class="form-label">Estado</label>
                                    <select class="form-select" name="estado" id="estado">
                                        <option value="A">Activo</option>
                                        <option value="I">Inactivo</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary btnGuardar" >Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        var valor_id = -1;
        $('#tabla').on('click','.btnEditar',function (){
            let val_id = $(this).data('id');
            valor_id = val_id;
            let val_url = '/admin/usuario/edit/'+val_id;
            let val_url_update = '/admin/usuario/update/'+val_id;
            $.get(val_url, function (res){
                $('#formulario').attr('action',val_url_update);
                $('#modalTitulo').html('Editar Usuario');
                $('#name').val(res.name);
                $('#email').val(res.email);
                $('#rol').val(res.rol);
                $('#divEstado').css('display','block');
                $('#estado').val(res.state);
                $('#password').attr('required',false);
                $('.btnGuardar').html('Editar').prop('disabled',false);
                $('#modalDatos').modal('show');
            });
        });


        $('.btnCrear').click(function (){
            let val_url_store = '/admin/usuario/store';
            $('#formulario').attr('action',val_url_store);
            $('#modalTitulo').html('Crear Usuario');
            $('#name').val('');
            $('#email').val('');
            $('#divEstado').css('display','none');
            $('#password').attr('required',true);
            $('#rol').val(val_tipo);
            $('.btnGuardar').html('Guardar').prop('disabled',true);
            $('#modalDatos').modal('show');
        });

        $('#email').keyup(function (){

            let email = $(this).val();
            let url = `/service/verificarEmail/${email}`
            $.get(url, function (response) {
                console.log(response)
                if (response.response == 'error'){
                    $('.btnGuardar').prop('disabled',false);
                }else {
                    $('.btnGuardar').prop('disabled',true);
                    if (valor_id == response.id){
                        $('.btnGuardar').prop('disabled',false);
                    }

                }

            });
        });
    </script>
@endsection
