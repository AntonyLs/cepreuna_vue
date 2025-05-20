@extends('layouts.app')
@section('titulo', 'Auxiliar | Asistencia Docente')

@section('content')

    <i-auxiliar-asistencia-docente fecha-hoy="{{ $fecha }}" :permissions="{{ $permisos }}">
    </i-auxiliar-asistencia-docente>

@endsection

@section('scripts')

@endsection
