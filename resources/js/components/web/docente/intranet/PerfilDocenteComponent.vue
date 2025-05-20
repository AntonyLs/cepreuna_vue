<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Mi Perfil</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 border-right">
                        <!-- <img :src="'../../storage/fotos/'+infoEstudiante.foto" class="card-img border-right" alt=""> -->
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                            <img class="img-thumbnail rounded" :src="'../../storage/fotos/' + docente.foto" width="150" />
                            <span class="font-weight-bold">{{ usuario }}</span>
                            <span>{{ docente.nro_documento }}</span>
                        </div>
                    </div>
                    <div class="col-md-5 border-right">
                        <div class="p-3 py-5">
                            <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Nombres y Apellidos:</label></div>
                                <div class="col-md-12">
                                    <label class="labels"> {{ docente.nombres }} </label> <label> {{ docente.paterno + " " + docente.materno }}</label>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6"><label class="labels font-weight-bold">Tipo de documento: </label></div>
                                <div class="col-md-6"><label class="labels font-weight-bold">Número de Documento</label></div>

                                <div class="col-md-6">
                                    <label class="labels">{{ docente.tipo_documento.denominacion }} </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">{{ docente.nro_documento }}</label>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6"><label class="labels font-weight-bold">Condición:</label></div>
                                <div class="col-md-6"><label class="labels font-weight-bold">Telefono:</label></div>
                                <div class="col-md-6">
                                    <label class="labels">{{ docente.condicion == 1 ? "Particular" : "Docente UNAP" }}</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels">{{ docente.celular }}</label>
                                </div>
                            </div>

                            <div class="row mt-3" v-for="grado in grados">
                                <div class="col-md-6">
                                    <label class="labels font-weight-bold">Grado o Título: </label><br>
                                    <label class="labels">{{ grado.grado_academico.denominacion }}</label>
                                </div>
                                <div class="col-md-6">
                                    <label class="labels font-weight-bold">Carrera: </label><br>
                                    <label class="labels">{{ grado.programa.denominacion }}</label>
                                </div>
                            </div>
                            <!-- <div class="row mt-3">
                                <div class="col-md-12"><label class="labels font-weight-bold">Profesión: </label></div>
                                <div class="col-md-12">
                                    <label class="labels">{{ docente.programa.denominacion }}</label>
                                </div>
                            </div> -->
                            <!-- <div class="row mt-3 mb-2">
                                <div class="col-md-12"><label class="labels font-weight-bold">Dirección: </label></div>
                                <div class="col-md-12"><label class="labels">Av. Las palmas #1024</label></div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-md-4" v-if="docente.condicion == 2">
                        <div class="p-3 py-5">
                            <div class="d-flex justify-content-between align-items-center experience"><span class="font-weight-bold">Datos Unap</span></div>
                            <div class="d-flex flex-row mt-3 exp-container">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/c/cb/Logo_UNAP.png" width="45" height="45" />
                                <div class="work-experience ml-1">
                                    <span class="font-weight-bold d-block">Docente de la Unap</span>
                                    <span class="d-block text-black-50 labels"><span class="labels font-weight-bold">Cod Docente: </span>{{ docente.codigo_unap }}</span>
                                    <!-- <span class="d-block text-black-50 labels">Vigente - Enero 2021</span> -->
                                </div>
                            </div>
                            <hr />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["horario", "turno_horas"],
    data: () => ({
        docente: [],
        usuario: "",
        grados: []
    }),

    // async created(){
    //     this._horario = [];
    //     this._horas = [];
    //     this._horario = this.horario
    //     this._horas = this.turno_horas
    //     await this.GenerarHorarioEs();
    // },
    methods: {
        getDocente: function() {
            axios.get("get-docente").then(
                function(response) {
                    // console.log(response.data);
                    this.docente = response.data.docente;
                    this.usuario = response.data.docentea;
                    this.grados = response.data.grados;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getDocente();
    }
};
</script>
