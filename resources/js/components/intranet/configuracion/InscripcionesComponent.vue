<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Fecha de Inscripción
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/inscripciones/lista/data">
                            <div slot="tipo_inscripcion" slot-scope="props">
                                {{ props.row.tipo_inscripcion == "1" ? "Normal" : "Extemporaneo" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="tipo_usuario" slot-scope="props">
                                {{ props.row.tipo_usuario == "1" ? "Estudiante" : "Docente" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="periodo.inicio_ciclo" slot-scope="props">
                                {{ props.row.periodo.inicio_ciclo }} - {{ props.row.periodo.fin_ciclo }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div
                class="modal fade"
                id="ModalFormulario"
                data-backdrop="static"
                data-keyboard="false"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ titulo }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="inicio">Inicio</label>
                                        <input type="datetime-local" class="form-control" id="inicio" v-model="fields.inicio" />
                                        <div v-if="errors && errors.inicio" class="text-danger">
                                            {{ errors.inicio[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fin">Fin</label>
                                        <input type="datetime-local" class="form-control" id="fin" v-model="fields.fin" />
                                        <div v-if="errors && errors.fin" class="text-danger">
                                            {{ errors.fin[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo Inscripción</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_inscripcion" :value="1" checked />
                                                Normal
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_inscripcion" :value="2" />
                                                Extemporaneo
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.tipo_inscripcion" class="text-danger">
                                        {{ errors.tipo_inscripcion[0] }}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo Usuario</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_usuario" :value="1" checked />
                                                Estudiante
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_usuario" :value="2" />
                                                Docente
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.tipo_usuario" class="text-danger">
                                        {{ errors.tipo_usuario[0] }}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Estado</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.estado" :value="1" checked />
                                                Activo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.estado" :value="0" />
                                                Inactivo
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.estado" class="text-danger">
                                        {{ errors.estado[0] }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="observacion">Observacion</label>

                                        <textarea class="form-control" name="" id="observacion" v-model="fields.observacion" rows="2"></textarea>

                                        <div v-if="errors && errors.observacion" class="text-danger">
                                            {{ errors.observacion[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";
import "vue-select/dist/vue-select.css";

export default {
    // props:[],
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                inicio: "",
                fin: "",
                tipo_inscripcion: "1",
                tipo_usuario: "1",
                estado: "1",
                observacion: ""
            },
            errors: {},
            departamentos: [],
            provincias: [],
            distritos: [],
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
            columns: ["id", "inicio", "fin", "tipo_inscripcion", "tipo_usuario", "estado", "observacion", "periodo.inicio_ciclo", "actions"],
            options: {
                headings: {
                    id: "id",
                    inicio: "Inicio",
                    fin: "Fin",
                    tipo_inscripcion: "Tipo Inscripcion",
                    tipo_usuario: "Tipo Usuario",
                    estado: "Estado",
                    observacion: "Observacion",
                    "periodo.inicio_ciclo": "Periodo",
                    actions: "Acciones"
                },
                sortable: ["id", "tipo_inscripcion", "tipo_usuario", "estado"]
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                // filterByColumn:true
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nueva Fecha de Inscripción";

            this.fields.estado = "1";
            this.fields.inicio = "";
            this.fields.fin = "";
            this.fields.tipo_inscripcion = "1";
            this.fields.tipo_usuario = "1";
            this.fields.observacion = "";

            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Fecha de Inscripción";
            axios.get("inscripciones/" + id + "/edit").then(response => {
                this.fields.estado = response.data.estado;
                this.fields.inicio = response.data.inicio;
                this.fields.fin = response.data.fin;
                this.fields.tipo_inscripcion = response.data.tipo_inscripcion;
                this.fields.tipo_usuario = response.data.tipo_usuario;
                this.fields.observacion = response.data.observacion;
            });

            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("inscripciones", this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                            // window.location.replace(response.data.url);
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        // $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            else {
                axios
                    .put("inscripciones/" + this.id, this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                            // window.location.replace(response.data.url);
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        // $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }
            // console.log("hols");
        }
    },
    mounted() {}
};
</script>

<style></style>
