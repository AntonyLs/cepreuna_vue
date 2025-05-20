<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="CepreUNA">
    <meta name="author" content="OTI">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}" />
    <title>CepreUNA @yield('titulo')</title>

    <!-- Main CepreUNA styles -->
    <link href="{{ asset('css/cepre-coreui.css') }}" rel="stylesheet">
    <style>
        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.75) no-repeat center center;
            z-index: 10000;
        }

        .spinner-grow {
            top: 40%;
            left: 50%;
            position: fixed;
            background-color: #39f;
        }

        @media (hover: hover),
        not all {

            .c-sidebar .c-sidebar-nav-dropdown-toggle:hover,
            .c-sidebar .c-sidebar-nav-link:hover {
                color: #fff;
                background: #004d40;
            }

            .c-sidebar {
                color: #fff;
                background: #003E37;
            }
        }

        .table thead tr:nth-child(1) {
            background-color: #004d40 !important;
            color: white;
        }

        .btn-primary {
            color: #fff;
            background-color: #003230;
            border-color: #003e37;
        }

        .btn-primary.focus,
        .btn-primary:focus {
            color: #fff;
            background-color: #01413e;
            border-color: #003e37;
            box-shadow: 0 0 0 0.2rem rgb(65 224 136 / 50%);
        }

        .btn-primary:not(:disabled):not(.disabled):active,
        .show>.btn-primary.dropdown-toggle {
            color: #fff;
            background-color: #003230;
            border-color: #003e37;
        }

        .btn-primary:hover {
            color: #fff;
            background-color: #01413e;
            border-color: #003e37;
        }

        .asis {
            color: white !important;
            padding: 3px;
            font-size: 12x;
        }

    </style>
</head>

<body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
        <div class="c-sidebar-brand d-lg-down-none">
            <h4 class="text-secondary">DOCENTES</h4>
            <h4></h4>
        </div>
        <ul class="c-sidebar-nav">
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('docente/dashboard') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}">
                        </use>
                    </svg> DASHBOARD</a>
            </li>
            <li class="c-sidebar-nav-title">Principal</li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('docente/perfil') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-user') }}"></use>
                    </svg> Perfil</a>
            </li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('docente/horario') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Horarios</a>
            </li>
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a
                    class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-education') }}">
                        </use>
                    </svg> Cursos</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('docente/cursos') }}"><span class="c-sidebar-nav-icon"></span> Mis
                            Cursos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('docente/cursos/cuadernillo') }}"><span class="c-sidebar-nav-icon"></span>
                            Cuadernillos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('docente/cursos/temario') }}"><span class="c-sidebar-nav-icon"></span>
                            Temarios</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('docente/cursos/sesion') }}"><span class="c-sidebar-nav-icon"></span>
                            Sesiones</a></li>
                </ul>
            </li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('docente/asistencia') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-calendar') }}">
                        </use>
                    </svg> Asistencia</a>
            </li>


        </ul>
        <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
            data-class="c-sidebar-minimized"></button>
    </div>
    <div class="c-wrapper c-fixed-components">
        <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
            <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show">
                <svg class="c-icon c-icon-lg">
                    <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button><a class="c-header-brand d-lg-none">
                <svg width="118" height="46" alt="CoreUI Logo">
                    <use xlink:href="coreui/assets/brand/coreui.svg#full"></use>
                </svg></a>
            <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar"
                data-class="c-sidebar-lg-show" responsive="true">
                <svg class="c-icon c-icon-lg">
                    <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button>
            {{-- <ul class="c-header-nav d-md-down-none">
		  <li class="c-header-nav-item px-3"><a class="c-header-nav-link">Dashboard</a></li>
		  <li class="c-header-nav-item px-3"><a class="c-header-nav-link">Users</a></li>
		  <li class="c-header-nav-item px-3"><a class="c-header-nav-link">Settings</a></li>
		</ul> --}}
            <ul class="c-header-nav ml-auto mr-4">

                <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <div class="c-avatar">{{ Auth::user()->usuario }}</div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right pt-0">
                        <div class="dropdown-header bg-light py-2"><strong>Account</strong></div><a
                            class="dropdown-item" href="{{ url('docente/perfil') }}">
                            <svg class="c-icon mr-2">
                                <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-user') }}">
                                </use>
                            </svg> Perfil</a>
                        <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                            <svg class="c-icon mr-2">
                                <use
                                    xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}">
                                </use>
                            </svg> Salir<form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                {{ csrf_field() }}
                            </form></a>


                    </div>
                </li>
            </ul>
            <div class="c-subheader px-3">
                <!-- Breadcrumb-->
                <ol class="breadcrumb border-0 m-0">
                    <li class="breadcrumb-item">Inicio</li>
                    <li class="breadcrumb-item active">@yield('titulo')</li>
                    <!-- Breadcrumb Menu-->
                </ol>
            </div>
        </header>
        <div class="c-body" id="app">
            <main class="c-main">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
            <footer class="c-footer">
                <div>Universidad Nacional del Altiplano © 2020</div>
                <div class="ml-auto">Oficina de Tecnologías de Información</div>
            </footer>
        </div>
    </div>
    <div class="loader">
        <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>

    </div>
    <!-- CoreUI and necessary plugins-->
    <script src="{{ asset('js/app.js?v=1.21082515') }}"></script>
    <script src="{{ asset('js/cepre-coreui.js') }}"></script>

</body>

</html>
