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

        #sql_editor {
            height: 300px;
            width: 100%;
            border: 1px solid #ccc;
            font-size: 1.1rem;

        }

        #submit_button {
            margin-top: 10px;
        }
    </style>
</head>

<body class="c-app">
    <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
        <div class="c-sidebar-brand d-lg-down-none">
            <h4 class="text-info">CEPRE</h4>
            <h4> - UNA</h4>
        </div>
        <ul class="c-sidebar-nav">
            @can('ver menu dashboard')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/dashboard') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-speedometer') }}">
                        </use>
                    </svg> DASHBOARD</a>
            </li>
            @endcan

            <li class="c-sidebar-nav-title">Principal</li>
            @can('ver menu inscripciones')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-folder-open') }}">
                        </use>
                    </svg> Inscripciones</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver inscripcion estudiantes')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/inscripcion/estudiante') }}"><span class="c-sidebar-nav-icon"></span>
                            Estudiantes</a>
                    </li>
                    @endcan
                    @can('ver inscripcion docentes')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/inscripcion/docente') }}"><span class="c-sidebar-nav-icon"></span>
                            Docentes</a>
                    </li>
                    @endcan
                    @can('ver inscripcion calificacion docente')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/inscripcion/calificacion-docente') }}"><span class="c-sidebar-nav-icon"></span>
                            Calificación Docentes</a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan
            @can('ver menu administracion')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-education') }}">
                        </use>
                    </svg> Administración</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/grupo-aula') }}"><span class="c-sidebar-nav-icon"></span> Habilitar Grupo</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/horario') }}"><span class="c-sidebar-nav-icon"></span> Horario</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/propuesta-horario') }}"><span class="c-sidebar-nav-icon"></span> Propuesta Horario</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/estudiante') }}"><span class="c-sidebar-nav-icon"></span> Estudiantes</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/actualizar-foto') }}"><span class="c-sidebar-nav-icon"></span> Actualizar Foto</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/docente') }}"><span class="c-sidebar-nav-icon"></span> Docentes</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/auxiliar') }}"><span class="c-sidebar-nav-icon"></span> Auxiliares</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/coordinador-auxiliar') }}"><span class="c-sidebar-nav-icon"></span> Coordinador Auxiliar</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/inscripciones') }}"><span class="c-sidebar-nav-icon"></span> Inscripciones</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/vacantes') }}"><span class="c-sidebar-nav-icon"></span> Vacantes</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cronograma-pagos') }}"><span class="c-sidebar-nav-icon"></span> Cronograma de Pagos</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/tarifario-estudiante') }}"><span class="c-sidebar-nav-icon"></span> Tarifario Estudiante</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/horario-inscripcion') }}"><span class="c-sidebar-nav-icon"></span> Horario Inscripción</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-matricula') }}"><span class="c-sidebar-nav-icon"></span> Sincronizar Matriculas</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-grupo') }}"><span class="c-sidebar-nav-icon"></span> Sincronizar Cursos</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-habilitacion') }}"><span class="c-sidebar-nav-icon"></span> Sincronizar Habilitación</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-docente') }}"><span class="c-sidebar-nav-icon"></span> Sincronizar Docentes</a>
                    </li>

                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-correo') }}"><span class="c-sidebar-nav-icon"></span> Sincronizar Correo Docente</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/cron-encuesta') }}"><span class="c-sidebar-nav-icon"></span> Habilitar Encuesta Docentes</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('ver menu administracion temporal')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-education') }}">
                        </use>
                    </svg> Administración</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/horario') }}"><span class="c-sidebar-nav-icon"></span> Horario</a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administracion/propuesta-horario') }}"><span class="c-sidebar-nav-icon"></span> Propuesta Horario</a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('ver menu matriculas')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Matriculas</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver matriculas pendientes')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/matricula/pendiente') }}"><span class="c-sidebar-nav-icon"></span>
                            Pendientes</a>
                    </li>
                    @endcan
                    @can('ver matriculados')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/matricula/matriculado') }}"><span class="c-sidebar-nav-icon"></span> Matriculados</a>
                    </li>
                    @endcan
                    @can('ver habilitar')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/matricula/habilitar') }}"><span class="c-sidebar-nav-icon"></span> Habilitar</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan
            @can('ver menu asistencia')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Asistencia</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver asistencia docente detalle')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/asistencia/docente') }}"><span class="c-sidebar-nav-icon"></span>
                            Docentes</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            @can('ver menu configuracion')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-cog') }}"></use>
                    </svg> Configuración</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/sede') }}"><span class="c-sidebar-nav-icon"></span>
                            Sedes</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/local') }}"><span class="c-sidebar-nav-icon"></span>
                            Locales</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/aulas') }}"><span class="c-sidebar-nav-icon"></span>
                            Aulas</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/grupos') }}"><span class="c-sidebar-nav-icon"></span>
                            Grupos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/criterios') }}"><span class="c-sidebar-nav-icon"></span>
                            Criterios</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/tarifas') }}"><span class="c-sidebar-nav-icon"></span>
                            Tarifas</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/tipo-documento') }}"><span class="c-sidebar-nav-icon"></span> Tipo Documento</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/colegios') }}"><span class="c-sidebar-nav-icon"></span>
                            Colegios</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/parentescos') }}"><span class="c-sidebar-nav-icon"></span>
                            Parentescos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/programas') }}"><span class="c-sidebar-nav-icon"></span>
                            Programas</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/turnos') }}"><span class="c-sidebar-nav-icon"></span>
                            Turnos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/plantilla_horario') }}"><span class="c-sidebar-nav-icon"></span> Plantilla Horario</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/cursos') }}"><span class="c-sidebar-nav-icon"></span>
                            Cursos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/areas') }}"><span class="c-sidebar-nav-icon"></span>
                            Areas</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/grado_academico') }}"><span class="c-sidebar-nav-icon"></span>
                            Grados Académicos</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/plantilla_horario') }}"><span class="c-sidebar-nav-icon"></span>
                            Plantilla Horario</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/curricula') }}"><span class="c-sidebar-nav-icon"></span>
                            Curricula</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/configuracion/periodos') }}"><span class="c-sidebar-nav-icon"></span>
                            Periodos</a></li>
                    <!-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="buttons/button-group.html"><span class="c-sidebar-nav-icon"></span> Docentes</a></li> -->
                </ul>
            </li>
            @endcan
            @can('ver menu administrar usuario')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-user-follow') }}">
                        </use>
                    </svg>Administrar Usuarios</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver usuarios')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-usuarios/usuario') }}"><span class="c-sidebar-nav-icon"></span> Usuarios</a>
                    </li>
                    @endcan
                    @can('ver roles')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-usuarios/roles') }}"><span class="c-sidebar-nav-icon"></span> Roles</a>
                    </li>
                    @endcan
                    @can('ver permisos')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-usuarios/permisos') }}"><span class="c-sidebar-nav-icon"></span> Permisos</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            @can('ver menu reportes')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-bar-chart') }}">
                        </use>
                    </svg> Reportes</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver reporte estudiantes')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/estudiante') }}"><span class="c-sidebar-nav-icon"></span> Estudiantes</a>
                    </li>
                    @endcan
                    @can('ver reporte docentes')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/docente') }}"><span class="c-sidebar-nav-icon"></span> Docentes</a>
                    </li>
                    @endcan
                    @can('ver reporte asistencia docentes')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/asistencia-docente') }}"><span class="c-sidebar-nav-icon"></span>Asistencia Docentes</a>
                    </li>
                    @endcan
                    @can('ver reporte docentes calificacion')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/docente-calificacion') }}"><span class="c-sidebar-nav-icon"></span> Docentes Calificación</a>
                    </li>
                    @endcan
                    @can('ver reporte pagos')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/pagos') }}"><span class="c-sidebar-nav-icon"></span>Pagos</a>
                    </li>
                    @endcan
                    @can('ver reporte docente ingresantes')
                    <li class="c-sidebar-nav-item">
                        <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/docente-ingresantes') }}"><span class="c-sidebar-nav-icon"></span>Docente Ingresantes</a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan
            @can('ver menu estadistica')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/estadistica') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-chart-line') }}">
                        </use>
                    </svg> Estadisticas</a>
            </li>
            @endcan
            {{-- @can('ver menu comunicado')
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/comunicado') }}">
            <svg class="c-sidebar-nav-icon">
                <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
            </svg> Comunicado</a>
            </li>
            @endcan --}}
            @can('ver menu aplicativo')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-mobile') }}">
                        </use>
                    </svg> Aplicativo</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver comunicado')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/aplicativo/comunicado') }}"><span class="c-sidebar-nav-icon"></span>
                            Comunicados</a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan
            @can('ver menu administrar nosotros')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-people') }}">
                        </use>
                    </svg> Administrar Nosotros</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver directivos')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-nosotros/directivos') }}"><span class="c-sidebar-nav-icon"></span>
                            Directivos</a>
                    </li>
                    @endcan
                    @can('ver misionvision')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-nosotros/misionvision') }}"><span class="c-sidebar-nav-icon"></span>
                            Misión y Visión</a>
                    </li>
                    @endcan
                    @can('ver objetivos')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-nosotros/objetivos') }}"><span class="c-sidebar-nav-icon"></span>
                            Objetivos</a>
                    </li>
                    @endcan
                    @can('ver historia')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-nosotros/historia') }}"><span class="c-sidebar-nav-icon"></span>
                            Historia</a>
                    </li>
                    @endcan
                    @can('ver ciclos')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/administrar-nosotros/ciclos') }}"><span class="c-sidebar-nav-icon"></span>
                            Ciclos</a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan
            @can('ver menu auxiliar')
            <li class="c-sidebar-nav-title">Auxiliar</li>
            @can('ver habilitacion')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/auxiliar/habilitacion') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-check') }}"></use>
                    </svg> Habilitación</a>
            </li>
            @endcan
            @can('ver horarios')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/auxiliar/horario') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Horario</a>
            </li>
            @endcan
            @can('ver asistencias')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-list') }}"></use>
                    </svg> Asistencias</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    @can('ver asistencia docentes')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/auxiliar/asistencia/docente') }}"><span class="c-sidebar-nav-icon"></span> Docentes</a>
                    </li>
                    @endcan
                    @can('ver asistencia estudiante')
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/auxiliar/asistencia/estudiante') }}"><span class="c-sidebar-nav-icon"></span> Estudiante</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcan

            {{-- <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a
                        class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                        <svg class="c-sidebar-nav-icon">
                            <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-bar-chart') }}">
            </use>
            </svg> Reportes</a>
            <ul class="c-sidebar-nav-dropdown-items">
                <li class="c-sidebar-nav-item">
                    <a class="c-sidebar-nav-link" href="{{ url('intranet/reporte/docente') }}"><span class="c-sidebar-nav-icon"></span> Docentes</a>
                </li>
            </ul>
            </li> --}}
            @endcan

            @can('ver menu coordinador auxiliar')
            <li class="c-sidebar-nav-title">Coordinador Auxiliar</li>

            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Docente</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/coordinador-auxiliar/docente/horario') }}"><span class="c-sidebar-nav-icon"></span> Horario</a></li>

                </ul>
            </li>

            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-list') }}"></use>
                    </svg> Asistencias</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/coordinador-auxiliar/asistencia/docente') }}"><span class="c-sidebar-nav-icon"></span> Docentes</a></li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/coordinador-auxiliar/asistencia/estudiante') }}"><span class="c-sidebar-nav-icon"></span> Estudiante</a></li>
                </ul>
            </li>
            @endcan

            @can('ver menu coordinador docente')
            <li class="c-sidebar-nav-title">Coordinador Docente</li>
            @can('ver cuadernillos')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/coordinador-docente/cuadernillo') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-file') }}"></use>
                    </svg> Cuadernillos</a>
            </li>
            @endcan
            @can('ver temarios')
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/coordinador-docente/temario') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-list') }}"></use>
                    </svg> Temario</a>
            </li>
            @endcan
            @endcan
            @can('ver menu recursos humanos')
            <li class="c-sidebar-nav-title">Recursos Humanos</li>
            @can('ver menu pagos')
            <li class="c-sidebar-nav-item c-sidebar-nav-dropdown"><a class="c-sidebar-nav-link c-sidebar-nav-dropdown-toggle">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Pagos</a>
                <ul class="c-sidebar-nav-dropdown-items">
                    <!-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/recursos-humanos/pagos/docentes/habilitacion') }}"><span class="c-sidebar-nav-icon"></span> Habilitacion Docentes </a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/recursos-humanos/pagos/docentes/expedientes') }}"><span class="c-sidebar-nav-icon"></span> Expedientes Docentes </a>
                    </li> -->
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/recursos-humanos/pagos/docentes/horas-mes') }}"><span class="c-sidebar-nav-icon"></span> Horas por mes </a>
                    </li>
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/devolucion/devoluciones') }}"><span class="c-sidebar-nav-icon"></span> Devoluciones </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/recursos-humanos/cron-correo') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-lan') }}"></use>
                    </svg> Enviar Accesos</a>
            </li> -->
            @endcan
            @endcan
            @can('menu libro reclamaciones')
            <li class="c-sidebar-nav-title">Libro de Reclamaciones</li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('intranet/libro-reclamaciones/reclamaciones') }}">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-book') }}">
                        </use>
                    </svg> Reclamos</a>
            </li>
            @endcan
        </ul>
        <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
    </div>
    <div class="c-wrapper c-fixed-components">
        <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
            <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
                <svg class="c-icon c-icon-lg">
                    <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-menu') }}"></use>
                </svg>
            </button><a class="c-header-brand d-lg-none">
                <svg width="118" height="46" alt="CoreUI Logo">
                    <!-- <use xlink:href="coreui/assets/brand/coreui.svg#full"></use> -->
                </svg></a>
            <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
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

                <li class="c-header-nav-item dropdown"><a class="c-header-nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <div class="c-avatar">{{ Auth::user()->name }}</div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right pt-0">
                        <div class="dropdown-header bg-light py-2"><strong>Account</strong></div><a class="dropdown-item">
                            <svg class="c-icon mr-2">
                                <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-user') }}">
                                </use>
                            </svg> Perfil</a>
                        <div class="dropdown-header bg-light py-2"><strong>Settings</strong></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                            <svg class="c-icon mr-2">
                                <use xlink:href="{{ asset('coreui/vendors/@coreui/icons/svg/free.svg#cil-account-logout') }}">
                                </use>
                            </svg> Salir<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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
    <script src="{{ asset('js/app.js?v=1.21082536') }}"></script>
    <script src="{{ asset('js/cepre-coreui.js') }}"></script>
    @stack('scripts')
</body>

</html>
