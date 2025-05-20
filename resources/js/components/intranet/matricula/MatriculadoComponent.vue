<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <h4>Estudiantes Matriculados.</h4>
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="row">
                                    <!-- <div class="form-group mb-0 col-4">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                        </select>
                                    </div> -->
                                    <div class="form-group mb-0 col-md-3">
                                        <label for="area">Area</label>
                                        <select class="form-control" v-model="area" @change="changeArea">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-md-3">
                                        <label for="turno">Turno</label>
                                        <select class="form-control" v-model="turno" @change="changeTurno">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-md-3">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-md-3">
                                        <label for="turno">Grupo</label>
                                        <select class="form-control" v-model="grupo" @change="changeGrupo">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo.denominacion }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a :href="'/intranet/reporte/estudiante-carnet-reverso/pdf'" class="btn btn-info mt-4 float-right" target="_blank">Carnet Reverso</a>
                                <a :href="'/intranet/reporte/estudiante-carnet/pdf/'+grupo" class="btn btn-success mt-4 float-right mx-1" target="_blank">Ver carnets</a>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/matricula/matriculado/lista/data">
                            <div slot="actions" slot-scope="props">
                                <button
                                    v-if="permissions.includes('vincular classroom estudiante')"
                                    type="button"
                                    class="p-1 m-0 h5 btn btn-link text-success"
                                    title="Vincular ClassRoom"
                                    @click="cursos(props.row.estudiantes_id)"
                                >
                                    <i class="fas fa-sync"></i>
                                </button>
                                <button
                                    v-if="permissions.includes('desmatricular estudiante')"
                                    type="button"
                                    class="p-1 m-0 h5 btn btn-link text-danger"
                                    title="Desmatricular"
                                    @click="desmatricular(props.row.id)"
                                >
                                    <i class="fas fa-user-minus"></i>
                                </button>
                                <template v-if="props.row.estudiante.panel == '1'">
                                    <button
                                        v-if="permissions.includes('bloquear panel')"
                                        type="button"
                                        class="p-1 m-0 h5 btn btn-link text-danger"
                                        title="Bloquear panel"
                                        @click="bloqueoPanel(props.row.estudiantes_id)"
                                    >
                                        <i class="fa fa-user-lock"></i>
                                    </button>
                                </template>
                                <template v-if="props.row.estudiante.panel == '0'">
                                    <button
                                        v-if="permissions.includes('desbloquear panel')"
                                        type="button"
                                        class="p-1 m-0 h5 btn btn-link text-success"
                                        title="Desbloquear panel"
                                        @click="desbloqueoPanel(props.row.estudiantes_id)"
                                    >
                                        <i class="fas fa-unlock"></i>
                                    </button>
                                </template>
                                <button v-if="permissions.includes('ver constancia estudiante')" class="p-1 m-0 h5 btn btn-link text-dark" @click="constancia(props.row.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Inscrito" : "Pre Inscrito" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="estudiante.panel" slot-scope="props">
                                <template v-if="props.row.estudiante.panel == '1'">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else>
                                    <span class="badge badge-danger">Inactivo</span>
                                </template>
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Constancia -->
        <div class="modal fade" id="ModalConstancia" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelConstancia" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelConstancia">
                            Constancia
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <object
                                class="embed-responsive-item"
                                type="application/pdf"
                                :data="urlConstancia"
                                allowfullscreen
                                width="100%"
                                height="500"
                                style="height: 85vh;"
                            ></object>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalCursos" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cursos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Curso</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(carga, i) in cargas" :key="carga.id">
                                            <th scope="row">{{ i + 1 }}</th>
                                            <td>{{ carga.curso.denominacion }}</td>
                                            <td>
                                                <template v-if="carga.estado == '0'">
                                                    <span class="badge badge-warning">Pendiente</span>
                                                </template>
                                                <template v-if="carga.estado == '1'">
                                                    <span class="badge badge-success">Vinculado</span>
                                                </template>
                                                <template v-if="carga.estado == '2'">
                                                    <span class="badge badge-danger">Error</span>
                                                </template>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm" @click="vincular(carga.id)">Vincular</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <!-- <button type="submit" class="btn btn-primary">Guardar</button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";

