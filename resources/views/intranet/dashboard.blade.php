@extends('layouts.app')
@section('titulo', 'Dashboard')

@section('content')
    @can('ver distribucion')
        <i-pre-distribucion></i-pre-distribucion>
    @endcan
@endsection

@section('scripts')


@endsection
