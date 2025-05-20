@extends('layouts.estudiante')
@section('titulo', ' Cursos')

@section('content')


    <div class="container">

        @if (auth()->user()->panel == '1')
            <w-cursos-estudiante :data="{{ $calificar }}"></w-cursos-estudiante>
        @else
            <div class="alert alert-warning" role="alert">
                <p class="mb-0"><b>Aviso</b>:</p>
                <p>Su usuario a sido <b>BLOQUEADO</b> para la visualización de sus cursos, comuniquese con CEPREUNA para su
                    regularización </p>

            </div>
        @endif



    </div>

@endsection

@section('scripts')


@endsection