export default {
    props: ["permissions"],
    data() {
        return {
            url: "",
            urlConstancia: "",
            sedes: [],
            sede: '',
            areas: [],
            area: "",
            turnos: [],
            turno: "",
            checks: [],
            //*********** */
            local: 0,
            locales: [],
            grupo: "",
            grupos: [],
            //curso
            cargas: [],
            matricula: 0,
            ///table//
            columns: ["id", "estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres", "area", "turno.denominacion", "grupo","estudiante.panel", "actions"],
            options: {
                perPage: 50,
                perPageValues: [50, 100],
                headings: {
                    id: "id",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Apellido Paterno",
                    "estudiante.materno": "Apellido Materno",
                    "estudiante.nombres": "Nombres",
                    area: "Area",
                    "turno.denominacion": "Turno",
                    grupo: "Grupo",
                    "estudiante.panel": "Panel",
                    //   'periodo.denominacion': 'Periodo',
                    actions: "Acciones"
                },
                sortable: ["id"],
                filterable: ["estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres"],
                // listColumns: {
                //     estado: [{
                //             id: "1",
                //             text: 'Inscrito'
                //         },
                //         {
                //             id: "0",
                //             text: 'Pre Inscrito'
                //         }

                //     ]
                // },
                // customFilters: ['correlativo','num_mat']
                filterByColumn: true
            }
        };
    },

    methods: {
        constancia: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.urlConstancia = "/intranet/inscripcion/estudiantes/pdf-constancia/" + response.data;
            });
            // console.log("hgols");
            $("#ModalConstancia").modal("show");
        },
        desmatricular: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de desmatricular al estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        // console.log(id);
                        $(".loader").show();
                        // var datos = {
                        //     docente: docente,
                        //     carga:carga,
                        // }
                        axios
                            .delete("/intranet/matricula/eliminar/" + id)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    this.$refs.table.refresh();
                                } else {
                                    toastr.error(response.data.message);
                                }
                                $(".loader").hide();
                            })
                            .catch(error => {
                                $(".loader").hide();
                            });
                    }
                }
            });
        },
        bloqueoPanel: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de bloquear el panel del estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        // console.log(id);
                        $(".loader").show();
                        // var datos = {
                        //     docente: docente,
                        //     carga:carga,
                        // }
                        axios
                            .put("/intranet/matricula/bloquear-panel/" + id)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    this.$refs.table.refresh();
                                } else {
                                    toastr.error(response.data.message);
                                }
                                $(".loader").hide();
                            })
                            .catch(error => {
                                $(".loader").hide();
                            });
                    }
                }
            });
        },
        desbloqueoPanel: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de desbloquear el panel del estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        // console.log(id);
                        $(".loader").show();
                        // var datos = {
                        //     docente: docente,
                        //     carga:carga,
                        // }
                        axios
                            .put("/intranet/matricula/desbloquear-panel/" + id)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    this.$refs.table.refresh();
                                } else {
                                    toastr.error(response.data.message);
                                }
                                $(".loader").hide();
                            })
                            .catch(error => {
                                $(".loader").hide();
                            });
                    }
                }
            });
        },

        changeGrupo: function() {
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno, sede:this.sede });
        },
        changeArea: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.getGrupos();
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno, sede:this.sede });
        },
        changeTurno: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno, sede:this.sede });
            this.getGrupos();
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
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno, sede:this.sede });
            this.getGrupos();
        },
        getGrupos: function() {
            axios
                .get("/intranet/get-grupo-aulas", {
                    params: {
                        area: this.area,
                        turno: this.turno,
                        sede: this.sede,
                    }
                })
                .then(response => {
                    // this.grupo = response.data[0].id;
                    this.grupos = response.data;
                });
        },
        getTurnos: function() {
            axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        getAreas: function() {
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getCursos: function(id) {
            axios.get("/intranet/get-carga-academica-estudiante/" + id).then(response => {
                // this.turnos = response.data;
                this.cargas = response.data.detalles;
                // console.log(response.data);
                // $("#ModalCursos").modal("show");
            });
        },
        cursos: function(id) {
            this.getCursos(id);
            this.matricula = id;
            $("#ModalCursos").modal("show");
            // axios.get("/intranet/get-carga-academica-estudiante/"+id).then(response => {
            //     // this.turnos = response.data;
            //     this.cargas = response.data.detalles;
            //     console.log(response.data);
            //     $("#ModalCursos").modal("show");
            // });
        },
        vincular: function(id) {
            $(".loader").show();
            axios
                .put("/intranet/matricula/vincular/" + id)
                .then(response => {
                    if (response.data.status == true) {
                        toastr.success(response.data.message);
                        this.getCursos(this.matricula);
                        // this.$refs.table.refresh();
                    } else {
                        toastr.error(response.data.message);
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                });
        }
    },

    mounted: function() {
        this.getAreas();
        this.getTurnos();
        this.getGrupos();
        this.getSedes();
        // this.$nextTick(function () {
        // console.log(this.sede,this.area,this.turno);
        // this.$refs.table.setLoadingState(true);
        // this.$refs.table.setCustomFilters({"grupo":this.grupo,"area":this.area,"turno":this.turno});
        // this.$refs.table.refresh();
        //  this.$refs.table.setLoadingState(false);
        // this.checks = [];
        // })
    }
};
</script>

<style></style>
