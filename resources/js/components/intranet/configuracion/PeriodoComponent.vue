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
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/periodos/lista/data">
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
                                        <label for="inicio_ciclo">Inicio de ciclo</label>
                                        <input type="text" class="form-control" id="inicio_ciclo" v-model="fields.inicio_ciclo" />
                                        <div v-if="errors && errors.inicio_ciclo" class="text-danger">
                                            {{ errors.inicio_ciclo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fin_ciclo">Fin de ciclo</label>
                                        <input type="text" class="form-control" id="fin_ciclo" v-model="fields.fin_ciclo" />
                                        <div v-if="errors && errors.fin_ciclo" class="text-danger">
                                            {{ errors.fin_ciclo[0] }}
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
                inicio_ciclo: "",
                fin_ciclo: "",
                estado: ""
            },
            errors: {},
            parentescos: [],
            columns: ["id", "inicio_ciclo", "fin_ciclo", "estado", "actions"],
            options: {
                headings: {
                    id: "id",
                    inicio_ciclo: "Inicio de Ciclo",
                    fin_ciclo: "Fin de Ciclo",
                    estado: "Estado",
                    actions: "Acciones"
                },
                sortable: ["id", "denominacion"]
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Periodo";
            this.fields.inicio_ciclo = "";
            this.fields.fin_ciclo = "";
            this.fields.estado = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Periodo";
            axios.get("periodos/" + id + "/edit").then(response => {
                this.fields.inicio_ciclo = response.data.inicio_ciclo;
                this.fields.fin_ciclo = response.data.fin_ciclo;
                this.fields.estado = response.data.estado;
            });
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("periodos", this.fields)
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
                    .put("periodos/" + this.id, this.fields)
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
        }
    }
};
</script>

<style></style>
