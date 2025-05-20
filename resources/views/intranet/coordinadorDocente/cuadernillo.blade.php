@extends('layouts.app')
@section('titulo', 'Coordinador Docente | Cuadernillo')

@section('content')


    <i-coordinadord-cuadernillo :permissions="{{ $permisos }}"></i-coordinadord-cuadernillo>

@endsection

@section('scripts')

@endsection
