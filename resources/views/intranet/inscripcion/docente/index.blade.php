@extends('layouts.app')
@section('titulo', 'Inscripciones | Docentes')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <header class="card-header">
                    Docentes Inscritos.
                    <a href="/inscripciones/docentes-extemporaneos" class="btn btn-primary float-right" target="_blank">Inscribir docente</a>
                </header>
                <div class="card-body">
                    <i-lista-inscripcion-docente :permissions="{{ $permisos }}"></i-lista-inscripcion-docente>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
