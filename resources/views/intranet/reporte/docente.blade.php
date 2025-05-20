@extends('layouts.app')
@section('titulo', 'Reporte | Docente')

@section('content')

    <i-reporte-docente :permissions="{{ $permisos }}" :ruta_rpt_docente_horas="{{json_encode(route('intranet.reporte.docente'))}}" ></i-reporte-docente>


@endsection

@section('scripts')

@endsection
