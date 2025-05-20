<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Devoluciones
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                      <i class="fa fa-plus"></i> Agregar
                      </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/devolucion/devoluciones/lista/data">
                            <div slot="procede" slot-scope="props">
                                <div v-if="props.row.procede == '0'">
                                    No
                                </div>
                                <div v-else-if="props.row.procede == '1'">
                                    Si
                                </div>
                                <div v-else>
                                    En Tramite
                                </div>
                            </div>
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="secuencia" slot-scope="props">
                                {{ props.row.secuencia }}
                            </div>
                            <div slot="monto_pago" slot-scope="props">
                                {{ props.row.monto_pago }}
                            </div>
                            <div slot="fecha_pago" slot-scope="props">
                                {{ dateFormat(props.row.fecha_pago) }}
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit" v-on:keyup.enter="submit">
            <div class="modal fade" id="ModalDevolucion" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                {{ titulo }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="nombres">Nombres</label>
                                            <input type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" />
                                            <div v-if="errors && errors.nombres" class="text-danger">
                                                {{ errors.nombres[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="nro_documento">Número de Documento</label>
                                            <input type="text" class="form-control" name="nro_documento" id="nro_documento" @input="changeDocumento" v-model="fields.nro_documento" />
                                            <div v-if="errors && errors.nro_documento" class="text-danger">
                                                {{ errors.nro_documento[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fecha">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" v-model="fields.fecha" />
                                        <div v-if="errors && errors.fecha" class="text-danger">
                                            {{ errors.fecha[0] }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="nro_registro">Número Solicitud</label>
                                            <input type="text" class="form-control" name="nro_registro" id="nro_registro" @input="changeDocumento" v-model="fields.nro_registro" />
                                            <div v-if="errors && errors.nro_registro" class="text-danger">
                                                {{ errors.nro_registro[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <label class="col-12">Estado</label>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.procede" :value="'0'" checked />
                                                No
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.procede" :value="'1'" />
                                                Si
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.procede" :value="'2'" />
                                                En Tramite
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.procede" class="text-danger">
                                        {{ errors.procede[0] }}
                                    </div>
                                </div>

                                <h5 v-if="false">Validación</h5>
                                <div v-if="errors && errors.tokens" class="text-danger">
                                    {{ errors.tokens[0] }}
                                </div>
                                <div class="row">
                                    <w-pago v-if="validacion" :documento="fields.nro_documento" @result="resultPago = $event"></w-pago>
                                    <br />
                                    <div class="container" style="margin: 14px;">
                                        <div class="row">
                                            <table class="table">
                                                <tbody>
                                                    <tr v-for="result in resultPago.pago" :key="result.secuencia">
                                                        <td>
                                                            <div class="alert alert-secondary" role="alert">
                                                                <b>Secuencia</b>: {{ result.secuencia }} | <b>Monto</b>: {{ result.monto }} | <b> fecha</b>:
                                                                {{ dateFormat(result.fecha) }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="alert alert-success" role="alert">
                                                                {{ result.message }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cerrar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Guardar
                            </button>
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
            validacion: true,
            id: 0,
            titulo: "",
            fields: {
                nombres: "",
                nro_documento: "",
                fecha: "",
                nro_registro: "",
                secuencia: "",
                procede: "0",
                tokens: []
            },
            errors: {},
            departamentos: [],
            provincias: [],
            distritos: [],
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
            columns: ["id", "nombres", "nro_documento", "fecha", "nro_registro", "procede", "secuencia", "monto_pago", "fecha_pago", "actions"],
            options: {
                headings: {
                    id: "id",
                    nombres: "Nombres",
                    nro_documento: "Nro Documento",
                    fecha: "Fecha",
                    nro_registro: "Nro Solicitud",
                    procede: "Estado Procedido",
                    secuencia: "Secuencia",
                    pagos_id: "Pagos",
                    actions: "Acciones"
                },
                sortable: ["id", "fecha", "nro_registro", "procede"],
                filterable: ["nombres", "nro_registro", "procede", "secuencia", "monto_pago", "fecha_pago"],
                filterByColumn: true
            },
            resultPago: {
                pago: []
            },
            montoPagar: 0
        };
    },

    methods: {
        dateFormat: function(date) {
            return moment(date, "YYYY-MM-DD").format("DD-MM-YYYY");
        },
        nuevo: function() {
            this.validacion = true;
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Registro";

            this.fields.nombres = "";
            this.fields.nro_documento = "";
            this.fields.fecha = "";
            this.fields.nro_registro = "";
            this.fields.procede = "";
            this.fields.tokens = [];

            $("#ModalDevolucion").modal("show");
        },
        validateForm() {
            this.errors = {};

            if (!this.fields.nombres) {
                this.errors.nombres = ["El nombre es requerido"];
            }

            if (!this.fields.nro_documento) {
                this.errors.nro_documento = ["El número de documento es requerido"];
            } else if (!/^\d+$/.test(this.fields.nro_documento)) {
                this.errors.nro_documento = ["El número de documento debe contener solo dígitos"];
            }

            if (!this.fields.fecha) {
                this.errors.fecha = ["La fecha es requerida"];
            }

            if (!this.fields.nro_registro) {
                this.errors.nro_registro = ["El número de registro es requerido"];
            }

            if (!this.fields.procede) {
                this.errors.procede = ["El estado de procedimiento es requerido"];
            }

            return Object.keys(this.errors).length === 0;
        },
        editar: function(id) {
            this.validacion = false;
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Datos de Tramite";
            axios.get(`/intranet/devolucion/devoluciones/${id}/edit`).then(response => {
                // console.log(response.data);
                const data = response.data;
                this.fields.nombres = data.nombres;
                this.fields.nro_documento = data.nro_documento;
                this.fields.fecha = data.fecha;
                this.fields.nro_registro = data.nro_registro;
                this.fields.procede = data.procede.toString();
                this.validateForm();
            });

            $("#ModalDevolucion").modal("show");
        },
        submit: function() {
            this.errors = {};
            // Crear el array de tokens como parte de los campos que se van a enviar
            let tokens = [];
            this.resultPago.pago.map(function(pago) {
                tokens.push(typeof pago.token !== "undefined" ? pago.token : "");
            });

            // Agregar el array de tokens al objeto fields (o puedes crear otro objeto)
            if (tokens.length == 0) {
                this.fields.tokens = [""]; // Si no hay tokens, enviar un array vacío
            } else {
                this.fields.tokens = tokens; // Si hay tokens, los agregamos al objeto fields
            }
            if (this.validateForm()) {
                if (this.edit == 0)
                    axios
                        .post("devoluciones", this.fields)
                        .then(response => {
                            // $(".loader").hide();
                            if (response.data.status) {
                                this.$refs.table.refresh();
                                toastr.success(response.data.message);
                                $("#ModalDevolucion").modal("hide");
                                //forzar
                                $(".modal-backdrop").remove();
                                $("body").removeClass("modal-open");
                                $("body").css("padding-right", "");
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
                        .put("devoluciones/" + this.id, this.fields)
                        .then(response => {
                            // $(".loader").hide();
                            if (response.data.status) {
                                this.$refs.table.refresh();
                                toastr.success(response.data.message);
                                $("#ModalDevolucion").modal("hide");
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
        changeDocumento: function() {
            if (this.fields.nro_documento.length == 0) {
                this.statusDocumento = false;
            } else {
                this.statusDocumento = true;
            }
        },
        dateFormat: function(date) {
            return moment(date, "YYYY-MM-DD").format("DD-MM-YYYY");
        }
    },
    mounted() {}
};
</script>

<style></style>
