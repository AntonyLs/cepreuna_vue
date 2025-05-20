@extends('layouts.app')
@section('titulo', 'Habilitación')

@section('content')
    {{-- <i-auxiliar-habilitacion :permissions="{{ $permisos }}"></i-auxiliar-habilitacion> --}}
    <i-auxiliar-habilitacion-buscar :permissions="{{ $permisos }}"></i-auxiliar-habilitacion-buscar>

@endsection

@section('scripts')

@endsection
