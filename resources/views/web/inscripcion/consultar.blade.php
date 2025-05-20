@extends('layouts.web')
@section('titulo', 'Consultar Inscripción | CepreUNA')
@section('css')

    <style type="text/css">
        .vs--searchable .vs__dropdown-toggle {
            cursor: text;
            height: 36px;
        }

    </style>

@endsection
@section('content')

    <div id="app" class="bg-gray2">
        <div class="container" style="height: 1000px">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow p-3 mb-5 bg-white rounded formulario" style="height: 100%">
                        <div class="card-body ">
                            <div class="row">

                                <div class="col-md-12 col-xs-12">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="float-right">
                                                            <nav class="breadcrumb">
                                                                <a class="breadcrumb-item"
                                                                    href="{{ url('/') }}">Inicio</a>
                                                                <span class="breadcrumb-item active">Inscripción</span>
                                                                <span class="breadcrumb-item active">Consulta</span>
                                                            </nav>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <w-consultar-local></w-consultar-local>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script>

    </script>

@endsection
