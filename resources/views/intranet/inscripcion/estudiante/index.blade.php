@extends('layouts.app')
@section('titulo', 'Inscripciones | Estudiantes')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <header class="card-header">
                    Estudiantes Inscritos.
                </header>
                <div class="card-body">
                    <i-lista-inscripcion-estudiante :permissions="{{ $permisos }}">
                    </i-lista-inscripcion-estudiante>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
