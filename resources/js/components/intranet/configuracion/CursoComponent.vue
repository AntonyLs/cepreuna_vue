<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Periodos
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/cursos/lista/data">
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
                                        <label for="">Area</label>
                                        <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion"></v-select>
                                        <div v-if="errors && errors.area" class="text-danger">{{ errors.area[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="codigo">Codigo</label>
                                        <input type="text" class="form-control" id="codigo" v-model="fields.codigo" />
                                        <div v-if="errors && errors.codigo" class="text-danger">
                                            {{ errors.codigo[0] }}
                                        </div>
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
                                        <label for="color">Color</label>
                                        <input type="text" class="form-control" id="color" v-model="fields.color" />
                                        <div v-if="errors && errors.color" class="text-danger">
                                            {{ errors.color[0] }}
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
                codigo: "",
                denominacion: "",
                areas: "",
                color: ""
            },
            errors: {},
            parentescos: [],
            columns: ["id", "codigo", "denominacion", "area.denominacion", "color", "actions"],
            options: {
                headings: {
                    id: "ID",
                    codigo: "Código",
                    denominacion: "Denominación",
                    "area.denominacion": "Area",
                    color: "Color",
                    actions: "Acciones"
                },
                sortable: ["id", "denominacion"],
                filterable: ["denominacion"],
                filterByColumn: true
            },
            areas: []
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Curso";
            this.fields.codigo = "";
            this.fields.denominacion = "";
            this.fields.area = "";
            this.fields.color = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Curso";
            axios.get("cursos/" + id + "/edit").then(response => {
                this.fields.codigo = response.data.codigo;
                this.fields.denominacion = response.data.denominacion;
                this.fields.area = response.data.area.id;
                this.fields.color = response.data.color;
            });
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("cursos", this.fields)
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
                    .put("cursos/" + this.id, this.fields)
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
        }
    },
    mounted() {
        this.getAreas();
    }
};
</script>

<style></style>
