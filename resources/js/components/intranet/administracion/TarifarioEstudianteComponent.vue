<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Cronograma de Pagos
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/tarifario-estudiante/lista/data">
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.estudiantes_id)">
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
            <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card mb-1" v-for="(tarifario, key) in tarifarios" :key="tarifario.id">
                                    <div class="card-header py-1">
                                        <b class="text-info">{{ cuotas[tarifario.nro_cuota] }}</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Monto</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.monto[key]" />
                                                </div>
                                                <div v-if="errors && errors.inicio" class="text-danger">
                                                    {{ errors.inicio[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Pagado</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.pagado[key]" />
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Mora</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.mora[key]" />
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 mb-0">
                                                <label for="inicio">Modalidad</label>
                                                <div class="input-group input-group-sm">
                                                    <select id="" class="form-control" v-model="fields.modalidad[key]">
                                                        <option v-for="modalidad in options.listColumns.modalidad" :value="modalidad.id">{{ modalidad.text }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 mb-0">
                                                <label for="inicio">Tipo Estudiante</label>
                                                <div class="input-group input-group-sm">
                                                    <select id="" class="form-control" v-model="fields.tipo_estudiante[key]">
                                                        <option v-for="tipo in options.listColumns.tipo_estudiante" :value="tipo.id">{{ tipo.text }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
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
                id: [],
                monto: [],
                pagado: [],
                mora: [],
                nro_cuota: [],
                modalidad: [],
                tipo_estudiante: []
            },
            errors: {},
            tarifarios: {},
            cuotas: {},

            distrito: "",
            columns: ["id", "estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres", "tipo_estudiante", "modalidad", "actions"],
            options: {
                headings: {
                    id: "id",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Paterno",
                    "estudiante.materno": "Materno",
                    "estudiante.nombres": "Nombres",
                    tipo_estudiante: "Tipo Estudiante",
                    modalidad: "Modalidad",
                    actions: "Acciones"
                },
                sortable: ["id"],
                filterable: ["estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres", "tipo_estudiante", "modalidad"],
                listColumns: {
                    tipo_estudiante: [
                        {
                            id: "1",
                            text: "Normal"
                        },
                        {
                            id: "2",
                            text: "Hijo Trabajador"
                        },
                        {
                            id: "3",
                            text: "Descuento por Trabajador UNA"
                        },
                        {
                            id: "4",
                            text: "Hermanos"
                        },
                        {
                            id: "5",
                            text: "Inscripción por RR"
                        },
                        {
                            id: "6",
                            text: "Servicio Militar"
                        }
                    ],
                    modalidad: [
                        {
                            id: "1",
                            text: "Virtual"
                        },
                        {
                            id: "2",
                            text: "Presencial"
                        }
                    ]
                },
                filterByColumn: true
            }
        };
    },

    methods: {
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Tarifario";
            axios.get("tarifario-estudiante/" + id + "/edit").then(response => {
                this.tarifarios = response.data.tarifarios;
                this.cuotas = response.data.cuotas;
                this.fields.id = response.data.id;
                this.fields.monto = response.data.monto;
                this.fields.pagado = response.data.pagado;
                this.fields.mora = response.data.mora;
                this.fields.nro_cuota = response.data.nro_cuota;
                this.fields.modalidad = response.data.modalidad;
                this.fields.tipo_estudiante = response.data.tipo_estudiante;
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
                    .put("tarifario-estudiante/" + this.id, this.fields)
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
