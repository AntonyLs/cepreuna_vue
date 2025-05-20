<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Colegios
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/colegios/lista/data">
                            <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div>
                            <div slot="actions" slot-scope="props">
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <label for="">Tipo Colegios</label>
                                        <v-select v-model="fields.tipo_colegio" :options="tipo_colegios" :reduce="tipo_colegio => tipo_colegio.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.tipo_colegio" class="text-danger">{{ errors.tipo_colegio[0] }}</div>
                                    </div>
                                </div>
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
                                        <label for="direccion">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" v-model="fields.direccion" />
                                        <div v-if="errors && errors.direccion" class="text-danger">
                                            {{ errors.direccion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="departamento">Departamento</label>
                                        <input type="text" class="form-control" id="departamento" v-model="fields.departamento" />
                                        <div v-if="errors && errors.departamento" class="text-danger">
                                            {{ errors.departamento[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="provincia">Provincia</label>
                                        <input type="text" class="form-control" id="provincia" v-model="fields.provincia" />
                                        <div v-if="errors && errors.provincia" class="text-danger">
                                            {{ errors.provincia[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="distrito">Distrito</label>
                                        <input type="text" class="form-control" id="distrito" v-model="fields.distrito" />
                                        <div v-if="errors && errors.distrito" class="text-danger">
                                            {{ errors.distrito[0] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cerrar
                            </button>
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
    data() {
        return {
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                id: "",
                denominacion: "",
                direccion: "",
                departamento: "",
                provincia: "",
                distrito: "",
                tipo_colegio: ""
            },
            errors: {},
            parentescos: [],
            columns: ["id", "denominacion", "direccion", "departamento", "provincia", "distrito", "tipo_colegio.denominacion", "actions"],
            options: {
                headings: {
                    id: "ID",
                    denominacion: "Denominación",
                    direccion: "Dirección",
                    departamento: "Departamento",
                    provincia: "Provincia",
                    distrito: "Distrito",
                    "tipo_colegio.denominacion": "Tipo Colegio",

                    actions: "Acciones"
                },
                sortable: ["id", "denominacion"],
                filterable: ["denominacion", "direccion", "departamento", "provincia", "distrito"],
                filterByColumn: true
            },
            tipo_colegios: []
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Colegio";
            this.fields.denominacion = "";
            this.fields.direccion = "";
            this.fields.departamento = "";
            this.fields.provincia = "";
            this.fields.distrito = "";
            this.fields.tipo_colegio = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Colegio";
            axios.get("colegios/" + id + "/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.direccion = response.data.direccion;
                this.fields.departamento = response.data.departamento;
                this.fields.provincia = response.data.provincia;
                this.fields.distrito = response.data.distrito;
                this.fields.tipo_colegio = response.data.tipo_colegio.id;
            });
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("colegios", this.fields)
                    .then(response => {
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            else {
                axios
                    .put("colegios/" + this.id, this.fields)
                    .then(response => {
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }
        },
        getTipoColegios: function() {
            axios.get("../get-tipo-colegios").then(
                function(response) {
                    this.tipo_colegios = response.data;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getTipoColegios();
    }
};
</script>

<style></style>
