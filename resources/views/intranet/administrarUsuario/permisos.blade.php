@extends('layouts.app')
@section('titulo', 'Permisos')

@section('content')


    <i-permisos :permissions="{{ $permisos }}"></i-permisos>

@endsection

@section('scripts')


@endsection
