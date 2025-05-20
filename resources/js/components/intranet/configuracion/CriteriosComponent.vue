<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Criterios de Evaluacion
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/criterios/lista/data">
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="tipo" slot-scope="props">
                                <template v-if="props.row.tipo=='1'">
                                    Docente
                                </template>
                                <template v-if="props.row.tipo=='2'">
                                    Estudiante
                                </template>
                                <template v-if="props.row.tipo=='3'">
                                    Inscripción Docente
                                </template>
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
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="denominacion">Enunciado del Criterio</label>
                                        <textarea class="form-control" rows="3" v-model="fields.denominacion"></textarea>
                                        <div v-if="errors && errors.denominacion" class="text-danger">
                                            {{ errors.denominacion[0] }}
                                        </div>
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="form-group col-12">
                                        <label for="puntaje">Puntaje</label>
                                        <input type="text" class="form-control" v-model="fields.puntaje" />
                                        <div v-if="errors && errors.puntaje" class="text-danger">
                                            {{ errors.puntaje[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo</label>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo" :value="1" checked />
                                                Docente
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo" :value="2" />
                                                Estudiante
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo" :value="3" />
                                                Inscripcion Docente
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.tipo" class="text-danger">
                                        {{ errors.tipo[0] }}
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
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
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
                puntaje: "1",
                estado: "1",
                denominacion: "",
                tipo: "1"
            },
            errors: {},
            departamentos: [],
            provincias: [],
            distritos: [],
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
            columns: ["id", "denominacion", "estado", "tipo", "puntaje", "actions"],
            options: {
                headings: {
                    id: "id",
                    denominacion: "Criterio de Evaluación",
                    estado: "Estado",
                    tipo: "Tipo",
                    puntaje: "Puntaje",
                    actions: "Acciones"
                },
                sortable: ["id", "denominacion", "estado"]
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
            this.titulo = "Nuevo Criterio";
            this.fields.estado = "1";
            this.fields.denominacion = "";
            this.fields.tipo = "1";
            this.fields.puntaje = "1";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Criterios";
            axios.get("criterios/" + id + "/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.tipo = response.data.tipo;
                this.fields.estado = response.data.estado;
                this.fields.puntaje = response.data.puntaje;
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("criterios", this.fields)
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
                    .put("criterios/" + this.id, this.fields)
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
        }
    },
    mounted() {}
};
</script>

<style></style>
