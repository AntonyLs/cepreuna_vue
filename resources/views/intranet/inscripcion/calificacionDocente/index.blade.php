@extends('layouts.app')
@section('titulo', 'Inscripciones | Calificacion Docente')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <i-lista-inscripcion-calificacion-docente :permissions="{{ $permisos }}"></i-lista-inscripcion-calificacion-docente>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
