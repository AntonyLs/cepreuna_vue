<style>
.table thead tr:nth-child(1) {
    /* background-color:rgb(99, 111, 131);
        color:white; */
}
</style>
<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <h4>Estudiantes Inscritos.</h4>
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <div class="form-group mb-0 col-4">
                                        <label for="sede">Sede</label>
                                        <select class="form-control" v-model="sede" @change="changeSede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{ sede.denominacion }}</option>
                                        </select>
                                    </div>
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
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="row justify-content-end">
                                    <button v-if="permissions.includes('matricular estudiante')" class="btn btn-success col-md-6" @click="matricular">Matricular</button>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <div class="form-check">
                            <input type="checkbox" id="todo" class="form-check-input" v-model="todo" @change="seleccionarTodo($event)" value="true" />
                            <label for="todo" class="mb-0">Seleccionar Todo</label>
                        </div>
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/matricula/pendiente/lista/data">
                            <div slot="select" slot-scope="props">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" v-model="checks" :value="props.row.id" />
                                </div>
                            </div>
                            <div slot="actions" slot-scope="props">
                                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Inscrito" : "Pre Inscrito" }}
                                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalFicha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ficha</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh;"></object>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div
                class="modal fade"
                id="ModalMatricula"
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
                            <h5 class="modal-title" id="exampleModalLabel">Matricular</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div v-if="errors && errors.area" class="text-danger">
                                        {{ errors.area[0] }}
                                    </div>
                                    <div v-if="errors && errors.turno" class="text-danger">
                                        {{ errors.turno[0] }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="local">Local</label>
                                        <select type="text" class="form-control" v-model="fields.local" id="local" @change="changeLocal">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="local in locales" :value="local.id" :key="local.id">{{ local.denominacion }}</option>
                                        </select>
                                        <div v-if="errors && errors.local" class="text-danger">
                                            {{ errors.local[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="grupo">Grupo</label>
                                        <select type="text" class="form-control" v-model="fields.grupo" id="grupo">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="group in grupos" :value="group.id" :key="group.id">{{ group.grupo.denominacion }} | {{ group.aula.codigo }}</option>
                                        </select>
                                        <div v-if="errors && errors.grupo" class="text-danger">
                                            {{ errors.grupo[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group col-12">
                                        <div v-if="errors && errors.checks" class="text-danger">
                                            {{ errors.checks[0] }}
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
var area = 0;
var turno = 0;
var sede = 0;
export default {
    props: ["permissions"],
    data() {
        return {
            url: "",
            errors: {},
            todo: false,
            fields: { local: "", grupo: "", checks: [] },
            sedes: [],
            sede: 0,
            areas: [],
            area: 0,
            turnos: [],
            turno: 0,
            checks: [],
            //*********** */
            local: 0,
            locales: [],
            grupo: "",
            grupos: [],
            ///table//
            columns: [
                "select",
                "id",
                "correlativo",
                "cantidad_inscrito",
                "estudiante.nro_documento",
                "estudiante.paterno",
                "estudiante.materno",
                "estudiante.nombres",
                "sede.denominacion",
                "area.denominacion",
                "turno.denominacion",
                "actions"
            ],
            options: {
                perPage: 50,
                perPageValues: [50, 100],
                headings: {
                    select: "",
                    id: "id",
                    correlativo: "Correlativo",
                    cantidad_inscrito: "Cant. Inscripciones",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Apellido Paterno",
                    "estudiante.materno": "Apellido Materno",
                    "estudiante.nombres": "Nombres",
                    "sede.denominacion": "Sede",
                    "area.denominacion": "Area",
                    "turno.denominacion": "Turno",
                    //   'periodo.denominacion': 'Periodo',
                    actions: "Acciones"
                },
                sortable: ["id", "correlativo", "cantidad_inscrito"],
                filterable: ["correlativo", "cantidad_inscrito", "estudiante.nro_documento", "estudiante.paterno", "estudiante.materno", "estudiante.nombres"],
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
        submit: function() {
            // console.log("hols");
            $(".loader").show();
            this.fields.checks = this.checks;
            this.fields.area = this.area;
            this.fields.turno = this.turno;
            // console.log(this.fields);
            axios
                .post("/intranet/matricula/guardar", this.fields)
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);

                        $("#ModalMatricula").modal("hide");
                        this.checks = [];
                        this.todo = false;
                        // window.location.replace(response.data.url);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    // $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        matricular: function() {
            // console.log("gols");
            if (this.checks.length != 0 && this.sede != "" && this.area != "" && this.turno != "") {
                this.errors = {};
                this.fields.local = "";
                this.fields.grupo = "";
                this.grupos = [];
                this.getLocales();
                // this.getGrupos();
                $("#ModalMatricula").modal("show");
            } else {
                var aviso = "<ul>";
                aviso += "<li>Seleccionar como mínimo un estudiante.</li>";
                aviso += "<li>Seleccionar una Sede.</li>";
                aviso += "<li>Seleccionar un Area.</li>";
                aviso += "<li>Seleccionar un Turno.</li>";
                aviso += "</ul>";
                toastr.warning(aviso, "Aviso");
            }
        },
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "estudiantes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        seleccionarTodo: function(event) {
            if (event.target.checked) {
                // console.log("SEleccionar todo");
                this.checks = [];
                var datos = this.$refs.table.data;
                // datos.forEach(function(i,val){
                //     this.checks.push(val.id);
                // });
                for (let value of datos) {
                    this.checks.push(value.id);
                    // console.log(value);
                }
                // $('.tipo_descuento').prop('checked',false);
                // event.path[0].checked = true;
                // this.fields.tipo_descuento = event.target.value;
                // this.statusDescuento = true;
            } else {
                // console.log("deselccionar");
                this.checks = [];
                // this.fields.tipo_descuento = "1";
                // this.statusDescuento = false;
            }
            // console.log(this.$refs.table.data);
        },
        changeLocal: function() {
            this.grupos = [];
            this.getGrupos();
        },
        changeSede: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ sede: this.sede, area: this.area, turno: this.turno });
        },
        changeArea: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ sede: this.sede, area: this.area, turno: this.turno });
        },
        changeTurno: function() {
            // this.sede = value;
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ sede: this.sede, area: this.area, turno: this.turno });
        },
        getSedes: function() {
            axios.get("/intranet/get-sedes").then(response => {
                this.sedes = response.data;
            });
        },
        getLocales: function() {
            axios
                .get("/intranet/get-locales", {
                    params: {
                        sede: this.sede
                    }
                })
                .then(response => {
                    this.locales = response.data;
                });
        },
        getGrupos: function() {
            axios
                .get("/intranet/get-grupos", {
                    params: {
                        area: this.area,
                        turno: this.turno,
                        local: this.fields.local
                    }
                })
                .then(response => {
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
        }
    },

    mounted: function() {
        this.getSedes();
        this.getAreas();
        this.getTurnos();
        // this.$nextTick(function () {
        // console.log(this.sede,this.area,this.turno);
        // this.$refs.table.setLoadingState(true);
        // this.$refs.table.setCustomFilters({"sede":this.sede,"area":this.area,"turno":this.turno});
        // this.$refs.table.refresh();
        //  this.$refs.table.setLoadingState(false);
        // this.checks = [];
        // })
    }
};
</script>

<style></style>
