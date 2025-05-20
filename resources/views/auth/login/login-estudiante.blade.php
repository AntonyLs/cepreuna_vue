<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CepreUNA">
    <meta name="author" content="OTI">
    <meta name="keyword" content="Cepreuna">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}" />
    <title>{{ config('app.name', 'CepreUNA') }}</title>

    <!-- Main CepreUNA styles -->
    <link href="{{ asset('css/cepre-coreui.css') }}" rel="stylesheet">
    <style>
        .bg-primary {
            background-color: #10518a!important;
        }
        .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
            color: #fff;
            background-color: #10518a;
            border-color: #094070;
        }
        .btn-primary:not(:disabled):not(.disabled):active:focus, .show>.btn-primary.dropdown-toggle:focus {
            box-shadow: 0 0 0 0.2rem rgb(65 224 195 / 50%);
        }
        @media (hover: hover), not all{
            .btn-primary:hover {
                color: #fff;
                background-color: #10518a;
                border-color: #094070;
            }
        }
        .btn-primary {
            color: #fff;
            background-color: #10518a;
            border-color: #094070;
        }
    </style>
  </head>
  <body class="c-app flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card-group">
            <div class="card p-4">
              <div class="card-body text-center">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <img src="{{asset('images/logo.png')}}" style="width: 40%;">
                    </div>
                <h1>Iniciar Sesión</h1>
                <p class="text-muted">Ingrese mediante su cuenta Institucional</p>

                <div class="input-group mb-3">

                        <a name="" id="" class="btn btn-danger" style="margin: 0 auto;" href="{{ url('estudiante/login/google') }}" role="button">
                        <i class="fab fa-google"></i> <span class="border-left m-1 p-2"> Ingresar con Google </span>
                        </a>
                </div>
                @if (session('status'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
              </div>
            </div>
            <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
              <div class="card-body text-center">
                <div>
                  <!--<h2>Regístrate</h2>-->
                  <img src="{{asset('images/logo-unap.png')}}" style="width: 30%;">
                  <p>
                    Universidad Nacional del Altiplano <br>
                    <small>Centro Pre Universitario</small>
                  </p>
                  <!--<button class="btn btn-lg btn-outline-light mt-3" type="button">Register Now!</button>-->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/cepre-coreui.js') }}"></script>

  </body>
</html>
