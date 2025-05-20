@extends('layouts.app')
@section('titulo', 'Roles')

@section('content')


    <i-roles :permissions="{{ $permisos }}"></i-roles>

@endsection

@section('scripts')


@endsection
