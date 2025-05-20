@extends('layouts.app')
@section('titulo', 'Reporte | Pagos')

@section('content')

    <i-reporte-pagos :permissions="{{ $permisos }}" :ruta_rpt_pagos="{{ json_encode(route('intranet.reporte.pagos')) }}"></i-reporte-pagos>


@endsection

@section('scripts')

@endsection
