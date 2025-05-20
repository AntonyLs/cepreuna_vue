<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Mi Perfil</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row" v-if="estudiante.edit == '1'">
                    <div class="col-md-12">
                        <div class="alert alert-success" role="alert">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="text-left">
                                        <strong>La confirmación de sus datos se realizo de manera satisfactoria.</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-right">
                                        <a id="" class="btn btn-danger p-1" :href="'/estudiante/pdf-declaracion-jurada/' + estudiante.id" role="button" download
                                            ><i class="fas fa-file-pdf"></i> Descargar Declaracion Jurada</a
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 border-right">
                        <!-- <img :src="'../../storage/fotos/'+infoEstudiante.foto" class="card-img border-right" alt=""> -->
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                            <img class="img-thumbnail rounded" :src="'../../storage/fotos/' + estudiante.foto" width="150" />
                            <span class="font-weight-bold">{{ estudiante.usuario }}</span>
                            <span>{{ estudiante.nro_documento }}</span>
                        </div>
                    </div>
                    <div class="col-md-4 border-right">
                        <div class="p-3 py-5">
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Nombres y Apellidos:</label></div>
                                <div class="col-md-12">
                                    <label class="labels"> {{ estudiante.nombres }} </label> <label> {{ estudiante.paterno + " " + estudiante.materno }}</label>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6"><label class="labels font-weight-bold">Tipo de documento: </label></div>
                                <div class="col-md-6"><label class="labels font-weight-bold">Número de Documento</label></div>

                                <div class="col-md-6">
                                    <label class="labels">{{ estudiante.tipo_documento.denominacion }} </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">{{ estudiante.nro_documento }}</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Fecha de Nacimiento: </label></div>
                                <div class="col-md-12">
                                    <label class="labels">{{ estudiante.fecha_nac }}</label>
                                </div>
                            </div>
                            <div class="row mt-3" v-if="estudiante.ubigeo">
                                <div class="col-md-12"><label class="labels font-weight-bold">Lugar de Nacimiento: </label></div>
                                <div class="col-md-12" v-if="estudiante.ubigeos_nacimiento">
                                    <label class="labels">{{ estudiante.departamento + " - " + estudiante.provincia + "-" + estudiante.distrito }}</label>
                                </div>
                                <div class="col-md-12" v-else>
                                    <label class="labels">{{ estudiante.ubigeo.departamento + " - " + estudiante.ubigeo.provincia + "-" + estudiante.ubigeo.distrito }}</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Año de Egreso: </label></div>
                                <div class="col-md-12">
                                    <label class="labels">{{ estudiante.anio_egreso }}</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Celular: </label></div>
                                <div class="col-md-12">
                                    <label class="labels">{{ estudiante.celular }}</label>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Colegio: </label></div>
                                <div class="col-md-12">
                                    <label class="labels">{{ estudiante.colegio.denominacion }}</label>
                                </div>
                            </div>

                            <!-- <div class="row mt-3 mb-2">
                                <div class="col-md-12"><label class="labels font-weight-bold">Dirección: </label></div>
                                <div class="col-md-12"><label class="labels">Av. Las palmas #1024</label></div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-md-5 col-sm-12 text-center">
                        <h5 class="text-primary">App Movil</h5>
                        <div>
                            <qrcode-vue :value="qr" :size="size" renderAs="svg" level="H" :className="'qrcode m-2'"></qrcode-vue>
                        </div>
                        <div class="alert alert-primary" role="alert">
                            <h6>Instrucciones:</h6>
                            <ul class="text-left">
                                <li>Descargar la App de CepreUna en la PlayStore de Google.</li>
                                <li>Abrir la App de CepreUna.</li>
                                <li>Escanear el código QR.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        estudiante: [],
        usuario: "",
        qr: "",
        size: 300
    }),

    // async created(){
    //     this._horario = [];
    //     this._horas = [];
    //     this._horario = this.horario
    //     this._horas = this.turno_horas
    //     await this.GenerarHorarioEs();
    // },
    methods: {
        getEstudiante: function() {
            axios.get("get-estudiante").then(
                function(response) {
                    // console.log(response.data);
                    this.estudiante = response.data.estudiante;
                    this.qr = response.data._token;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getEstudiante();
    }
};
</script>
