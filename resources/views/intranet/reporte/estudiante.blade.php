@extends('layouts.app')
@section('titulo', 'Reporte | Estudiantes')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <header class="card-header">
                    Reporte de Estudiantes
                </header>
                <div class="card-body">
                    <i-reporte-estudiante :permissions="{{ $permisos }}"></i-reporte-estudiante>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')


@endsection
