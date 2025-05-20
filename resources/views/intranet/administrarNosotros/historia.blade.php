@extends('layouts.app')
@section('titulo', 'Historia')

@section('content')
    <i-historia :permissions="{{ $permisos }}"></i-historia>
@endsection

@section('scripts')

@endsection
