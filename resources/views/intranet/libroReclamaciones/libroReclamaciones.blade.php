@extends('layouts.app')
@section('titulo', 'Libro de Reclamaciones')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <i-libro-reclamaciones :external_url="{{ $external_url }}" :permissions="{{ $permisos }}">
                    </i-libro-reclamaciones>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
