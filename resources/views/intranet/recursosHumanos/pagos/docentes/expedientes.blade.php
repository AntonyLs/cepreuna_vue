@extends('layouts.app')
@section('titulo', 'Inscripciones | Expediente Docente')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <i-expediente-pago-docente :users="{{ $users }}" :external_url="{{ $external_url }}"
                        :permissions="{{ $permisos }}">
                    </i-expediente-pago-docente>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
