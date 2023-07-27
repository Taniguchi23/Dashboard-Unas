@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Notificaciones</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('index')}}">Inicio</a></li>
                <li class="breadcrumb-item active">Notificación y alertas</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Formulario de notificación</h5>
                        <form>
                            @csrf
                            <div class="row mb-3">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email">
                                </div>
                            </div>
                            <div class="text-primary" id="respuesta"></div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary btnSuscribirse">Suscribirse</button>
                                <button type="button" class="btn btn-secondary">Reiniciar</button>
                            </div>
                        </form><!-- End Horizontal Form -->

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        $(document).ready(function (){
            $('.btnSuscribirse').click(function (){
                let email = $('#email').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });
                $.post({
                    data: {
                        email:email,
                    },
                    url:   '/service/suscribirse',
                    type:  'POST',
                    beforeSend: function(){
                        $("#respuesta").html("Procesando, espere por favor...");
                        $('.btnSuscribirse').css('opacity', 0.6).prop('disabled', true).val('Enviando...');
                    },
                    success:  function (response) {
                        $("#respuesta").html('');
                        $('.btnSuscribirse').css('opacity', 1).prop('disabled', false).val('Enviar');
                        if (response === 'ok'){
                            toastr.success('Se ha suscrito satisfactoriamente','Suscripción exitosa',{"progressBar": true});
                        }else {
                            toastr.warning('Ya se ha suscrito','Error',{"progressBar":true})
                        }
                    }
                });

            });
        });
    </script>
@endsection
