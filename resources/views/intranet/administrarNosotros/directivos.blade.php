@extends('layouts.app')
@section('titulo', 'Directivos')

@section('content')
    <i-directivos :permissions="{{ $permisos }}"></i-directivos>
@endsection

@section('scripts')

@endsection
