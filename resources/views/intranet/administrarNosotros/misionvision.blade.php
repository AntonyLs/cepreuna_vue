@extends('layouts.app')
@section('titulo', 'MisionVision')

@section('content')
    <i-misionvision :permissions="{{ $permisos }}"></i-misionvision>
@endsection

@section('scripts')

@endsection
