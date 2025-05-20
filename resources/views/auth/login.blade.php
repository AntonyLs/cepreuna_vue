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

  </head>
  <body class="c-app flex-row align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card-group">
            <div class="card p-4">
              <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                <h1>Iniciar Sesión</h1>
                <p class="text-muted">Ingrese sus credenciales</p>
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text">
                      <svg class="c-icon">
                        <use xlink:href="coreui/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                      </svg></span></div>
                  <input class="form-control" type="text" placeholder="Email" id="email" name="email" required>
                  @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                </div>
                <div class="input-group mb-4">
                  <div class="input-group-prepend"><span class="input-group-text">
                      <svg class="c-icon">
                        <use xlink:href="coreui/vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                      </svg></span></div>
                  <input class="form-control" type="password" placeholder="Contraseña" id="password" name="password">
                </div>
                <div class="row">
                  <div class="col-6">
                    <button class="btn btn-primary px-4" type="submit">Entrar</button>
                  </div>
                </form>
                @if (Route::has('password.request'))
                  <div class="col-6 text-right">
                    <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                  </div>
                  @endif
                </div>
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
