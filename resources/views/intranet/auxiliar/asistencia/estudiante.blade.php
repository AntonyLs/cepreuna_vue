@extends('layouts.app')
@section('titulo', 'Auxiliar | Asistencia Estudiante')

@section('content')

    <i-auxiliar-asistencia-estudiante fecha-hoy="{{ $fecha }}" :permissions="{{ $permisos }}">
    </i-auxiliar-asistencia-estudiante>

@endsection

@section('scripts')

@endsection
