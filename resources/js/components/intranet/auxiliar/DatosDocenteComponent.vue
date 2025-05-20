<template>
    <div class="container">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" style="display:flex" data-toggle="modal" :data-target="'#modalDocente'+docenteApto.id">
        <i class="fas fa-user-tie"></i> <span class="mx-2"> Datos</span>
        </button>

        <!-- Modal -->
        <div class="modal fade" :id="'modalDocente'+docenteApto.id" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Datos del Docente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background-color: #eee;">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="card mb-2 shadow-sm rounded">
                                        <div class="row no-gutters">
                                            <div class="col-md-4 col-xs-12">
                                                <img :src="docenteApto.foto === null ? '':'../../storage/fotos/'+docenteApto.foto" class="card-img border-right" alt="">
                                            </div>
                                            <div class="col-md-8 col-xs-12">
                                                <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Datos Personales del Estudiante</b></div>
                                                <div class="card-body py-2 text-left">
                                                    <b>Nombres y Apellidos:</b>
                                                    <p class="m-0">{{ docenteApto.nombres+' '+docenteApto.paterno+' '+docenteApto.materno}}</p>
                                                    <b>Número de Documento:</b>
                                                    <p class="m-0">{{ docenteApto.nro_documento }}</p>
                                                    <b>Celular:</b>
                                                    <p class="m-0">{{ docenteApto.celular }}</p>
                                                    <b>Correo Electrónico</b>
                                                    <p class="m-0">{{ docenteApto.email }}</p>
                                                    <b>Cuenta Institucional</b>
                                                    <p class="m-0">{{ usuario }}</p>
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

</template>

<script>
    import $ from 'jquery'
    export default {
        data() {
            return{
                fields: {

                },
                // errors: {},
                // result:{
                //     pago:[]
                // },
                // errorPago:null,
                selectAdjunto:'Selecione',
                docenteApto:this.datosDocente,
                idDocente:''
            }
        },
        props: ['datosDocente','usuario'],
        methods: {

        },
        mounted() {
        }
    }
</script>
