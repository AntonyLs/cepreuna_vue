<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Curricula
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/curricula/lista/data">
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="tipo" slot-scope="props">
                                {{ props.row.tipo == "1" ? "Presencial" : "Virtual" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-secondary" @click="detalle(props.row.id)">
                                    Detalle
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
                                        <label for="resolucion">Resolucion</label>
                                        <input type="text" class="form-control" id="resolucion" v-model="fields.resolucion" />
                                        <div v-if="errors && errors.resolucion" class="text-danger">
                                            {{ errors.resolucion[0] }}
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
                                        <label for="tipo">Tipo</label>
                                        <select class="form-control" id="tipo" v-model="fields.tipo" >
                                            <option value="1">Presencial</option>
                                            <option value="2">Virtual</option>
                                        </select>
                                        <div v-if="errors && errors.tipo" class="text-danger">
                                            {{ errors.tipo[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="area">Area</label>
                                        <select class="form-control" id="area" v-model="fields.area" >
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="area in areas" :value="area.id" :key="area.id">{{area.denominacion}}</option>
                                        </select>
                                        <div v-if="errors && errors.area" class="text-danger">
                                            {{ errors.area[0] }}
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
        <div
                class="modal fade"
                id="ModalDetalle"
                data-backdrop="static"
                data-keyboard="false"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Detalle</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="form-group col-12">
                                    <button class="btn btn-primary float-right" @click="nuevoD"><i class="fa fa-plus"></i> Nuevo</button>
                                    <v-server-table ref="tableDetalle" :columns="columnsD" :options="optionsD" url="/intranet/configuracion/curricula/detalle/lista/data">
                                        <div slot="horario_inscripcion" slot-scope="props">
                                            {{ props.row.horario_inscripcion == "1" ? "Activo" : "Inactivo" }}
                                            <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                                        </div>
                                        <div slot="actions" slot-scope="props">
                                            <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                            <button class="btn btn-sm btn-danger" @click="eliminarD(props.row.id)">
                                                eliminar
                                            </button>
                                            <button  class="btn btn-sm btn-primary" @click="editarD(props.row)">
                                                editar
                                            </button>
                                        </div>
                                    </v-server-table>
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

        <form @submit.prevent="submitD">
            <div
                class="modal fade"
                id="ModalFormularioD"
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
                            <h5 class="modal-title" id="exampleModalLabel">Detalle</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="horas">horas</label>
                                        <input type="text" class="form-control" id="horas" v-model="fieldsD.horas" />
                                        <div v-if="errors && errors.horas" class="text-danger">
                                            {{ errors.horas[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="horario_inscripcion">Horario Inscripcion</label>
                                        <select class="form-control" id="horario_inscripcion" v-model="fieldsD.horario_inscripcion" >
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                        <div v-if="errors && errors.horario_inscripcion" class="text-danger">
                                            {{ errors.horario_inscripcion[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="curso">curso</label>
                                        <select class="form-control" id="curso" v-model="fieldsD.curso" >
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="curso in cursos" :value="curso.id" :key="curso.id">{{ curso.denominacion }}</option>
                                        </select>
                                        <div v-if="errors && errors.curso" class="text-danger">
                                            {{ errors.curso[0] }}
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
            editD: 0,
            id: 0,
            titulo: "",
            fields: {
                estado: "1",
                resolucion: "",
                estado: "1",
                tipo: "1",
                area: ""
            },
            errors: {},
            sedes: [],
            columns: ["id", "resolucion","estado","tipo","area.denominacion", "actions"],
            options: {
                headings: {
                    id: "id",
                    resolucion: "Resolucion",
                    estado: "Estado",
                    tipo: "Tipo",
                    "area.denominacion": "Area",
                    actions: "Acciones"
                },
                sortable: ["id", "denominacion"]
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                // filterByColumn:true
            },
            areas:[],
            cursos:[],

            fieldsD: {
                horas: 1,
                horario_inscripcion: "1",
                curso: "",
            },
            columnsD: ["id", "horas","horario_inscripcion","curso.denominacion", "actions"],
            optionsD: {
                headings: {
                    id: "id",
                    horas: "hora",
                    horario_inscripcion: "horario_inscripcion",
                    "curso.denominacion": "Curso",
                    actions: "Acciones"
                },
                sortable: ["id", "hora"]
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                // filterByColumn:true
            },
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo curricula";
            this.fields.resolucion = "";
            this.fields.estado ="1";
            this.fields.tipo = "1";
            this.fields.area = "";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar curricula";
            axios.get("curricula/" + id + "/edit").then(response => {
                this.fields.resolucion = response.data.resolucion;
                this.fields.estado = response.data.estado;
                this.fields.tipo = response.data.tipo;
                this.fields.area = response.data.areas_id;
            });
            $("#ModalFormulario").modal("show");
        },
        getAreas:function(){
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getCursos:function(){
            axios.get("/intranet/get-cursos").then(response => {
                this.cursos = response.data;
            });
        },
        detalle:function(id){
            this.id = id;
            this.$refs.tableDetalle.setCustomFilters({
                // sede: this.sede,
                curricula: id,
                // turno: this.turno,
                // tipo_descuento: this.tipo_descuento,
                // estado: this.estado
            });
            $("#ModalDetalle").modal("show");
        },
        nuevoD:function(){
            this.editD = 0;
            $("#ModalFormularioD").modal("show");
        },
        editarD:function(data){
            this.editD = data.id;
            $("#ModalFormularioD").modal("show");
            this.fieldsD.horas = data.horas;
            this.fieldsD.horario_inscripcion= data.horario_inscripcion;
            this.fieldsD.curso = data.cursos_id;
        },
        eliminarD:function(id){
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de eliminar el detalle de curricula?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .delete("curricula/detalle/eliminar/" + id)
                            .then(response => {
                                $(".loader").hide();
                                console.log(response.data);
                                if (response.data.status) {
                                    toastr.success(response.data.message);
                                    this.$refs.tableDetalle.refresh();
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
        submitD: function() {
            this.errors = {};
            if (this.editD == 0)
                axios
                    .post("curricula/detalle/guardar/"+this.id, this.fieldsD)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.tableDetalle.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormularioD").modal("hide");
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
                    .put("curricula/detalle/editar/" + this.editD, this.fieldsD)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.tableDetalle.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormularioD").modal("hide");
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
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("curricula", this.fields)
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
                    .put("curricula/" + this.id, this.fields)
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
        this.getAreas();
        this.getCursos();
    }
};
</script>

<style></style>
