@extends('layouts.app')
@section('titulo', 'Matrícula | Pendientes')

@section('content')
    <i-lista-matricula-pendiente :permissions="{{ $permisos }}"></i-lista-matricula-pendiente>
@endsection

@section('scripts')

@endsection
