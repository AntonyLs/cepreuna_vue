@extends('layouts.app')
@section('titulo', 'Usuarios')

@section('content')


    <i-usuario :permissions="{{ $permisos }}"></i-usuario>

@endsection

@section('scripts')


@endsection
