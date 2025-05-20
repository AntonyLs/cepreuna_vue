<template>
    <div>
        <div v-if="!confirmado" class="col-md-12">
            <div class="container">
                <div class="row">
                    <div class="col text-center">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="m-0">Confirmación de Datos</h4>
                            </div>
                            <div v-if="estudiante.edit == '0'" class="card-body mx-4">
                                <div v-if="!rptaNo && confirmarData" class="row">
                                    <div class="col-md-3 col-xs-12">
                                        <img class="img-thumbnail rounded" :src="'../../storage/fotos/' + estudiante.foto" width="150" />
                                    </div>
                                    <div class="col-md-8 col-xs-12">
                                        <p>
                                            <b>Nombres y Apellidos: </b>
                                            {{ estudiante.nombres }}
                                            {{ estudiante.paterno + " " + estudiante.materno }}
                                        </p>
                                        <p>
                                            <b>Número de Documento: </b>
                                            {{ estudiante.nro_documento }}
                                        </p>
                                        <p>
                                            <b>Fecha Nacimiento: </b>
                                            {{ estudiante.fecha_nac }}
                                        </p>
                                        <p>
                                            <b>Lugar de Nacimiento: </b>
                                            {{ estudiante.ubigeo.departamento+" - "+estudiante.ubigeo.provincia+"-"+ estudiante.ubigeo.distrito}}
                                        </p>
                                        <p>
                                            <b>Año de Egreso: </b>
                                            {{ estudiante.anio_egreso }}
                                        </p>
                                        <div class="alert alert-warning" role="alert">
                                            Declaro bajo juramento que los datos registrados <b>nombres, apellidos, DNI, fecha de nacimiento, lugar de nacimiento, año de egreso y la fotografía son correctos y válidos </b> para el examen de
                                            CEPREUNA.
                                        </div>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#confirmacion">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path
                                                    d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"
                                                />
                                            </svg>
                                            Si
                                        </button>
                                        <button type="button" class="btn btn-danger" @click="rptaNo = true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
                                                />
                                            </svg>
                                            No
                                        </button>
                                    </div>
                                </div>
                                <div v-show="rptaNo" class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info" role="alert">
                                            <b>Ingrese sus datos de manera correcta, recuerde que podra editarlos solo una vez</b>
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-xs-12">
                                        <upload-imagen @imagen64="fields.foto = $event"></upload-imagen>
                                        <div v-if="errors && errors.foto" class="text-danger">
                                            {{ errors.foto[0] }}
                                        </div>
                                    </div>
                                    <div class="col-md-5 col-xs-12">
                                        <form action="" autocomplete="off">
                                            <div class="row">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="nombres">Nombres</label>
                                                        <input type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" />
                                                        <div v-if="errors && errors.nombres" class="text-danger">
                                                            {{ errors.nombres[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="paterno">Apellido Paterno</label>
                                                        <input type="text" class="form-control" name="paterno" id="paterno" v-model="fields.paterno" />
                                                        <div v-if="errors && errors.paterno" class="text-danger">
                                                            {{ errors.paterno[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="materno">Apellido Materno</label>
                                                        <input type="text" class="form-control" name="materno" id="materno" v-model="fields.materno" />
                                                        <div v-if="errors && errors.materno" class="text-danger">
                                                            {{ errors.materno[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="nro_documento">Número de Documento</label>
                                                        <input type="text" class="form-control" name="nro_documento" id="nro_documento" v-model="fields.nro_documento" />
                                                        <div v-if="errors && errors.nro_documento" class="text-danger">
                                                            {{ errors.nro_documento[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="fecha nacimiento">Fecha de Nacimiento</label>
                                                        <input type="date" class="form-control" name="fecha_nac" id="fecha_nac" v-model="fields.fecha_nac" />
                                                        <div v-if="errors && errors.fecha_nac" class="text-danger">
                                                            {{ errors.fecha_nac[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="pais">País de nacimiento</label>
                                                        <!-- <input type="text" class="form-control" name="pais" id="pais" v-model="fields.pais" /> -->
                                                        <v-select :options="paises" :reduce="pais => pais.id" label="denominacion" @input="changePais"></v-select>
                                                        <div v-if="errors && errors.pais" class="text-danger">
                                                            {{ errors.pais[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="departamento">Departamento de nacimiento</label>

                                                        <v-select
                                                            :disabled="disabledDepartamento"
                                                            :options="departamentos"
                                                            :reduce="departamento => departamento.codigo_departamento"
                                                            v-model="fields.departamento"
                                                            label="departamento"
                                                            @input="changeDepartamento"
                                                        ></v-select>
                                                        <div v-if="errors && errors.departamento" class="text-danger">
                                                            {{ errors.departamento[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="provincia">Provincia de nacimiento</label>

                                                        <v-select
                                                            :disabled="disabledProvincia"
                                                            :options="provincias"
                                                            :reduce="provincia => provincia.codigo_provincia"
                                                            v-model="fields.provincia"
                                                            label="provincia"
                                                            @input="changeProvincia"
                                                        ></v-select>
                                                        <div v-if="errors && errors.provincia" class="text-danger">
                                                            {{ errors.provincia[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="distrito">Distrito de nacimiento</label>

                                                        <v-select :disabled="disabledDistrito" :options="distritos" label="distrito" v-model="distrito" @input="changeDistrito"></v-select>
                                                        <div v-if="errors && errors.ubigeo" class="text-danger">
                                                            {{ errors.ubigeo[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="form-group">
                                                        <label for="año de egreso">Año de egreso <small> (Ejemplo: 2019)</small></label>
                                                        <input type="number" class="form-control" name="egreso" id="egreso" v-model="fields.egreso" />
                                                        <div v-if="errors && errors.egreso" class="text-danger">
                                                            {{ errors.egreso[0] }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-success btn-block" @click="ActualizarEstudiante()">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div v-if="errorConfirmacion" class="alert alert-danger" role="alert">
                                            {{ errorConfirmacion }}
                                        </div>
                                        <div v-if="successConfirmacion" class="alert alert-success" role="alert">
                                            {{ successConfirmacion }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-xs" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Confirmación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <p style="size: 16px; font-weight:bold">
                            ¿Esta seguro de guardar los datos registrados?
                        </p>
                        <div class="alert alert-info" role="alert">
                            nota: el cepreuna no se responsabiliza en caso de la persistencia de errores.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" @click="ConfirmarDatos()">Si</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import toastr from "toastr";
import $ from "jquery";

export default {
    data: () => ({
        url: "",
        errorConfirmacion: "",
        successConfirmacion: "",
        rptaNo: false,
        confirmarData: true,
        fields: {
            nombres: "",
            paterno: "",
            materno: "",
            dni:"",
            ubigeo:"",
            fecha_nac:"",
            egreso:"",
        },
        confirmado: false,
        errors: {},
        paises: [],
        departamentos: [],
            provincias: [],
            distritos: [],
            disabledDepartamento: true,
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
    }),
    props: ["estudiante"],
    methods: {
        ConfirmarDatos: function() {
            // $(".loader").show();
            this.errors = {};
            axios
                .post("/estudiante/confirmar-datos/")
                .then(response => {
                    this.errorPago = null;
                    if (response.data.status) {
                        // this.successConfirmacion = "Confirmación de Datos Completa.";
                        this.confirmarData = false;
                        this.confirmado = true;
                        toastr.success("Confirmación de Datos Completa.");
                    } else {
                        this.errorConfirmacion = "Error al confirmar datos, intentelo de nuevo. Si el problema persiste comuniquese con el administrador del sistema";
                    }
                    // $(".loader").hide();
                    // $("#confirmacion").hide();
                })
                .catch(error => {
                    // $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        ActualizarEstudiante: function(){
            $(".loader").show();
            this.errors = {};
            axios
                .post("/estudiante/actualizar-datos/", this.fields)
                .then(response => {
                    this.errorPago = null;
                    if (response.data.status) {
                        toastr.success("Confirmación de Datos Completa. Descargue su declaración jurada en su Perfil");
                        this.confirmado = true;
                    } else {
                        toastr.warning("Error al confirmar datos, intentelo de nuevo. Si el problema persiste comuniquese con el administrador del sistema.");
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        getDepartamentos: function() {
            axios.get("/inscripciones/get-departamentos").then(
                function(response) {
                    this.departamentos = response.data;
                }.bind(this)
            );
        },
        getProvincias: function() {
            axios
                .get("/inscripciones/get-provincias", {
                    params: {
                        codigo: this.fields.departamento
                    }
                })
                .then(
                    function(response) {
                        this.provincias = response.data;
                    }.bind(this)
                );
        },
        getDistritos: function() {
            axios
                .get("/inscripciones/get-distritos", {
                    params: {
                        codigo: this.fields.provincia
                    }
                })
                .then(
                    function(response) {
                        this.distritos = response.data;
                    }.bind(this)
                );
        },
        getPaises: function() {
            axios.get("/inscripciones/get-paises").then(
                function(response) {
                    this.paises = response.data;
                }.bind(this)
            );
        },
        changeDepartamento: function(departamento) {
            // this.fields.departamento = departamento

            if (departamento != null) {
                this.disabledProvincia = false;
                this.disabledDistrito = true;
            } else {
                this.disabledProvincia = true;
                this.disabledDistrito = true;
            }
            this.fields.provincia = null;
            this.distrito = null;

            this.getProvincias();
        },
        changeProvincia: function(provincia) {
            if (provincia != null) {
                this.disabledDistrito = false;
            } else {
                this.disabledDistrito = true;
            }
            this.distrito = null;
            this.getDistritos();
        },
        changeDistrito: function(distrito) {
            this.fields.ubigeo = distrito.id;
        },
        changePais: function(pais) {
            // console.log(data);
            if (pais == 1) {
                this.disabledDepartamento = false;
                this.disabledProvincia = true;
                this.disabledDistrito = true;
            } else {
                this.disabledDepartamento = true;
                this.disabledProvincia = true;
                this.disabledDistrito = true;
                this.fields.departamento = null;
                this.fields.provincia = null;
                this.distrito = null;
            }
            this.fields.pais = pais;
        },
    },
    mounted() {
        this.getPaises();
        this.getDepartamentos();
    }
};
</script>

<style scoped>

</style
