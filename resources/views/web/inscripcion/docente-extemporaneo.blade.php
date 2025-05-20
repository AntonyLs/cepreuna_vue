@extends('layouts.web')
@section('titulo', 'Inscripcion Docente | CepreUNA')
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
                                                            <span class="breadcrumb-item active">Inscripcion</span>
                                                            <span class="breadcrumb-item active">Docentes</span>
                                                        </nav>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="float-left">
                                                        <h5>INSCRIPCIÓN DE DOCENTES</h5>
                                                    </div>
                                                    <div class="float-right">
                                                        Fecha: {{date('d-m-Y')}}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <w-form-inscripcion-docente></w-form-inscripcion-docente>
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
<script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>
</script>

@endsection
