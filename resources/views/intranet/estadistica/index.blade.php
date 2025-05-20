@extends('layouts.app')
@section('titulo', 'Estadisticas')

@section('content')

    <i-estadistica :permissions="{{ $permisos }}" fecha_f="{{$fecha_fin}}" fecha_i="{{$fecha_ini}}"></i-estadistica>


@endsection

@section('scripts')

@endsection
