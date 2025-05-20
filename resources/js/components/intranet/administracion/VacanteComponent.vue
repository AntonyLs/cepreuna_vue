<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Vacantes
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/vacantes/lista/data">
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
                                        <label for="">Sede</label>
                                        <v-select v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.sede" class="text-danger">{{ errors.sede[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="">Area</label>
                                        <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.area" class="text-danger">{{ errors.area[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="">Turno</label>
                                        <v-select v-model="fields.turno" :options="turnos" :reduce="turno => turno.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.turno" class="text-danger">{{ errors.turno[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="cantidad">Cantidad</label>
                                        <input type="number" class="form-control" id="cantidad" v-model="fields.cantidad" />
                                        <div v-if="errors && errors.cantidad" class="text-danger">
                                            {{ errors.cantidad[0] }}
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
                sede: "",
                area: "",
                turno: "",
                cantidad: "",
                estado: ""
            },
            errors: {},
            parentescos: [],
            columns: ["id", "sede.denominacion", "area.denominacion", "turno.denominacion", "cantidad", "estado", "actions"],
            options: {
                headings: {
                    id: "ID",
                    "sede.denominacion": "Sede",
                    "area.denominacion": "Area",
                    "turno.denominacion": "Turno",
                    cantidad: "Cantidad",
                    estado: "Estado",
                    actions: "Acciones"
                },
                sortable: ["id"],
                filterable: [],
                filterByColumn: true
            },
            sedes: [],
            areas: [],
            turnos: []
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Colegio";
            this.fields.sede = "";
            this.fields.area = "";
            this.fields.turno = "";
            this.fields.cantidad = "";
            this.fields.estado = "";

            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Colegio";
            axios.get("vacantes/" + id + "/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.sede = response.data.sede.id;
                this.fields.area = response.data.area.id;
                this.fields.turno = response.data.turno.id;
                this.fields.cantidad = response.data.cantidad;
                this.fields.estado = response.data.estado;
            });
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("vacantes", this.fields)
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
                    .put("vacantes/" + this.id, this.fields)
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
        getAreas: function() {
            axios.get("../get-areas").then(
                function(response) {
                    this.areas = response.data;
                }.bind(this)
            );
        },
        getSedes: function() {
            axios.get("../get-sedes").then(
                function(response) {
                    this.sedes = response.data;
                }.bind(this)
            );
        },
        getTurnos: function() {
            axios.get("../get-turnos").then(
                function(response) {
                    this.turnos = response.data;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getAreas();
        this.getSedes();
        this.getTurnos();
    }
};
</script>

<style></style>
