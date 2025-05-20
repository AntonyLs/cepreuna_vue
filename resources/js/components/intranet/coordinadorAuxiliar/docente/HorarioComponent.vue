<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <select class="form-control" id="area" v-model="area" @change="changeArea">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <select class="form-control" v-model="turno" @change="changeTurno">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sede">Sede</label>
                                    <select class="form-control" v-model="sede" @change="changeSede">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{ sede.denominacion }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grupo">Grupo</label>
                                    <select class="form-control" v-model="grupo" @change="changeGrupo">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo.denominacion }}</option>
                                    </select>
                                    <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="docente">Docente</label>
                                    <v-select :options="docentes" label="text" v-model="docente" @input="changeDocente" :reduce="docente => docente.id">
                                        <template slot="no-options">
                                            Escriba el nombre del docente
                                        </template>
                                        <template slot="option" slot-scope="option">
                                            <div class="d-center">
                                                {{ option.text }}
                                            </div>
                                        </template>
                                        <template slot="selected-option" slot-scope="option">
                                            <div class="selected d-center">
                                                {{ option.text }}
                                            </div>
                                        </template>
                                    </v-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-7">
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Horario</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row justify-content-md-center">
                                            <div class="col-12">
                                                <div v-if="errors && errors.carga" class="text-danger">{{ errors.carga[0] }}</div>
                                            </div>
                                            <table class="table table-bordered col table-sm">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th scope="col" colspan="2">Cursos</th>
                                                        <th scope="col">Docente</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col">Estado</th>
                                                        <th scope="col">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="carga in cargas" :key="carga.id">
                                                        <td v-bind:style="{ background: carga.curso.color }"></td>
                                                        <td :style="{ background: carga.tipo == '1' ? carga.curso.color : '' }">
                                                            <label class="form-check-label" v-bind:for="'Radio-' + carga.id">
                                                                {{ carga.curso.denominacion }}
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <template v-if="carga.docente"> {{ carga.docente.paterno }} {{ carga.docente.materno }} {{ carga.docente.nombres }} </template>
                                                        </td>
                                                        <td>
                                                            <template v-if="carga.docente !== null">
                                                                <template v-if="carga.tipo == '1'">
                                                                    Principal
                                                                </template>
                                                                <template v-else-if="carga.tipo == '2'">
                                                                    Suplente
                                                                </template>
                                                            </template>
                                                        </td>
                                                        <td>
                                                            <template v-if="carga.docente !== null">
                                                                <template v-if="carga.estado == '1'">
                                                                    <span class="badge badge-success">Activo</span>
                                                                </template>
                                                                <template v-else>
                                                                    <span class="badge badge-danger">Inactivo</span>
                                                                </template>
                                                            </template>
                                                        </td>
                                                        <td>
                                                            <template v-if="carga.docente !== null">
                                                                <template v-if="carga.estado == '1'">
                                                                    <button class="btn btn-sm btn-danger" @click="deshabilitarDocente(carga.id)" title="Deshabilitar">
                                                                        <i class="fas fa-user-slash"></i>
                                                                    </button>
                                                                </template>
                                                                <template v-else>
                                                                    <button class="btn btn-sm btn-success" @click="habilitarDocente(carga.id)" title="Habilitar">
                                                                        <i class="fas fa-user-check"></i>
                                                                    </button>
                                                                </template>

                                                                <template v-if="carga.tipo == '1'">
                                                                    <button
                                                                        class="btn btn-sm btn-info"
                                                                        title="Asignar Docente Suplente"
                                                                        @click="asignarDocenteSuplente(carga.id, carga.curso.denominacion)"
                                                                    >
                                                                        <i class="fas fa-user-plus"></i>
                                                                    </button>
                                                                </template>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row justify-content-md-center">
                                            <h6 class="text-primary col-12 text-center">Horario {{ turnoGrupo }}</h6>
                                            <br />
                                            <div v-if="errors && errors.horario" class="text-danger">{{ errors.horario[0] }}</div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered horario-cordinador-auxiliar">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th scope="col" width="120px">Hora</th>
                                                            <th scope="col">Lunes</th>
                                                            <th scope="col">Martes</th>
                                                            <th scope="col">Miercoles</th>
                                                            <th scope="col">Jueves</th>
                                                            <th scope="col">Viernes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="hora in horarios" :key="hora.id">
                                                            <th scope="col" width="120px">{{ hora.hora_inicio }}-{{ hora.hora_fin }}</th>
                                                            <td scope="col" v-for="i in 5" :key="i">
                                                                <template v-for="val in hora.horario">
                                                                    <template v-if="i.toString() == val.dia">
                                                                        <label v-bind:style="{ background: val.curso.color }" :key="val">
                                                                            <!-- <button @click="handleClick" class="btn btn-sm btn-danger p-0" :key="val.id">
                                                                                <i class="fa fa-times"></i>
                                                                            </button> -->
                                                                            {{ val.curso.denominacion }}
                                                                        </label>
                                                                    </template>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Datos docente y Carga</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="font-weight-bold">DNI</label>
                                                <br />
                                                <span>{{ datosDocente.nro_documento }}</span>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="font-weight-bold">Apellidos y Nombre</label>
                                                <br />
                                                <span>{{ datosDocente.paterno }} {{ datosDocente.materno }} {{ datosDocente.nombres }}</span>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="font-weight-bold">Celular</label>
                                                <br />
                                                <span>{{ datosDocente.celular }}</span>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="row">
                                            <table class="table table-sm">
                                                <thead>
                                                    <th>#</th>
                                                    <th>Curso</th>
                                                    <th>Area</th>
                                                    <th>Grupo</th>
                                                    <th>Sede</th>
                                                    <th>Local</th>
                                                    <th>Direccion</th>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(carga, i) in cargaDocente" :key="carga.id">
                                                        <td>{{ i + 1 }}</td>
                                                        <th :style="'background-color:' + carga.curso.color">{{ carga.curso.denominacion }}</th>
                                                        <td>{{ carga.grupo_aula.area.denominacion }}</td>
                                                        <td>{{ carga.grupo_aula.grupo.denominacion }}</td>
                                                        <td>{{ carga.grupo_aula.aula.local.sede.denominacion }}</td>
                                                        <td>{{ carga.grupo_aula.aula.local.denominacion }}</td>
                                                        <td>{{ carga.grupo_aula.aula.local.direccion }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Horario Docente</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row justify-content-md-center" v-for="(horario,index) in disponibilidadHorario" :key="index">
                                            <h6 class="text-primary col-12 text-center">Horario {{ horario.turno }}</h6>
                                            <br />
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped disponibilidad">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th>Hora</th>
                                                            <th width="20px">Lunes</th>
                                                            <th width="20px">Martes</th>
                                                            <th width="20px">Miercoles</th>
                                                            <th width="20px">Jueves</th>
                                                            <th width="20px">Viernes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="hora in horario.disponibilidad" :key="hora.id">
                                                            <th>{{ hora.hora_inicio }}-{{ hora.hora_fin }}</th>
                                                            <td width="20px" v-for="i in 5" :key="i">
                                                                <template v-for="val in hora.horario">
                                                                    <template v-if="i.toString() == val.dia">
                                                                        <!-- <h5 class="my-0"> -->
                                                                        <span style="font-size:14px" class="badge" v-bind:style="{ background: val.curso.color }" :key="val">
                                                                            <span class="badge badge-primary">{{ val.sede }}</span>
                                                                            <span class="badge badge-dark">{{ val.grupo }}</span>
                                                                            {{ val.curso.denominacion }}
                                                                        </span>
                                                                        <!-- </h5> -->
                                                                    </template>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row justify-content-md-center">
                                            <div class="col-md-3" v-for="disponibidadCurso in disponibilidadCursos" :key="disponibidadCurso.id">
                                                <ul class="list-group">
                                                    <li class="list-group-item m-0 py-2 table-info">
                                                        <strong>{{ disponibidadCurso.area }}</strong>
                                                    </li>
                                                    <li class="list-group-item m-0 p-1" v-for="curso in disponibidadCurso.cursos" :key="curso.denominacion">
                                                        {{ curso.denominacion }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="row justify-content-md-center" v-for="turno in disponibilidad" :key="turno.id">
                                            <h6 class="text-primary col-12 text-center">Horario {{ turno.turno }}</h6>
                                            <br />
                                            <table class="table table-bordered disponibilidad">
                                                <thead>
                                                    <tr class="table-info">
                                                        <th scope="col">Hora</th>
                                                        <th scope="col">Lunes</th>
                                                        <th scope="col">Martes</th>
                                                        <th scope="col">Miercoles</th>
                                                        <th scope="col">Jueves</th>
                                                        <th scope="col">Viernes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="hora in turno.disponibilidad" :key="hora.id">
                                                        <th scope="col">{{ hora.hora_inicio }} - {{ hora.hora_fin }}</th>
                                                        <td scope="col" v-for="i in 5" :key="i">
                                                            <template v-for="val in hora.disponibilidad">
                                                                <template v-if="i.toString() == val.dia">
                                                                    {{ val.sede.denominacion }}
                                                                </template>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales -->
        <div class="modal fade" id="ModalSuplente" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Asignar Docente Suplente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="denominacion">Curso</label>
                                    <br />
                                    <span>{{ cursoSuplente }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="docente">Docente</label>
                                        <v-select :options="docentesSuplentes" label="text" v-model="docenteSuplente" :reduce="docente => docente.id">
                                            <template slot="no-options">
                                                Escriba el nombre del docente
                                            </template>
                                            <template slot="option" slot-scope="option">
                                                <div class="d-center">
                                                    {{ option.text }}
                                                </div>
                                            </template>
                                            <template slot="selected-option" slot-scope="option">
                                                <div class="selected d-center">
                                                    {{ option.text }}
                                                </div>
                                            </template>
                                        </v-select>
                                        <div v-if="errors && errors.docente" class="text-danger">{{ errors.docente[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" @click="guardar">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";

export default {
    props: ["userId"],
    data() {
        return {
            errors: {},
            url: "",
            area: "",
            turno: "",
            sede: "",
            grupo: "",
            docente: "",
            cargaAcademica: "",
            curso: "",
            areas: [],
            turnos: [],
            sedes: [],
            grupos: [],
            docentes: [],
            disponibilidad: [],
            disponibilidadHorario: [],
            disponibilidadCursos: [],
            cargas: [],
            horarios: [],
            vacio: false,
            dia: [],
            color: "",
            turnoGrupo: "",
            datosDocente: {},
            cargaDocente: [],

            docenteSuplente: null,
            docentesSuplentes: [],
            cursoSuplente: "",
            idCarga: 0
            // dia2:[],
            // dia3:[],
            // dia4:[],
            // dia5:[],
        };
    },
    methods: {
        guardar: function() {
            $(".loader").show();
            var datos = {
                carga: this.idCarga,
                docente: this.docenteSuplente
            };
            axios
                .post("horario", datos)
                .then(response => {
                    // this.data = response.data.data;
                    if (response.data.status == true) {
                        toastr.success(response.data.message);
                        this.getCargaAcademica();
                        // this.changeDocente();
                        this.docenteSuplente = null;
                    } else {
                        toastr.error(response.data.message);
                    }
                    $(".loader").hide();
                    $("#ModalSuplente").modal("hide");
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        // toastr.error();
                    }
                });
        },
        asignarDocenteSuplente(id, curso) {
            // console.log(id);
            this.cursoSuplente = curso;
            this.idCarga = id;
            this.docenteSuplente = null;
            $("#ModalSuplente").modal("show");
        },
        getAreas: function() {
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getTurnos: function() {
            axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        getSedes: function() {
            axios
                .get("/intranet/get-sedes", {
                    params: {
                        all: true
                    }
                })
                .then(response => {
                    this.sedes = response.data;
                });
        },
        changeSede: function() {
            this.getGrupoAulas();
        },
        async getGrupoAulas() {
            try {
                const response = await axios.get("/intranet/get-grupo-aulas", {
                    params: {
                        area: this.area,
                        turno: this.turno,
                        sede: this.sede
                    }
                });

                const misGruposResponse = await axios.get("/intranet/administracion/coordinador-grupo/"+this.userId+"/edit");
                const misGrupos = misGruposResponse.data;

                // Filter grupos based on comparison with misGrupos
                this.grupos = response.data.filter(item => {
                    return misGrupos.some(misGrupo => misGrupo.id === item.id);
                });

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        },
        changeArea: function() {
            this.getGrupoAulas();
        },
        changeTurno: function() {
            this.getGrupoAulas();
            this.changeDocente();
        },
        changeDocente: function() {
            axios
                .get("/intranet/get-disponibilidad", {
                    params: {
                        docente: this.docente,
                        turno: this.turno
                    }
                })
                .then(response => {
                    // this.grupo = response.data[0].id;
                    this.disponibilidadHorario = response.data.horario;
                    this.disponibilidad = response.data.disponibilidad;
                    this.disponibilidadCursos = response.data.cursos;
                    this.cargaDocente = response.data.carga_academica;
                    this.datosDocente = response.data.docente;
                });
        },
        getDocente: function() {
            axios.get("/intranet/get-docentes").then(response => {
                this.docentes = response.data;
                this.docentesSuplentes = response.data;
            });
        },
        getCargaAcademica: function() {
            axios
                .get("/intranet/get-carga-academica", {
                    params: {
                        grupo: this.grupo
                    }
                })
                .then(response => {
                    this.cargas = response.data.cargaAcademica;
                    this.horarios = response.data.horario;
                    this.turnoGrupo = response.data.turno.denominacion;
                });
        },
        changeGrupo: function() {
            this.getCargaAcademica();
            this.curso = "";
            this.color = "";
            this.dia = [];
            $(".checksH").css("background", "none");
        },

        changeHorario: function(event) {
            var elemento = event.path[1];
            // console.log(event)
            if (event.target.checked) {
                elemento.style.background = this.color;
            } else {
                elemento.style.background = "white";
            }
        },
        deshabilitarDocente: function(carga) {
            this.$confirm({
                title: "Deshabilitar",
                message: "¿Esta seguro de deshabilitar el Docente del Curso?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .put("horario/deshabilitar/" + carga)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    this.getCargaAcademica();
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
        habilitarDocente: function(carga) {
            this.$confirm({
                title: "Habilitar",
                message: "¿Esta seguro de habilitar el Docente?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .put("horario/habilitar/" + carga)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    this.getCargaAcademica();
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
        }
    },
    mounted: function() {
        this.getAreas();
        this.getTurnos();
        this.getGrupoAulas();
        this.getDocente();
        this.getSedes();
    }
};
</script>
<style>
.disponibilidad th,
.disponibilidad td {
    padding: 0px;
}
.horario-cordinador-auxiliar td {
    position: relative;
    /* color:white; */
}
.horario-cordinador-auxiliar td label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    height: 100%;
    font-weight: 500;
}
</style>
