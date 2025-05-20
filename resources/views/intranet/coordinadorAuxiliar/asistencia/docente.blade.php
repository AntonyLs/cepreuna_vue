@extends('layouts.app')
@section('titulo', 'Coordinador Auxiliar | Asistencia docente')

@section('content')

<i-coordinador-auxiliar-asistencia-docente fecha-hoy="{{$fecha}}" :permissions="{{ $permisos }}"></i-coordinador-auxiliar-asistencia-docente>

@endsection

@section('scripts')

@endsection
