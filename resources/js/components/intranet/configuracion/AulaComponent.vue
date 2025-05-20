<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="col-md-12">Aulas</div>
                        <hr />
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <label for="sede">Sede</label>
                                <select class="form-control" v-model="sede" @change="changeSedeFilter">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{ sede.denominacion }}</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <div class="float-right">
                                    <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/aulas/lista/data">
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
                                        <label for="sede">Sede</label>
                                        <v-select v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" @input="changeSede" label="denominacion"></v-select>
                                        <!-- <select type="text" class="form-control" id="sede" v-model="fields.sede" @input="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.denominacion }}</option>
                                        </select> -->
                                        <div v-if="errors && errors.sede" class="text-danger">
                                            {{ errors.sede[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="sede">Local</label>
                                        <v-select v-model="fields.local" :options="locales" :reduce="local => local.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.local" class="text-danger">
                                            {{ errors.sede[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="denominacion">Código</label>
                                        <input type="text" class="form-control" id="codigo" v-model="fields.codigo" />
                                        <div v-if="errors && errors.codigo" class="text-danger">
                                            {{ errors.codigo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="direccion">Capacidad</label>
                                        <input type="number" class="form-control" id="capacidad" v-model="fields.capacidad" />
                                        <div v-if="errors && errors.capacidad" class="text-danger">
                                            {{ errors.capacidad[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="nro_aulas">Pabellon</label>
                                        <input type="text" class="form-control" id="pabellon" v-model="fields.pabellon" />
                                        <div v-if="errors && errors.pabellon" class="text-danger">
                                            {{ errors.pabellon[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="nro_aulas">Piso</label>
                                        <input type="text" class="form-control" id="piso" v-model="fields.piso" />
                                        <div v-if="errors && errors.piso" class="text-danger">
                                            {{ errors.piso[0] }}
                                        </div>
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
                codigo: "",
                capacidad: "",
                pabellon: "",
                piso: "",
                sede: null,
                local: null
            },
            errors: {},
            sedes: [],
            locales: [],
            columns: ["id", "codigo", "capacidad", "pabellon", "piso", "local.denominacion", "local.sede.denominacion", "actions"],
            options: {
                headings: {
                    id: "ID",
                    codigo: "Codigo",
                    capacidad: "Capacidad",
                    pabellon: "Pabellon",
                    piso: "Piso",
                    "local.denominacion": "Local",
                    "local.sede.denominacion": "Sede",
                    actions: "Acciones"
                },
                sortable: ["id", "codigo"],
                filterable: ["codigo", "local.denominacion"],
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                filterByColumn: true
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Registro";
            this.fields.denominacion = "";
            this.fields.direccion = "";
            this.fields.nro_aulas = "";
            this.fields.sede = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Registro";
            axios.get("aulas/" + id + "/edit").then(response => {
                this.fields.codigo = response.data.codigo;
                this.fields.capacidad = response.data.capacidad;
                this.fields.pabellon = response.data.pabellon;
                this.fields.piso = response.data.piso;
                this.fields.sede = response.data.local.sedes_id;
                this.getLocalSede();
                this.fields.local = response.data.locales_id;
                // this.locales[0] = {
                //     id: response.data.local.id,
                //     denominacion: response.data.local.denominacion
                // };
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("aulas", this.fields)
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
                    .put("aulas/" + this.id, this.fields)
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

        getSede: function() {
            axios.get("/intranet/get-sedes").then(
                function(response) {
                    this.sedes = response.data;
                }.bind(this)
            );
        },
        getLocalSede: function() {
            axios
                .get("/intranet/get-locales", {
                    params: {
                        sede: this.fields.sede
                    }
                })
                .then(
                    function(response) {
                        this.locales = response.data;
                    }.bind(this)
                );
        },
        changeSede: function() {
            if (this.fields.sede) {
                this.getLocalSede();
            } else {
                this.locales = [];
            }
        },
        changeSedeFilter: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ sede: this.sede });
        }
    },
    mounted() {
        this.getSede();
    }
};
</script>

<style></style>
