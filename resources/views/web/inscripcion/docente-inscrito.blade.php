@extends('layouts.web')
@section('titulo', 'Inscripcion correcta | CepreUNA')
@section('css')

<style type="text/css">
.vs--searchable .vs__dropdown-toggle {
    cursor: text;
    height: 36px;
}
</style>

@endsection
@section('content')

<div id="app" class="bg-gray2">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card shadow p-3 mb-5 bg-white rounded formulario">
                    <div class="card-body ">
                        <div class="row">

                            <div class="col-md-12 col-xs-12">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="float-right">
                                                        <nav class="breadcrumb">
                                                            <a class="breadcrumb-item" href="{{  url('/') }}">Inicio</a>
                                                            <span class="breadcrumb-item active">Preinscripción</span>
                                                            <span class="breadcrumb-item active">Finalizado</span>
                                                        </nav>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="float-left">
                                                        <h5>Su Preinscripción fue realizada con éxito, descargue su formato PDF.</h5>
                                                    </div>
                                                    <div class="float-right">
                                                        Fecha: {{date('d-m-Y')}}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    @if (!$status)
                                                        <ul>
                                                        <li> Lo que intenta visualizar no existe</li>
                                                        <li>Recargue la página(f5), Si el problema persiste comuniquese con el administrador</li>
                                                        </ul>
                                                    @else
                                                    <a class="btn btn-success mb-3" href="{{ url('inscripciones/docentes/pdf/'.$id) }}" download="" >Descargar Ficha de Preinscripción</a>
                                                        <div class="form-row">
                                                            <div class="col-md-12 text-center col-xs-12 embed-responsive embed-responsive-16by9">
                                                                <object class="embed-responsive-item" type="application/pdf" data="{{ url('inscripciones/docentes/pdf/'.$id) }}" allowfullscreen></object>
                                                            </div>
                                                        </div>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

<script>

</script>

@endsection
