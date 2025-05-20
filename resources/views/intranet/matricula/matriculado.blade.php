@extends('layouts.app')
@section('titulo', 'Matrícula')

@section('content')
    <i-lista-matricula-matriculado :permissions="{{ $permisos }}"></i-lista-matricula-matriculado>

@endsection

@section('scripts')

@endsection
