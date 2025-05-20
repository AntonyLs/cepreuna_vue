<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <h4>Estudiantes Matriculados.</h4>
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <!-- <div class="form-group mb-0 col-4">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                        </select>
                                    </div> -->
                                    <div class="form-group mb-0 col-4">
                                        <label for="area">Area</label>
                                        <select class="form-control" v-model="area" @change="changeArea">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Turno</label>
                                        <select class="form-control" v-model="turno" @change="changeTurno">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Grupo</label>
                                        <select class="form-control" v-model="grupo" @change="changeGrupo">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/auxiliar/habilitacion/lista/data">
                            <div slot="actions" slot-scope="props">
                                <template v-if="permissions.includes('habilitar estudiante')">
                                    <button
                                        v-if="props.row.habilitado == '0'"
                                        type="button"
                                        class="p-0 m-0 h5 btn btn-link text-success"
                                        title="Habilitar"
                                        @click="habilitar(props.row.id)"
                                    >
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                </template>
                                <button v-if="permissions.includes('ver constancia estudiante')" class="p-2 m-0 h5 btn btn-link text-dark" @click="constancia(props.row.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                            <div slot="habilitado" slot-scope="props">
                                {{ props.row.habilitado == "1" ? "Si" : "No" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
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
            sede: 0,
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
            columns: ["id", "estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres","habilitado", "area", "turno.denominacion", "grupo", "actions"],
            options: {
                perPage: 50,
                perPageValues: [50, 100],
                headings: {
                    id: "id",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Apellido Paterno",
                    "estudiante.materno": "Apellido Materno",
                    "estudiante.nombres": "Nombres",
                    "estudiante.habilitado": "Habilitado",
                    area: "Area",
                    "turno.denominacion": "Turno",
                    grupo: "Grupo",
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
        habilitar: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de habilitar el estudiante?",
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
                            .put("/intranet/auxiliar/habilitacion/habilitar/" + id)
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
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno });
        },
        changeArea: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.getGrupos();
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno });
        },
        changeTurno: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ grupo: this.grupo, area: this.area, turno: this.turno });
            this.getGrupos();
        },
        getGrupos: function() {
            axios
                .get("/intranet/get-grupo-aulas-auxiliar", {
                    params: {
                        area: this.area,
                        turno: this.turno
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
        }
    },

    mounted: function() {
        this.getAreas();
        this.getTurnos();
        this.getGrupos();
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
