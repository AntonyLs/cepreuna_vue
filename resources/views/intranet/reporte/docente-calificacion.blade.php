@extends('layouts.app')
@section('titulo', 'Reporte | Calificación Docente')

@section('content')

    <i-reporte-docente-calificacion :permissions="{{ $permisos }}"></i-reporte-docente-calificacion>


@endsection

@section('scripts')

@endsection
