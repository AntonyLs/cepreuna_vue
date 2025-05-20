@extends('layouts.docente')
@section('titulo', ' Dashboard')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col text-center">
                <div class="card">
                    <div class="card-header"><h4 class="m-0">Bienvenido a su Panel Docente</h4></div>
                    <div class="card-body">En este panel Ud. puede visualizar todo referente a sus cursos.</div>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card card-accent-success">
                    <div class="card-header font-weight-bold">Notificación</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-xs-12 text-center">
                                {{-- <img src="{{ asset("images/comunicados/comunicado.png") }}" class="img-fluid" alt=""> --}}
                                <comunicado mostrar="2"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')


@endsection
