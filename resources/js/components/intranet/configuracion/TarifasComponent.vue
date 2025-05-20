<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Tarifas
                        <!-- <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button> -->
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/tarifas/lista/data">
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="tipo_estudiante" slot-scope="props">
                                {{ props.row.tipo_estudiante == "1" ? "Normal" : "Por Descuento" }}
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
                                        <label for="denominacion">Denominación</label>
                                        <input type="text" class="form-control" id="denominacion" v-model="fields.denominacion" />
                                        <div v-if="errors && errors.denominacion" class="text-danger">
                                            {{ errors.denominacion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="denominacion">Importe</label>
                                        <input type="number" step="0.01" class="form-control" id="denominacion" v-model="fields.importe" />
                                        <div v-if="errors && errors.importe" class="text-danger">
                                            {{ errors.importe[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo Estudiante</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_estudiante" :value="1" checked />
                                                Normal
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo_estudiante" :value="2" />
                                                Por Descuento
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.tipo_estudiante" class="text-danger">
                                        {{ errors.tipo_estudiante[0] }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="departamento">Tipo Colegio</label>
                                            <v-select
                                                :options="tipoColegios"
                                                :reduce="tipo_colegio => tipo_colegio.id"
                                                v-model="fields.tipo_colegio"
                                                label="denominacion"
                                            ></v-select>
                                            <div v-if="errors && errors.departamento" class="text-danger">
                                                {{ errors.departamento[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="departamento">Concepto</label>
                                            <v-select :options="conceptos" :reduce="concepto => concepto.id" v-model="fields.concepto" label="denominacion"></v-select>
                                            <div v-if="errors && errors.departamento" class="text-danger">
                                                {{ errors.departamento[0] }}
                                            </div>
                                        </div>
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
                estado: "1",
                denominacion: "",
                importe: "",
                tipo_estudiante: "1",
                tipo_colegio: null,
                concepto: null
            },
            errors: {},
            tipoColegios: [],
            conceptos: [],
            columns: ["id", "denominacion", "importe", "tipo_estudiante", "tipo_colegio.denominacion", "concepto_pago.denominacion", "estado", "actions"],
            options: {
                headings: {
                    id: "id",
                    denominacion: "Denominacion",
                    estado: "Importe",
                    tipo_estudiante: "Tipo Estudiante",
                    "tipo_colegio.denominacion": "Tipo Colegio",
                    "concepto_pago.denominacion": "Concepto",
                    estado: "Estado",
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
            this.titulo = "Nueva Tarifa";
            this.fields.estado = "1";
            this.fields.denominacion = "";
            this.fields.importe = "";
            this.fields.tipo_estudiante = "";
            this.fields.tipo_colegio = null;
            this.fields.concepto = null;

            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Tarifa";

            axios.get("tarifas/" + id + "/edit").then(response => {
                console.log(response.data);

                this.fields.denominacion = response.data.denominacion;
                this.fields.importe = response.data.importe;
                this.fields.estado = response.data.estado;
                this.fields.tipo_estudiante = response.data.tipo_estudiante;
                this.fields.tipo_colegio = response.data.tipo_colegio.id;
                this.fields.concepto = response.data.concepto_pago.id;

                // this.getProvincias();
                // this.getDistritos();
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("tarifas", this.fields)
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
                    .put("tarifas/" + this.id, this.fields)
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
        },
        getTipoColegios: function() {
            axios.get("/intranet/get-tipo-colegios").then(
                function(response) {
                    this.tipoColegios = response.data;
                }.bind(this)
            );
        },
        getConceptos: function() {
            axios.get("/intranet/get-conceptos").then(
                function(response) {
                    this.conceptos = response.data;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getTipoColegios();
        this.getConceptos();
    }
};
</script>

<style></style>
