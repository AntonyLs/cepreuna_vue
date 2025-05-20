<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Habilitación de Grupos
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="areaFiltro">Area</label>
                                    <select class="form-control" id="areaFiltro" v-model="areaF" @change="changeAreaFiltro">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="area in areasFiltro" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turnoFiltro">Turno</label>
                                    <select class="form-control" id="turnoFiltro" v-model="turnoF" @change="changeTurnoFiltro">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="turno in turnosFiltro" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/grupo-aula/lista/data">
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-link text-danger p-1 m-0 h5" title="Eliminar" @click="eliminarGrupo(props.row.id)">
                                    <i class="fas fa-times"></i>
                                </button>
                                <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="grupos_id" slot-scope="props">
                                {{ props.row.grupo.denominacion }}
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
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <!-- <div class="row">
                                    <div class="form-group col-12">
                                        <label for="denominacion">Denominación</label>
                                        <input type="text" class="form-control" id="denominacion" v-model="fields.denominacion"/>
                                        <div v-if="errors && errors.denominacion" class="text-danger">
                                            {{ errors.denominacion[0] }}
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Area</label>
                                        <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.area" class="text-danger">{{ errors.area[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Sede</label>
                                        <v-select v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" @input="changeSedes" label="denominacion"></v-select>
                                        <div v-if="errors && errors.sede" class="text-danger">{{ errors.sede[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Local</label>
                                        <v-select
                                            v-model="fields.local"
                                            :options="locales"
                                            :disabled="disabledLocal"
                                            @input="changeLocales"
                                            :reduce="local => local.id"
                                            label="denominacion"
                                        ></v-select>
                                        <div v-if="errors && errors.local" class="text-danger">{{ errors.local[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Aula</label>
                                        <v-select v-model="fields.aula" :options="aulas" :disabled="disabledAula" :reduce="aula => aula.id" label="codigo"></v-select>
                                        <div v-if="errors && errors.aula" class="text-danger">{{ errors.aula[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Grupo</label>
                                        <v-select v-model="fields.grupo" :options="grupos" :reduce="grupo => grupo.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.grupo" class="text-danger">{{ errors.grupo[0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label for="">Turno</label>
                                        <v-select v-model="fields.turno" :options="turnos" :reduce="turno => turno.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.turno" class="text-danger">{{ errors.turno[0] }}</div>
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
                grupo: "",
                aula: "",
                turno: "",
                area: ""
            },
            errors: {},
            columns: ["id", "aula.local.sede.denominacion", "area.denominacion", "grupos_id", "aula.codigo", "turno.denominacion","actions"],
            options: {
                headings: {
                    id: "id",
                    "aula.local.sede.denominacion": "Sede",
                    "area.denominacion": "Area",
                    grupos_id: "Grupo",
                    "aula.codigo": "Aula",
                    "turno.denominacion": "Turno",
                    actions: "Acciones"
                },
                sortable: ["id", "grupos_id"],
                // filterable: ['correlativo','num_mat','paterno'],
                filterable: [],
                filterByColumn: true
            },
            grupos: [],
            aulas: [],
            turnos: [],
            locales: [],
            sedes: [],
            areas: [],
            areasFiltro: [],
            turnosFiltro: [],
            turnoF: "",
            areaF: "",
            disabledLocal: true,
            disabledAula: true
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Habilitar Nuevo Grupo en Aula";
            this.fields.grupo = "";
            this.fields.aula = "";
            this.fields.turno = "";
            this.fields.area = "";

            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Sede";
            axios.get("sede/" + id + "/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.direccion = response.data.direccion;
                this.fields.estado = response.data.estado;
                this.fields.departamento = response.data.ubigeo.codigo_departamento;
                this.fields.provincia = response.data.ubigeo.codigo_provincia;
                this.fields.distrito = response.data.ubigeo.id;
                this.fields.ubigeo = response.data.ubigeo.id;
                this.disabledProvincia = false;
                this.disabledDistrito = false;
                this.getProvincias();
                this.getDistritos();
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            $(".loader").show();
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("grupo-aula", this.fields)
                    .then(response => {
                        $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                        console.log(response.data);
                    })
                    .catch(error => {
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            else {
                axios
                    .put("grupo-aula/" + this.id, this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        // if (response.data.status) {
                        //     this.$refs.table.refresh();
                        //     toastr.success(response.data.message);
                        //     $("#ModalFormulario").modal("hide");
                        //     // window.location.replace(response.data.url);
                        // } else {
                        //     toastr.warning(response.data.message, "Aviso");
                        // }
                        console.log(response.data);
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

        getGrupos: function() {
            axios.get("../get-only-grupos", {}).then(
                function(response) {
                    this.grupos = response.data;
                }.bind(this)
            );
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
        },
        getLocales: function() {
            axios
                .get("../get-locales", {
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
        getAulas: function() {
            axios
                .get("../get-only-aulas", {
                    params: {
                        local: this.fields.local
                    }
                })
                .then(
                    function(response) {
                        this.aulas = response.data;
                    }.bind(this)
                );
        },
        changeSedes: function(sede) {
            if (sede != null) {
                this.disabledLocal = false;
            } else {
                this.disabledLocal = true;
                this.disabledAula = true;
                this.fields.local = null;
                this.fields.aula = null;
            }
            this.getLocales();
        },
        changeLocales: function(local) {
            if (local != null) {
                this.disabledAula = false;
            } else {
                this.disabledAula = true;
                this.fields.aula = null;
            }
            this.getAulas();
        },
        getAreasFiltro: function() {
            axios.get("../get-areas").then(
                function(response) {
                    this.areasFiltro = response.data;
                }.bind(this)
            );
        },
        getTurnosFiltro: function() {
            axios.get("../get-turnos").then(
                function(response) {
                    this.turnosFiltro = response.data;
                }.bind(this)
            );
        },
        changeAreaFiltro: function() {
            this.$refs.table.setCustomFilters({ area: this.areaF, turno: this.turnoF });
            // this.$refs.table.refresh();
        },
        changeTurnoFiltro: function() {
            this.$refs.table.setCustomFilters({ area: this.areaF, turno: this.turnoF });
        },
        eliminarGrupo: function(id) {
            // console.log("holi");
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de eliminar el grupo aula?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();

                        axios
                            .delete("grupo-aula/" + id)
                            .then(response => {
                                $(".loader").hide();

                                if (response.data.status) {
                                    toastr.success(response.data.message);
                                    this.$refs.table.refresh();
                                } else {
                                    toastr.warning(response.data.message, "Aviso");
                                }
                            })
                            .catch(error => {
                                $(".loader").hide();
                                toastr.warning("error al eliminar correo, intentelo de nuevo", "Aviso");
                            });
                    }
                }
            });
        },
    },
    mounted() {
        // console.log("Component mounted.");
        // this.getTipoDocumentos();
        // this.getPaises();
        this.getGrupos();
        // this.getLocales();
        // this.getColegios();
        this.getSedes();
        this.getAreas();
        // this.getParentescos();
        this.getTurnos();
        this.getAreasFiltro();
        this.getTurnosFiltro();
    }
};
</script>

<style></style>
