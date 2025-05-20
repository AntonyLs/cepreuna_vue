@extends('layouts.app')
@section('titulo', 'Inscripciones | Horas Docente')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <i-lista-docente-horas-mes :tipos="{{ $tipos }}"></i-lista-docente-horas-mes>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
