<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Coordinador Auxiliares
                        <!-- <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button> -->
                    </header>

                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/coordinador-auxiliar/lista/data">
                            <!-- <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div> -->

                            <div slot="actions" slot-scope="props">
                                <a href="#" class="btn btn-sm btn-primary" @click="detalles(props.row.id, $event)">Detalles</a>
                                <a href="#" class="p-0 m-0 h5 text-success" @click="auxilares(props.row.id, $event)">
                                    <i class="fas fa-user-cog"></i>
                                </a>
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalFormulario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelConstancia" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelConstancia">
                            Asignar Auxiliares
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <v-server-table ref="table_auxiliar" :columns="columns_auxiliar" :options="options_auxiliar" url="/intranet/administracion/coordinador-auxiliar-auxiliar/lista/data">
                                <div slot="select" slot-scope="props">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" v-model="checks" :value="props.row.id" />
                                    </div>
                                </div>
                                <!-- <div slot="estado" slot-scope="props">
                                    <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                    <span v-else class="badge badge-danger">Inactivo</span>
                                </div> -->
                            </v-server-table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="button" class="btn btn-success" @click="guardar()">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal ver grupos -->
        <form @submit.prevent="submitGrupos">
            <div class="modal fade" id="ModalGrupos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Asignación de Grupos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body" style="margin-bottom: 130px;">
                            <div class="container-fluid">
                                <!-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="form-group">
                                    <label for="Grupo">Grupo</label>
                                    <v-select multiple v-model="dataGrupos.grupos" :options="grupos" label="grupo"> </v-select>
                                </div>
                            </div>
                            <!-- <v-select v-model="fields.tipo_documento" :options="tipoDocumentos" :reduce="tipo_documento => tipo_documento.id" label="denominacion"></v-select> -->
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
            area: "",
            turno: "",
            sede: "",
            grupo: "",
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                auxiliares: [],
                id: 0
            },
            errors: {},
            columns: ["id", "name", "paterno", "materno", "dni", "actions"],
            options: {
                headings: {
                    id: "Id",
                    name: "Nombres",
                    paterno: "Paterno",
                    materno: "Materno",
                    dni: "DNI",
                    actions: "Acciones"
                },
                sortable: ["id", "name", "paterno", "materno", "dni"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
            },
            columns_auxiliar: ["select", "id", "user.name", "user.paterno", "user.materno", "user.dni"],
            options_auxiliar: {
                headings: {
                    select: "",
                    id: "Id",
                    "user.name": "Nombres",
                    "user.paterno": "Paterno",
                    "user.materno": "Materno",
                    "user.dni": "DNI"
                },
                sortable: ["id", "user.name", "user.paterno", "user.materno", "user.dni"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
            },
            checks: [],
            idC: 0,
            grupos: [],
            dataGrupos: {
                grupos: []
            }
        };
    },

    methods: {
        detalles: function(id, event) {
            event.preventDefault();
            axios.get("coordinador-grupo/" + id + "/edit").then(response => {
                this.dataGrupos.grupos = response.data;
                console.log("response ->>", response.data)
            });
            this.dataGrupos.grupos = null;
             this.id = id;
            this.getGrupoAulas();
            $("#ModalGrupos").modal("show");
            console.log(id);
            this.idC = id;
        },
        auxilares: function(id, event) {
            this.idC = id;
            this.$refs.table_auxiliar.setCustomFilters({ user: this.idC });
            axios.get("/intranet/administracion/coordinador-auxiliar/" + id).then(response => {
                // this.url = "estudiantes/pdf/"+response.data;
                this.checks = response.data.data;
                console.log(response);
            });
            event.preventDefault();
            // this.edit = 0;
            // this.errors = {};
            // this.titulo = "Agregar Nuevo Usuario";
            // this.fields.nombres = "";
            // this.fields.paterno = "";
            // this.fields.materno = "";
            // this.fields.dni = "";
            // this.fields.estado = "1";
            // this.fields.email = "";
            // this.fields.password = "";
            $("#ModalFormulario").modal("show");
        },
        guardar: function() {
            this.fields.auxiliares = this.checks;
            this.fields.id = this.idC;
            axios
                .post("/intranet/administracion/coordinador-auxiliar", this.fields)
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        // this.$refs.table.refresh();
                        toastr.success(response.data.message);

                        $("#ModalFormulario").modal("hide");
                        // this.checks = [];
                        // this.todo = false;
                        // window.location.replace(response.data.url);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
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
        getGrupoAulas: function() {
            axios
                .get("/intranet/get-grupo-aulas-auxiliar", {
                    params: {
                        area: this.area,
                        turno: this.turno,
                        sede: this.sede
                    }
                })
                .then(response => {
                    // this.grupo = response.data[0].id;
                    this.grupos = response.data;
                    console.log(response.data);
                });
        },
        submitGrupos: function() {
            $(".loader").show();
            axios
                .post("coordinador-grupo/" + this.idC, this.dataGrupos)
                .then(response => {
                    $(".loader").hide();
                    // console.log(response);
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalGrupos").modal("hide");
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
    },
    mounted() {
        //this.getGrupoAulas();
    }
};
</script>

<style></style>
