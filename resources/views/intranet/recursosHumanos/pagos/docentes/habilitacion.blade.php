@extends('layouts.app')
@section('titulo', 'Inscripciones | Habilitar Docente')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <i-habilitacion-pago-docente :permissions="{{ $permisos }}"></i-habilitacion-pago-docente>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
