<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Permisos
                        <button v-if="permissions.includes('crear permisos')" class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>

                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administrar-usuarios/permisos/lista/data">
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button v-if="permissions.includes('editar permisos')" class="btn btn-sm btn-info" @click="editar(props.row.id)">
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
                                        <label class="col-md-12" for="name">Denominación</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="name" v-model="fields.name" id="name" aria-describedby="helpId" placeholder="" />
                                            <div v-if="errors && errors.name" class="text-danger">{{ errors.name[0] }}</div>
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
    props: ["permissions"],
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                name: ""
            },
            rol: "",
            roles: [],
            errors: {},
            selectAdjunto: "Selecione",
            columns: ["id", "name", "actions"],
            options: {
                headings: {
                    id: "Id",
                    name: "Denominación",
                    actions: "Acciones"
                },
                sortable: ["id", "name"],
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
            this.titulo = "Agregar Nuevo Permiso";
            this.fields.name = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Permiso";
            axios.get("permisos/" + id + "/edit").then(response => {
                console.log(response.data.name);
                this.fields.name = response.data.name;
            });
            $("#ModalFormulario").modal("show");
        },

        submit: function() {
            $(".loader").show();
            this.errors = {};
            if (this.edit == 0) {
                axios
                    .post("permisos", this.fields)
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
                    .put("permisos/" + this.id, this.fields)
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
        }
    },
    mounted() {}
};
</script>

<style></style>
