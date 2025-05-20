<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Auxiliares
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>

                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/auxiliar/lista/data">
                            <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div>

                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-secondary" @click="asignarGrupos(props.row.id)">
                                    Grupos
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
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="nombres">Nombres</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="nombres" v-model="fields.nombres" id="nombres" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.nombres" class="text-danger">{{ errors.nombres[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="paterno">Apellido Paterno</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="paterno" v-model="fields.paterno" id="paterno" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.paterno" class="text-danger">{{ errors.paterno[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="materno">Apellido Materno</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="materno" v-model="fields.materno" id="materno" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.materno" class="text-danger">{{ errors.materno[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="dni">DNI</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="dni" v-model="fields.dni" id="dni" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.dni" class="text-danger">{{ errors.dni[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="dni">Celular</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="dni" v-model="fields.celular" id="celular" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.celular" class="text-danger">{{ errors.celular[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo</label>
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
                                <hr />
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="email">Correo Electrónico</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="email" v-model="fields.email" id="email" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <label class="col-md-12" for="password">Contraseña</label>
                                        <div class="col-md-12">
                                            <input
                                                type="password"
                                                class="form-control"
                                                name="password"
                                                v-model="fields.password"
                                                id="password"
                                                aria-describedby="helpId"
                                                placeholder=""
                                            />
                                            <div v-if="errors && errors.password" class="text-danger">{{ errors.password[0] }}</div>
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
                                <!-- <div class="col-md-3"> -->
                                    <!-- <div class="form-group">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                        </select>
                                    </div> -->
                                <!-- </div> -->
                                <div class="form-group">
                                        <label for="Grupo">Grupo</label>
                                <v-select multiple v-model="dataGrupos.grupos" :options="grupos" label="denominacion"> </v-select>
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
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            sede: "",
            fields: {
                nombres: "",
                paterno: "",
                materno: "",
                dni: "",
                celular: "",
                email: "",
                estado: "1"
            },

            grupos: [],
            sedes: [],
            errors: {},
            selectAdjunto: "Selecione",
            columns: ["id", "user.name", "user.paterno", "user.materno", "user.dni", "user.email", "telefono", "actions"],
            options: {
                headings: {
                    id: "Id",
                    "user.name": "Nombres",
                    "user.paterno": "Paterno",
                    "user.materno": "Materno",
                    "user.dni": "DNI",
                    "user.email": "Email",
                    telefono: "Telefono",
                    actions: "Acciones"
                },
                sortable: ["id", "user.name", "user.paterno", "user.materno", "user.dni", "user.email"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
            },
            dataGrupos: {
                grupos: []
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Agregar Nuevo Usuario";
            this.fields.nombres = "";
            this.fields.paterno = "";
            this.fields.materno = "";
            this.fields.dni = "";
            this.fields.estado = "1";
            this.fields.email = "";
            this.fields.celular = "";
            this.fields.password = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Usuario";
            axios.get("auxiliar/" + id + "/edit").then(response => {
                // console.log(response.data.name);
                this.fields.nombres = response.data.user.name;
                this.fields.paterno = response.data.user.paterno;
                this.fields.materno = response.data.user.materno;
                this.fields.dni = response.data.user.dni;
                this.fields.celular = response.data.telefono;
                this.fields.estado = response.data.user.estado;
                this.fields.email = response.data.user.email;
            });
            $("#ModalFormulario").modal("show");
        },
        asignarGrupos: function(id) {
            axios.get("auxiliar-grupo/" + id + "/edit").then(response => {
                this.dataGrupos.grupos = response.data;
            });
            this.dataGrupos.grupos = null;
            this.id = id;
            this.getGrupoAulas();

            $("#ModalGrupos").modal("show");
        },
        filesChange(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjunto = "Selecione";
            } else {
                this.selectAdjunto = file.name;
                this.fields.file = file;
            }
        },
        submitGrupos: function() {
            $(".loader").show();
            axios
                .post("auxiliar-grupo/" + this.id, this.dataGrupos)
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
        submit: function() {
            $(".loader").show();
            this.errors = {};
            if (this.edit == 0) {
                axios
                    .post("auxiliar", this.fields)
                    .then(response => {
                        $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
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
            } else {
                axios
                    .put("auxiliar/" + this.id, this.fields)
                    .then(response => {
                        $(".loader").hide();
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
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }
            // console.log("hols");
        },
        getGrupoAulas: function() {
            axios.get("/intranet/get-grupo-aulas-aux/",{
                params: {
                    sede: this.sede,
                },
            }).then(response => {
                this.grupos = response.data;
                // this.grupos.map(g => {
                //     return (g.denominacion = g.grupo.denominacion);
                // });
            });
        },
        getSedes:function(){
             axios.get("/intranet/get-sedes",{
                params: {
                    all: true
                },
            }).then(response => {
                this.sedes = response.data;
            });
        },
        changeSede:function(){
            this.getGrupoAulas();
        },
    },
    mounted() {
        // this.getGrupoAulas();
        this.getSedes();
    }
};
</script>

<style></style>
