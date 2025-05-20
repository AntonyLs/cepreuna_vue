@extends('layouts.app')
@section('titulo', 'Reporte | Asistencia Docente')

@section('content')

    <i-reporte-asistencia-docente :permissions="{{ $permisos }}" :ruta_rpt_docente_sedes="{{json_encode(route('intranet.reporte.docenteSede'))}}"></i-reporte-asistencia-docente>


@endsection

@section('scripts')

@endsection
