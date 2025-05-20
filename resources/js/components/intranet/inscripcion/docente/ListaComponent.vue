<template>
    <div>
        <!-- <excel-export
            class="btn btn-success"
            :data="json_data"
            :columns="json_fields"
            :filename="'filename'"
            :sheetname="'sheetname'"
            >
            Descargar excel
        </excel-export> -->
        <vue-confirm-dialog></vue-confirm-dialog>
        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/inscripcion/docente/lista/data">
            <div slot="actions" slot-scope="props">
                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarInscripcion(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                    <i class="fas fa-file-signature"></i>
                </button>
                <!-- <template v-if="props.row.apto == '1'">
                    <button v-if="props.row.docente.docente_apto.enviar_acceso == '0'" type="button" class="p-0 m-0 h5 btn btn-link text-success" @click="enviarAccesos(props.row.docentes_id)">
                        <i class="fas fa-user-check"></i>
                    </button>
                </template> -->
            </div>
            <div slot="apto" slot-scope="props">
                <template v-if="props.row.apto == '1'">
                    <span class="badge badge-success">SI</span>
                </template>
                <template v-else>
                    <span class="badge badge-danger">NO</span>
                </template>
                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
            </div>
            <!-- <div slot="publico" slot-scope="props">
                <template v-if="props.row.apto == '1'">
                    <span class="badge badge-success" v-if="props.row.docente.docente_apto.enviar_acceso == '1'">SI</span>
                    <span class="badge badge-danger" v-else>NO</span>
                </template>
                <template v-else>
                    <span class="badge badge-danger">NO</span>
                </template>
            </div> -->
        </v-server-table>

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
        <!-- Modal  Editar de Inscripcion-->
        <div class="modal fade" id="modalEditarInscripcion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Inscripción de un Docente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-100">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-md-5 col-xs-12">
                                    <div class="card mb-2 shadow-sm rounded">
                                        <div class="row no-gutters">
                                            <div class="col-md-4 col-xs-12">
                                                <img :src="'../../storage/fotos/' + infoDocente.foto" class="card-img border-right" alt="" />
                                            </div>
                                            <div class="col-md-8 col-xs-12">
                                                <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Datos Personales del Estudiante</b></div>
                                                <div class="card-body py-2">
                                                    <b>Nombres y Apellidos:</b>
                                                    <p class="m-0">{{ infoDocente.nombres }} {{ infoDocente.paterno }} {{ infoDocente.materno }}</p>
                                                    <b>Número de Documento:</b>
                                                    <p class="m-0">{{ infoDocente.nroDocumento }}</p>
                                                    <b>Condicion:</b>
                                                    <p class="m-0">{{ infoDocente.condicion == 2 ? "Unap - " + this.infoDocente.codigoUnap : "Particular" }}</p>
                                                    <b>Celular:</b>
                                                    <p class="m-0">{{ infoDocente.celular }}</p>
                                                    <b>Correo Electrónico</b>
                                                    <p class="m-0">{{ infoDocente.email }}</p>
                                                    <b>Grados y Título</b>
                                                    <template v-for="grado in infoDocente.gradoAcademico">
                                                            <p class="m-0"><b>{{ grado.grado_academico.denominacion }}</b>: {{ grado.programa.denominacion }}</p>
                                                            <hr class="my-0">
                                                    </template>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-2 shadow-sm rounded">
                                        <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Datos de Inscripción</b></div>
                                        <div class="card-body py-2">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <b>Apto</b>
                                                    <h6>
                                                        <span class="badge" :class="[infoInscripcion.apto == 1 ? 'badge-success' : 'badge-danger']">{{
                                                            infoInscripcion.apto == 1 ? "SI" : "NO"
                                                        }}</span>
                                                    </h6>
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <b>Puntaje</b>
                                                    <p class="m-0">{{ infoInscripcion.puntaje }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <!-- <button
                                            v-if="permissions.includes('calificar docente')"
                                            type="button"
                                            class="btn btn-success"
                                            @click="clickCalificar"
                                            :disabled="infoInscripcion.apto != 0 ? true : false"
                                        >
                                            <i class="fas fa-check"></i> Calificar
                                        </button> -->
                                        <button v-if="permissions.includes('editar curso docente')" type="button" class="btn btn-warning" @click="editarCursos">
                                            <i class="fas fa-edit"></i> Editar Cursos
                                        </button>
                                        <button v-if="permissions.includes('editar disponibilidad docente')" type="button" class="btn btn-primary" @click="editarDisponibilidad">
                                            <i class="fas fa-calendar"></i> Editar Disponibilidad
                                        </button>
                                        <!-- <button type="button" class="btn btn-success"><i class="far fa-check-square"></i> Validar Inscripción</button> -->
                                    </div>
                                </div>
                                <div class="col-md-7 col-xs-12">
                                    <div class="card mb-2 shadow-sm rounded" :class="[calificar ? 'd-block' : 'd-none']">
                                        <div class="card-header text-white text-center bg-success py-1">
                                            <b class="card-title mb-0">Calificar</b>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row">
                                                <div class="form-group col-12">
                                                    <label for="puntaje">Puntaje</label>
                                                    <input type="number" class="form-control" id="puntaje" v-model="fieldsCalificar.puntaje" />
                                                    <div v-if="errors && errors.puntaje" class="text-danger">
                                                        {{ errors.puntaje[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <label class="col-12">Apto</label>
                                                <div class="col-md-6">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" v-model="fieldsCalificar.apto" :value="1" />
                                                            Si
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" v-model="fieldsCalificar.apto" :value="0" />
                                                            No
                                                        </label>
                                                    </div>
                                                </div>
                                                <div v-if="errors && errors.apto" class="text-danger">
                                                    {{ errors.apto[0] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button type="button" class="btn btn-success" @click="submitCalificar">Guardar</button>
                                        </div>
                                    </div>
                                    <div class="card mb-2 shadow-sm rounded" :class="[editCursos ? 'd-block' : 'd-none']">
                                        <div class="card-header text-white text-center bg-warning py-1">
                                            <b class="card-title mb-0">Cursos</b>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row justify-content-md-center">
                                                <div class="col-md-4" v-for="item in areasCursos" :key="item.id">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr class="bg-warning">
                                                                <th scope="col">{{ item.area }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="curso in item.cursos" :key="curso.id">
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input
                                                                            class="form-check-input"
                                                                            type="checkbox"
                                                                            :value="curso.id"
                                                                            v-bind:id="'CheckCurso-' + curso.id"
                                                                            v-model="allCursos[curso.id]"
                                                                        />
                                                                        <label class="form-check-label" v-bind:for="'CheckCurso-' + curso.id">
                                                                            {{ curso.curso.denominacion }}
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button type="button" class="btn btn-warning" @click="submitCursos">Guardar</button>
                                        </div>
                                    </div>
                                    <div class="card mb-2 shadow-sm rounded" :class="[editDisponibilidad ? 'd-block' : 'd-none']">
                                        <div class="card-header text-white text-center bg-primary py-1">
                                            <b class="card-title mb-0">Disponibilidad</b>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row justify-content-md-center" v-for="turno in turnoHorario" :key="turno.id">
                                                <h6 class="col-12 text-center">Horario {{ turno.turno }}</h6>
                                                <br />
                                                <table class="table table-bordered table-sm">
                                                    <thead>
                                                        <tr class="table-primary">
                                                            <th scope="col">Hora</th>
                                                            <th scope="col">Lunes</th>
                                                            <th scope="col">Martes</th>
                                                            <th scope="col">Miercoles</th>
                                                            <th scope="col">Jueves</th>
                                                            <th scope="col">Viernes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="hora in turno.horario" :key="hora.id">
                                                            <th scope="col">{{ hora.hora_inicio }} - {{ hora.hora_fin }}</th>
                                                            <td scope="col">
                                                                <select v-model="lu[hora.id]">
                                                                    <option value=""></option>
                                                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'1-' + hora.id + '-' + disponibidad">{{
                                                                        disponiblesSede[disponibidad]
                                                                    }}</option>
                                                                </select>
                                                            </td>
                                                            <td scope="col">
                                                                <select v-model="ma[hora.id]">
                                                                    <option value=""></option>
                                                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'2-' + hora.id + '-' + disponibidad">{{
                                                                        disponiblesSede[disponibidad]
                                                                    }}</option>
                                                                </select>
                                                            </td>
                                                            <td scope="col">
                                                                <select v-model="mi[hora.id]">
                                                                    <option value=""></option>
                                                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'3-' + hora.id + '-' + disponibidad">{{
                                                                        disponiblesSede[disponibidad]
                                                                    }}</option>
                                                                </select>
                                                            </td>
                                                            <td scope="col">
                                                                <select v-model="ju[hora.id]">
                                                                    <option value=""></option>
                                                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'4-' + hora.id + '-' + disponibidad">{{
                                                                        disponiblesSede[disponibidad]
                                                                    }}</option>
                                                                </select>
                                                            </td>
                                                            <td scope="col">
                                                                <select v-model="vi[hora.id]">
                                                                    <option value=""></option>
                                                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'5-' + hora.id + '-' + disponibidad">{{
                                                                        disponiblesSede[disponibidad]
                                                                    }}</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <button type="button" class="btn btn-primary" @click="submitDisponibilidad">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            errors: {},
            calificar: false,
            editCursos: false,
            editDisponibilidad: false,
            fieldsCalificar: { puntaje: 0, apto: 0 },
            fieldsCursos: { cursos: [] },
            fields: { sede: [], disponibilidad: [] },
            areasCursos: [],
            allCursos: [],
            turnoHorario: [],
            lu: [],
            ma: [],
            mi: [],
            ju: [],
            vi: [],
            disponiblesSede: [],
            ///table//
            columns: ["id", "apto", "puntaje", "docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres", "actions"],
            options: {
                headings: {
                    id: "id",
                    // publico: "publico",
                    apto: "apto",
                    puntaje: "puntaje",
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    actions: "Acciones"
                },
                sortable: ["id", "apto", "puntaje"],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true
            },
            infoInscripcion: {
                id: "",
                apto: "",
                puntaje: ""
            },
            infoDocente: {
                nombres: "",
                paterno: "",
                materno: "",
                nroDocumento: "",
                condicion: "",
                codigoUnap: "",
                email: "",
                celular: "",
                foto: "",
                gradoAcademico: [],
                programa: ""
            },
            json_fields: [
                {
                    label: "id",
                    field: "id"
                },
                {
                    label: "apto",
                    field: "apto",
                    dataFormat: this.formatApto
                },
                {
                    label: "puntaje",
                    field: "puntaje"
                },
                {
                    label: "Documento",
                    field: "docente.nro_documento"
                },
                {
                    label: "Apellido Paterno",
                    field: "docente.paterno"
                },
                {
                    label: "Apellido Materno",
                    field: "docente.materno"
                },
                {
                    label: "Nombres",
                    field: "docente.nombres"
                }
            ],
            json_data: []
        };
    },

    methods: {
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "docentes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        editarInscripcion: function(id) {
            this.calificar = false;
            this.editCursos = false;
            this.editDisponibilidad = false;
            axios.get("/intranet/inscripcion/docente/" + id).then(response => {
                this.infoDocente.nombres = response.data.docente.nombres;
                this.infoDocente.paterno = response.data.docente.paterno;
                this.infoDocente.materno = response.data.docente.materno;
                this.infoDocente.nroDocumento = response.data.docente.nro_documento;
                this.infoDocente.condicion = response.data.docente.condicion;
                this.infoDocente.codigoUnap = response.data.docente.codigo_unap;
                this.infoDocente.email = response.data.docente.email;
                this.infoDocente.celular = response.data.docente.celular;
                this.infoDocente.foto = response.data.docente.foto;
                this.infoDocente.gradoAcademico = response.data.inscripcion.grados;
                // this.infoDocente.programa = response.data.docente.programa.denominacion;

                this.infoInscripcion.id = response.data.inscripcion.id;
                this.infoInscripcion.apto = response.data.inscripcion.apto;
                this.infoInscripcion.puntaje = response.data.inscripcion.puntaje;

                this.fieldsCalificar.apto = response.data.inscripcion.apto;
                this.fieldsCalificar.puntaje = response.data.inscripcion.puntaje;

                // console.log(this.infoInscripcion.apto);
            });

            // this.infoInscripcion.id = id;
            $("#modalEditarInscripcion").modal("show");
        },
        clickCalificar: function() {
            this.errors = {};
            this.editDisponibilidad = false;
            this.editCursos = false;
            this.calificar = true;
        },
        submitCalificar: function() {
            $(".loader").show();
            axios
                .post("docente/calificar/" + this.infoInscripcion.id, this.fieldsCalificar)
                .then(response => {
                    if (response.data.status) {
                        this.calificar = false;
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        this.infoInscripcion.apto = this.fieldsCalificar.apto;
                        this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        editarCursos: function() {
            this.allCursos = [];
            var cursos = [];
            axios
                .get("/intranet/get-docente-cursos", {
                    params: {
                        inscripcion: this.infoInscripcion.id
                    }
                })
                .then(response => {
                    // console.log(response.data);
                    this.areasCursos = response.data;
                    for (const i in this.areasCursos) {
                        for (const k in this.areasCursos[i].cursos) {
                            console.log(this.areasCursos[i].cursos[k].select);
                            cursos[this.areasCursos[i].cursos[k].id] = this.areasCursos[i].cursos[k].select;
                        }
                    }

                    this.allCursos = cursos;
                });

            this.editDisponibilidad = false;
            this.editCursos = true;
            this.calificar = false;
            // console.log("holi");
        },
        submitCursos: function() {
            $(".loader").show();
            var cursos = [];
            for (const prop in this.allCursos) {
                // console.log(this.estado[prop]);
                if (this.allCursos[prop] != null && this.allCursos[prop] != false) cursos.push(prop);
            }

            this.fieldsCursos.cursos = cursos;

            axios
                .post("docente/cursos/" + this.infoInscripcion.id, this.fieldsCursos)
                .then(response => {
                    if (response.data.status) {
                        // this.editarCursos();
                        this.editCursos = false;
                        // this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        // this.infoInscripcion.apto = this.fieldsCalificar.apto;
                        // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        editarDisponibilidad: function() {
            var disponibilidad = [];
            this.editDisponibilidad = true;
            this.editCursos = false;
            this.calificar = false;
            this.fields.sede = [];
            this.disponiblesSede = [];
            axios
                .get("/intranet/get-docente-disponibilidad", {
                    params: {
                        inscripcion: this.infoInscripcion.id
                    }
                })
                .then(response => {
                    this.lu = [];
                    this.ma = [];
                    this.mi = [];
                    this.ju = [];
                    this.vi = [];
                    this.turnoHorario = response.data.horario;
                    disponibilidad = response.data.disponibilidad;

                    for (const i in response.data.sede) {
                        this.fields.sede.push(response.data.sede[i].sedes_id);
                        this.disponiblesSede[response.data.sede[i].sedes_id] = response.data.sede[i].sede.denominacion;
                    }
                    for (const i in disponibilidad) {
                        switch (disponibilidad[i].dia) {
                            case "1":
                                this.lu[disponibilidad[i].plantilla_horarios_id] = "1-" + disponibilidad[i].plantilla_horarios_id + "-" + disponibilidad[i].sedes_id;
                                break;
                            case "2":
                                this.ma[disponibilidad[i].plantilla_horarios_id] = "2-" + disponibilidad[i].plantilla_horarios_id + "-" + disponibilidad[i].sedes_id;
                                break;
                            case "3":
                                this.mi[disponibilidad[i].plantilla_horarios_id] = "3-" + disponibilidad[i].plantilla_horarios_id + "-" + disponibilidad[i].sedes_id;
                                break;
                            case "4":
                                this.ju[disponibilidad[i].plantilla_horarios_id] = "4-" + disponibilidad[i].plantilla_horarios_id + "-" + disponibilidad[i].sedes_id;
                                break;
                            case "5":
                                this.vi[disponibilidad[i].plantilla_horarios_id] = "5-" + disponibilidad[i].plantilla_horarios_id + "-" + disponibilidad[i].sedes_id;
                                break;
                            default:
                                break;
                        }
                    }
                });
        },
        submitDisponibilidad: function() {
            $(".loader").show();
            var disponible = [];
            var dias = [];

            dias = dias.concat(this.lu);
            dias = dias.concat(this.ma);
            dias = dias.concat(this.mi);
            dias = dias.concat(this.ju);
            dias = dias.concat(this.vi);

            console.log(dias);
            dias.forEach(function(val, key) {
                // console.log(val);
                if (val != "") {
                    if (val != null) {
                        disponible.push(val);
                    }
                }
            });
            this.fields.disponibilidad = disponible;

            axios
                .post("docente/disponibilidad/" + this.infoInscripcion.id, this.fields)
                .then(response => {
                    if (response.data.status) {
                        this.editDisponibilidad = false;
                        toastr.success(response.data.message);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        formatApto: function(value) {
            if (value == "1") {
                return "Si";
            }
            return "No";
        },
        enviarAccesos: function(id){
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de mostrar el docente apto de manera publica?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .get("/intranet/inscripcion/docente/enviar-accesos/" + id)
                            .then(response => {
                                $(".loader").hide();
                                // console.log(response.data);
                                if (response.data.status) {
                                    toastr.success(response.data.message);
                                    this.$refs.table.refresh();
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
        }
    },
    mounted() {
        axios.get("/intranet/inscripcion/docente/lista/data?all=true").then(response => {
            // this.url = "docentes/pdf/"+response.data;
            this.json_data = response.data;
            // console.log(response.data);
        });
    }
};
</script>

<style></style>
