@extends('layouts.app')
@section('titulo', 'Matrícula')

@section('content')
    <i-lista-matricula-habilitar :permissions="{{ $permisos }}"></i-lista-matricula-habilitar>

@endsection

@section('scripts')

@endsection
