@extends('layouts.app')
@section('titulo', 'Reporte | Docente Ingresantes')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <header class="card-header">
                    Reporte de Docentes con la cantidad de ingresantes
                </header>
                <div class="card-body">
                    <i-reporte-docente-ingresantes :permissions="{{ $permisos }}"></i-reporte-docente-ingresantes>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
