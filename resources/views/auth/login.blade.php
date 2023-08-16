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
                <div class="brand-wrapper text-center">
                    <img src="/assets/img/logounas.png" alt="sdsd" style="max-height: 75px">
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-warning bg-warning text-light border-0 alert-dismissible fade show" role="alert">
                        {{session('error')}}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('cambio'))
                    <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                        {{session('cambio')}}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" ></button>
                    </div>
                @endif
                <div class="login-wrapper my-auto">
                    <h1 class="login-title">Sistema de Vulnerabilidades</h1>
                    <form action="{{route('login')}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="email@example.com" required>
                        </div>
                        <div class="form-group mb-4">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="******" required>
                        </div>
                        <div class="g-recaptcha mb-3" data-sitekey="6LcFkKgnAAAAAGCerl2DFgsI_Akiz4fyA3F49Pk-"></div>

                        <input name="login" id="login" class="btn btn-block login-btn" type="submit" value="Acceso">
                    </form>
                    <a href="{{route('validacion.vista')}}" class="forgot-password-link">Olvidaste tu contraseña?</a>
                </div>
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

</body>
</html>
