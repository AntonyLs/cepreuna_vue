<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Cronograma de Pagos
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/cronograma-pagos/lista/data">
                            <div slot="estado" slot-scope="props">
                                <!-- {{ props.row.estado == "1" ? "Activo" : "Inactivo" }} -->
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
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
                                        <label for="inicio">Inicio</label>
                                        <input type="date" class="form-control" name="inicio" id="inicio" v-model="fields.inicio" />
                                        <div v-if="errors && errors.inicio" class="text-danger">
                                            {{ errors.inicio[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fin">Fin</label>
                                        <input type="date" class="form-control" name="fin" id="fin" v-model="fields.fin" />
                                        <div v-if="errors && errors.fin" class="text-danger">
                                            {{ errors.fin[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="nro_cuota">Número de Cuota</label>
                                        <input type="number" step="0.01" class="form-control" id="nro_cuota" v-model="fields.nro_cuota" />
                                        <div v-if="errors && errors.nro_cuota" class="text-danger">
                                            {{ errors.nro_cuota[0] }}
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
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="observacion">Observación</label>
                                        <textarea class="form-control" rows="3" id="observacion" v-model="fields.observacion"></textarea>
                                        <div v-if="errors && errors.observacion" class="text-danger">
                                            {{ errors.observacion[0] }}
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
                estado: "1",
                denominacion: "",
                tipo: "1"
            },
            errors: {},
            departamentos: [],
            provincias: [],
            distritos: [],
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
            columns: ["id", "inicio", "fin", "nro_cuota", "estado", "observacion", "actions"],
            options: {
                headings: {
                    id: "id",
                    inicio: "Inicio",
                    fin: "Fin",
                    nro_cuota: "N° Cuota",
                    estado: "Estado",
                    observacion: "Observacion",
                    actions: "Acciones"
                },
                sortable: ["id", "estado"]
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Cronograma de Pago";
            this.fields.estado = "1";
            this.fields.inicio = "";
            this.fields.fin = "";
            this.fields.nro_cuota = "";
            this.fields.observacion = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Cronograma de Pago";
            axios.get("cronograma-pagos/" + id + "/edit").then(response => {
                this.fields.estado = response.data.estado;
                this.fields.inicio = response.data.inicio;
                this.fields.fin = response.data.fin;
                this.fields.nro_cuota = response.data.nro_cuota;
                this.fields.observacion = response.data.observacion;
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("cronograma-pagos", this.fields)
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
                    .put("cronograma-pagos/" + this.id, this.fields)
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
        }
    },
    mounted() {}
};
</script>

<style></style>
