@extends('layouts.app')
@section('titulo', 'Objetivos')

@section('content')
    <i-objetivos :permissions="{{ $permisos }}"></i-objetivos>
@endsection

@section('scripts')

@endsection
