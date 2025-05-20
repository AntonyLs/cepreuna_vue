@extends('layouts.app')
@section('titulo', 'Administración | Docente')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <header class="card-header">
            Docentes Inscritos.
            </header>
            <div class="card-body">
                <i-administracion-docente></i-administracion-docente>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

@endsection
