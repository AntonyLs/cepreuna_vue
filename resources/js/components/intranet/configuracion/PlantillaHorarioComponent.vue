<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Plantilla Horario
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/plantilla_horario/lista/data">
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
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
                                    <div class="form-group col-6">
                                        <label for="hora_inicio">Hora Inicio</label>
                                        <input type="time" class="form-control" id="hora_inicio" v-model="fields.hora_inicio" />
                                        <div v-if="errors && errors.hora_inicio" class="text-danger">
                                            {{ errors.hora_inicio[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="hora_fin">Hora Fin</label>
                                        <input type="time" class="form-control" id="hora_fin" v-model="fields.hora_fin" />
                                        <div v-if="errors && errors.hora_fin" class="text-danger">
                                            {{ errors.hora_fin[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="estado">Estado</label>
                                        <select class="form-control" id="estado" v-model="fields.estado" >
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                        <div v-if="errors && errors.estado" class="text-danger">
                                            {{ errors.estado[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="turno">Turno</label>
                                        <select name="turno" class="form-control"  id="turno" v-model="fields.turno">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{turno.denominacion}}</option>
                                        </select>
                                        <div v-if="errors && errors.turno" class="text-danger">
                                            {{ errors.turno[0] }}
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
                hora_inicio: "",
                hora_fin: "",
                estado: "1",
                turno: "",
            },
            errors: {},
            sedes: [],
            columns: ["id", "hora_inicio","hora_fin",'estado',"turno.denominacion", "actions"],
            options: {
                headings: {
                    id: "id",
                    hora_inicio: "Hora Inicio",
                    hora_fin: "Hora Fin",
                    estado: "Estado",
                    "turno.denominacion": "Turno",
                    actions: "Acciones"
                },
                sortable: ["id", "hora_inicio"]
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                // filterByColumn:true
            },
            turnos:[]

        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo plantilla horario";
            this.fields.denominacion = "";
            this.fields.direccion = "";
            this.fields.nro_aulas = "";
            this.fields.sede = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar plantilla horario";
            axios.get("plantilla_horario/" + id + "/edit").then(response => {
                this.fields.hora_inicio = response.data.hora_inicio;
                this.fields.hora_fin = response.data.hora_fin;
                this.fields.estado = response.data.estado;
                this.fields.turno = response.data.turnos_id;
            });
            $("#ModalFormulario").modal("show");
        },
        getTurnos:function(){
            axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("plantilla_horario", this.fields)
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
                    .put("plantilla_horario/" + this.id, this.fields)
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
            // console.log("hols");
        }
    },
    mounted() {
        this.getTurnos();
    }
};
</script>

<style></style>
