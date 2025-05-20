@extends('layouts.app')
@section('titulo', 'Comunicados')

@section('content')
    <i-aplicativo-comunicado :permissions="{{ $permisos }}"></i-aplicativo-comunicado>
@endsection

@section('scripts')

@endsection
