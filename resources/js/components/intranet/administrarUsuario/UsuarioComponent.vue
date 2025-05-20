<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Usuarios
                        <button v-if="permissions.includes('crear usuario')" class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>

                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administrar-usuarios/usuario/lista/data">
                            <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div>

                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button v-if="permissions.includes('editar usuario')" class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <button v-if="permissions.includes('asignar rol usuario')" class="btn btn-sm btn-secondary" @click="asignarRol(props.row.id, props.row.rol_id)">
                                    Rol
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
        <!-- Modal ver cuadernillo -->
        <form @submit.prevent="submitRol">
            <div class="modal fade" id="ModalRol" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Asignación de Rol</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div v-for="role in roles" :key="role.id" class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="role" id="role" v-model="rol" :value="role.name" />
                                        {{ role.name }}
                                    </label>
                                </div>
                                <div v-if="errors && errors.rol" class="text-danger">{{ errors.rol[0] }}</div>
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
    props: ["permissions"],
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                nombres: "",
                paterno: "",
                materno: "",
                dni: "",
                email: "",
                estado: "1"
            },
            rol: "",
            roles: [],
            errors: {},
            selectAdjunto: "Selecione",
            columns: ["id", "name", "paterno", "materno", "dni", "email", "estado", "rol", "actions"],
            options: {
                headings: {
                    id: "Id",
                    name: "Nombres",
                    paterno: "Paterno",
                    materno: "Materno",
                    dni: "DNI",
                    email: "Email",
                    estado: "Estado",
                    rol: "Rol",
                    actions: "Acciones"
                },
                sortable: ["id", "name", "paterno", "materno", "dni", "email", "rol"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
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
            this.fields.password = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Usuario";
            axios.get("usuario/" + id + "/edit").then(response => {
                console.log(response.data.name);
                this.fields.nombres = response.data.name;
                this.fields.paterno = response.data.paterno;
                this.fields.materno = response.data.materno;
                this.fields.dni = response.data.dni;
                this.fields.estado = response.data.estado;
                this.fields.email = response.data.email;
            });
            $("#ModalFormulario").modal("show");
        },
        asignarRol: function(id, role_id) {
            this.id = id;
            axios.get("usuario/rol/" + role_id).then(response => {
                this.rol = response.data.name;
            });

            $("#ModalRol").modal("show");
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
        submitRol: function() {
            $(".loader").show();
            this.errors = {};
            axios
                .put("usuario/rol/" + this.id, { rol: this.rol })
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalRol").modal("hide");
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
                    .post("usuario", this.fields)
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
                    .put("usuario/" + this.id, this.fields)
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
        getRoles: function() {
            axios.get("/intranet/get-roles/").then(response => {
                this.roles = response.data;
            });
        }
    },
    mounted() {
        this.getRoles();
    }
};
</script>

<style></style>
