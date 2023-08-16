<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RESEGTI - Sistema de vulnerabilidades</title>
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>
<body>
<main>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 login-section-wrapper">
                <div class="brand-wrapper text-center mb-3">
                    <img src="/assets/img/logounas.png" alt="sdsd" style="max-height: 75px">
                </div>
                @if(session('mensaje'))
                    <div class="alert alert-success">
                        {{ session('mensaje') }}
                    </div>
                @endif
                @if(!$estado)
                    <div class="alert alert-success m-5">
                        El token es invalido o ha caducado.
                    </div>
                @else
                <div class="login-wrapper my-auto">
                    <h3>Hola, {{$name}}  </h3>
                    <h1 class="login-title">Cambiar contraseña</h1>
                    <form action="{{route('validacion.cambiar',['id' => $id])}}" method="post">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="password">Nueva contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="******" required>
                            <span class="password_error text-danger"></span>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password">Confirmar contraseña</label>
                            <input type="password" name="password2" id="confirm_password" class="form-control" placeholder="******" required>
                            <span class="password_error text-danger"></span>
                        </div>
                        <input name="login" id="login" class="btn btn-block login-btn" type="submit" value="Cambiar Contraseña" disabled>
                    </form>
                </div>
                @endif
            </div>
            <div class="col-sm-6 px-0 d-none d-sm-block">
                <img src="/assets/img/fiis3.jpg" alt="login image" class="login-img">
            </div>
        </div>
    </div>
</main>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('#confirm_password').on('keyup', function() {
            var password = $('#password').val();
            var confirm_password = $(this).val();
            console.log('p',password)
            console.log('c',confirm_password)
            if (password === confirm_password) {
                $('.password_error').html('');
                $('#login').attr('disabled',false);
            } else {
                $('.password_error').html('Las contraseñas no coinciden');
            }
        });
    });
</script>

</body>
</html>
