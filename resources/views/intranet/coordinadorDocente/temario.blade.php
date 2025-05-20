@extends('layouts.app')
@section('titulo', 'Coordinador Docente | Temario')

@section('content')


    <i-coordinadord-temario :permissions="{{ $permisos }}"></i-coordinadord-temario>

@endsection

@section('scripts')

@endsection
