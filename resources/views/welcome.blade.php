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
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}" />
        <!-- css -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/web/main.css') }}">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

        <!-- Styles -->
        @yield('css')
        <style>
            html, body {
                background-color: #fff;
                font-family: 'Roboto', sans-serif;
                height: 100vh;
                margin: 0;
            }
            #center img {
                margin-top: 20px;
            }
            body {
                /* background: linear-gradient(#016669,#015969, #014f94 ) fixed; */
                background-color: #ebedef;
            }
            .links a {
                font-size: 20px;
                margin: 0 10px;
            }
        </style>
    </head>

        <header>
            <div class="navigation">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 m-0 p-0">
                            <div class="pull-right">
                                <a id="" class="btn btn-link" style="color:rgb(129 129 129);" href="{{ url('login') }}" role="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                                        <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15H1.5zM11 2h.5a.5.5 0 0 1 .5.5V15h-1V2zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z"/>
                                      </svg>
                                    Intranet</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12  text-center" style="position: relative;">
                            <div id="center">
                            {{-- <div class="float-left"> --}}
                                <img src="{{ asset('images/logo.png') }}" class="index-logo" alt="">
                                <div class="text-center" style="vertical-align: bottom; margin-left: 10px;">
                                    <h1 class="font-weight-bold text-secondary my-4" style="margin:0;">Universidad Nacional del Altiplano</h5>
                                    <h3 style="color:rgb(129 129 129);" style="margin:0;">Centro Pre Universitario</p>
                                        <br>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 links text-center">

                            <a id="" class="btn btn-secondary" href="{{ url('web/login') }}" role="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                    <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                                    <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                                </svg>
                                 Panel Docente</a>

                            <a id="" class="btn btn-success" href="{{ url('web/login') }}" role="button">

                                <svg xmlns="http://www.w3.org/2000/svg" width="46" height="46" fill="currentColor" class="bi bi-book-half" viewBox="0 0 16 16">
                                    <path d="M8.5 2.687c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                                  </svg>
                                Panel Estudiante
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    <body>

    </body>
</html>
