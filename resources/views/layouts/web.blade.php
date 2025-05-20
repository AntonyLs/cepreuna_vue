<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;1,700;1,900&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/images/logo.ico') }}" />

    <!-- css -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/main.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous">
    </script>

    <!-- Styles -->
    @yield('css')
    <style>
        html,
        body {
            background-color: #fff;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            margin: 0;
        }

        .w-text-info {
            font-size: 12px;
            text-align: justify;
        }

        .custom-file-label::after {
            content: "Buscar";
        }

        .custom-file-input:lang(en)~.custom-file-label::after {
            content: "Buscar";
        }

    </style>
</head>

<body>
    <header>
        <div class="navigation">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12  text-center">
                        {{-- <div class="float-left"> --}}
                        <img src="{{ asset('images/logo.png') }}" class="logo" alt="">
                        <div class="text-center" style="vertical-align: bottom; margin-left: 10px;">
                            <h5 class="font-weight-bold text-dark" style="margin:0;">Universidad Nacional del Altiplano
                            </h5>
                            <p class="text-gray" style="margin:0; font-size: 15px;">Centro de Estudios Preuniversitario</p>
                        </div>
                        {{-- </div> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="line-header bg-secondary" style="height: 30px">

        </div>
    </header>
    <section class="">
        @yield('content')

    </section>

    <footer class="bg-secondary" style="width: 100%;">
        <div class="bg-dark">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="text-white text-center"><small> © Universidad Nacional del Altiplano, Puno - Perú,
                                2020.</small></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="text-white text-center"><small>Oficina de Tecnologías de Información</small></p>

                </div>
            </div>
        </div>
    </footer>

    <div class="loader">
        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>

    </div>
    {{-- <div id="loader" style="background: rgba(0,0,0,0.75) url({{ asset('images/loading.gif') }}) no-repeat center center;"></div> --}}
    <script src="{{ asset('js/app.js?v=1.21082519') }}"></script>
    <script src="{{ asset('js/web/main.js') }}"></script>
    @yield('scripts')
</body>

</html>
