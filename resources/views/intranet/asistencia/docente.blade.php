@extends('layouts.app')
@section('titulo', 'Asistencia | Docentes')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <header class="card-header">
                    Asistencia Docentes.
                </header>
                <div class="card-body">
                    <i-lista-asistencia-docente :permissions="{{ $permisos }}"></i-lista-asistencia-docente>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
