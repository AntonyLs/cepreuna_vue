<template>
    <div>
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
                                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grupo">Grupo</label>
                                    <select class="form-control" v-model="grupo" @change="changeGrupo">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo }}</option>
                                    </select>
                                    <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="grupo">Fecha</label>
                                    <input type="date" class="form-control" v-model="fecha" @change="changeFecha"  />
                                    <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <!-- <button class="btn btn-warning" @click="regularizar">Regularizar Asistencia</button> -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Horario</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row justify-content-md-center">
                                            <h6 class="text-primary col-12 text-center">Horario {{ turnoGrupo }}</h6>
                                            <br />
                                            <div v-if="errors && errors.horario" class="text-danger">{{ errors.horario[0] }}</div>
                                            <table class="table table-bordered horario-auxiliar col-md-6">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th scope="col">Hora</th>
                                                        <th scope="col" >
                                                            {{ dia }} <br />
                                                            {{ fechaSelect }}
                                                        </th>
                                                        <!-- <th scope="col">Martes</th>
                                                        <th scope="col">Miercoles</th>
                                                        <th scope="col">Jueves</th>
                                                        <th scope="col">Viernes</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="hora in horarios" :key="hora.id">
                                                        <th scope="col">{{ hora.hora_inicio }} - {{ hora.hora_fin }}</th>
                                                        <td scope="col">
                                                            <!-- <template v-for="val in hora.horario"> -->
                                                            <!-- <template v-if="i.toString()==val.dia"> -->
                                                            <!-- <template>

                                                                </template> -->
                                                            <template v-if="hora.horario">
                                                                <template v-if="!hora.horario.estado" >
                                                                    <a 
                                                                        href="#"
                                                                        v-bind:style="{ background: hora.horario.curso.color }"
                                                                        :key="hora.horario.id"
                                                                        @click="modalAsistencia(hora.horario.carga.id, hora.horario.carga.docentes_id, $event)"
                                                                    >
                                                                        {{ hora.horario.curso.denominacion }}
                                                                        <!-- <span class="badge badge-secondary">Hols</span> -->
                                                                    </a>
                                                                </template>
                                                                <template v-else>
                                                                    <label :key="hora.horario.id" class="bg-secondary text-dark">
                                                                        <button class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>
                                                                        {{ hora.horario.curso.denominacion }}
                                                                        <span class="badge" :class="'badge-' + pintarEstado(hora.horario.estado)[0]">{{
                                                                            pintarEstado(hora.horario.estado)[1]
                                                                        }}</span>
                                                                    </label>
                                                                </template>
                                                            </template>

                                                            <!-- </template> -->
                                                            <!-- </template> -->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row justify-content-center">
                                            <div class="col-md-4 text-center">
                                                <a :href="urlParte" class="btn btn-danger" download>Descargar Parte PDF</a>
                                                <a :href="urlExport" class="btn btn-danger my-2" download>Exportar PDF</a>
                                                <!-- <a :href="urlParte" class="btn btn-danger" target="_blank">Descargar Parte PDF</a> -->
                                            </div>
                                        </div>
                                        <div class="row justify-content-md-center">
                                            <div class="col-md-8">
                                                <table class="table table-bordered col table-sm">
                                                    <thead class="table-primary">
                                                        <tr>
                                                            <th scope="col" colspan="2">Cursos</th>
                                                            <th scope="col">Docente</th>
                                                            <th scope="col">Tipo</th>
                                                            <th scope="col">Datos</th>
                                                            <th scope="col">Enlace</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="carga in cargas" :key="carga.id">
                                                            <td v-bind:style="{ background: carga.curso.color }"></td>
                                                            <td>
                                                                <label class="form-check-label" v-bind:for="'Radio-' + carga.id">
                                                                    {{ carga.curso.denominacion }}
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.docente">
                                                                    {{ carga.docente.paterno }} {{ carga.docente.materno }} {{ carga.docente.nombres }}
                                                                </template>
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
                                                                <template v-if="carga.docentes_id != null">
                                                                    <!-- <i-datos-docente :datosDocente="carga.docente" :usuario="carga.usuario"></i-datos-docente> -->
                                                                </template>
                                                                <!-- <button :id="carga.docente.id" class="btn btn-sm btn-primary">Datos</button> -->
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.link != null">
                                                                    <a style="font-size:12px" :href="carga.link" :title="carga.link">
                                                                        {{ carga.link.substr(0, 30) + "..." }}
                                                                    </a>
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
        </div>

        <!-- Modales -->
        <div
            class="modal fade"
            id="ModalAsistencia"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Validar Asistencia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="denominacion">Docente</label>
                                    <br />
                                    <span>{{ docente }}</span>
                                </div>
                                <div class="form-group col-6">
                                    <label for="denominacion">Teléfono</label>
                                    <br />
                                    <span>{{ telefono }}</span>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="modalidad">Modalidad</label>
                                    <select v-model="fields.modalidad" id="modalidad" class="form-control" @change="changeModalidad">
                                        <option value="1">Virtual</option>
                                        <option value="2">Presencial</option>
                                    </select>
                                    <div v-if="errors && errors.modalidad" class="text-danger">{{ errors.modalidad[0] }}</div>
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-md-4 form-group" v-if="!statusTema">
                                    <label for="fecha_tema">Fecha tema</label>
                                    <input type="date" v-model="fields.fecha_tema" id="fecha_tema" class="form-control" >
                                    <div v-if="errors && errors.fecha_tema" class="text-danger">{{ errors.fecha_tema[0] }}</div>
                                </div>
                                <div class="col-md-8 form-group" v-if="!statusTema">
                                    <label for="tema">Tema</label>
                                    <textarea v-model="fields.tema" id="tema" class="form-control"></textarea>
                                    <div v-if="errors && errors.tema" class="text-danger">{{ errors.tema[0] }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="denominacion">Link Meet</label>
                                    <br />
                                    <a :href="link" target="_black" class="btn btn-info btn-lg d-block text-white"><i class="fas fa-tv" style="margin: 0 10px;"> </i> Ver Clase</a>
                                </div>
                            </div>
                            <div class="row" v-if="statusTema">
                                <div class="form-group col-12">
                                    <label for="denominacion">Tema</label>
                                    <template v-if="sesion">
                                        <div class="alert alert-success" role="alert"><strong>Tema: </strong>{{ this.tema }}</div>
                                    </template>
                                    <template v-else>
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Tema no registrado</strong>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-12">Estado</label>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="fields.estado" :value="1" checked />
                                            Presente
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="fields.estado" :value="2" />
                                            Tarde
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="fields.estado" :value="3" />
                                            Falta
                                        </label>
                                    </div>
                                </div>
                                <div v-if="errors && errors.estado" class="text-danger">
                                    {{ errors.estado[0] }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                   
                                    <label for="horas_asistidas">Horas Asistidas</label>
                                    <div class="input-group">
                                        <input type="number" min="0" max="4" value="0" onkeydown="return false;" class="form-control" id="horas_asistidas" v-model="fields.horas_asistidas" />
                                        <div class="input-group-prepend">
                                            <button class="btn btn-danger" type="button" @click="decrementHoras">-</button>
                                        </div>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" @click="incrementHoras">+</button>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.horas_asistidas" class="text-danger">
                                        {{ errors.horas_asistidas[0] }}
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label for="cantidad_estudiantes">Cantidad de Estudiantes</label>
                                    <input type="number" class="form-control" id="cantidad_estudiantes" v-model="fields.cantidad_estudiantes" />
                                    <div v-if="errors && errors.cantidad_estudiantes" class="text-danger">
                                        {{ errors.cantidad_estudiantes[0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" accept="image/*" id="image" name="image" @change="GetImage" required class="custom-file-input" />
                                            <label class="custom-file-label" for="image">{{ placeHolderImage }}</label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.imagen" class="text-danger">
                                        {{ errors.imagen[0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="observacion">Observación</label>
                                    <textarea class="form-control" id="observacion" v-model="fields.observacion"></textarea>
                                    <!-- <div v-if="errors && errors.denominacion" class="text-danger">
                                        {{ errors.denominacion[0] }}
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" v-if="permissions.includes('validar asistencia docente')">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button v-if="fechaHoy==fields.fecha_tema && fecha==fields.fecha_tema" type="button" class="btn btn-success" @click="guardar">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="modal fade"
            id="ModalRegularizar"
            data-backdrop="static"
            data-keyboard="false"
            tabindex="-1"
            role="dialog"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="exampleModalLabel">Regularizar Asistecia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div v-if="errors && errors.cargaR" class="text-danger">
                                {{ errors.cargaR[0] }}
                            </div>
                            <table class="table table-bordered col table-sm">
                                <thead class="table-primary">
                                    <tr>
                                        <th scope="col" colspan="2">Cursos</th>
                                        <th scope="col">Docente</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="cargaR in cargasRegularizadas" :key="cargaR.id">
                                        <td v-bind:style="{ background: cargaR.curso.color }"></td>
                                        <td>
                                            <label class="form-check-label" v-bind:for="'Radio-' + cargaR.id">
                                                {{ cargaR.curso.denominacion }}
                                            </label>
                                        </td>
                                        <td>
                                            <template v-if="cargaR.docente"> {{ cargaR.docente.paterno }} {{ cargaR.docente.materno }} {{ cargaR.docente.nombres }} </template>
                                        </td>
                                        <td>
                                            <template v-if="cargaR.docente !== null">
                                                <template v-if="cargaR.tipo == '1'">
                                                    Principal
                                                </template>
                                                <template v-else-if="cargaR.tipo == '2'">
                                                    Suplente
                                                </template>
                                            </template>
                                        </td>
                                        <td>
                                            <template v-if="cargaR.docente">
                                                <input type="radio" v-model="cargaRegularizar" :value="cargaR.id" />
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="inicio">Hora Inicio</label>
                                    <select class="form-control" id="inicio" v-model="inicioRegularizar">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="ini in plantillaInicio" :key="ini.id" :value="ini.hora_inicio">{{ ini.hora_inicio }}</option>
                                    </select>
                                    <div v-if="errors && errors.inicioR" class="text-danger">
                                        {{ errors.inicioR[0] }}
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="fin">Hora Fin</label>
                                    <select class="form-control" id="fin" v-model="finRegularizar">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="fi in plantillaFin" :key="fi.id" :value="fi.hora_fin">{{ fi.hora_fin }}</option>
                                    </select>
                                    <div v-if="errors && errors.finR" class="text-danger">
                                        {{ errors.finR[0] }}
                                    </div>
                                </div>
                                <div class="form-group col-4">
                                    <label for="cantidad">Cantidad Horas</label>
                                    <input type="number" class="form-control" id="cantidad" v-model="cantidadRegularizar" />
                                    <div v-if="errors && errors.cantidadR" class="text-danger">
                                        {{ errors.cantidadR[0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-12">Estado</label>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="estadoRegularizar" :value="1" checked />
                                            Presente
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="estadoRegularizar" :value="2" />
                                            Tarde
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" v-model="estadoRegularizar" :value="3" />
                                            Falta
                                        </label>
                                    </div>
                                </div>
                                <div v-if="errors && errors.estadoR" class="text-danger">
                                    {{ errors.estadoR[0] }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="observacion">Observación</label>
                                    <textarea class="form-control" id="observacion" v-model="observacionRegularizar"></textarea>
                                    <div v-if="errors && errors.observacionR" class="text-danger">
                                        {{ errors.observacionR[0] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" @click="guardarRegularizar">Guardar</button>
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
    props: ["fechaHoy", "permissions"],
    data() {
        return {
            errors: {},
            fields: { estado: 1, sesion: "", observacion: "", cantidad_estudiantes: "", imagen: null,horas_asistidas:0,modalidad:"",fecha_tema:"",tema:"" },
            url: "",
            urlExport: "",
            urlParte: "",
            area: "",
            turno: "",
            grupo: "",
            sede: "",
            statusTema:false,
            virtual:false,
            fecha: this.fechaHoy,

            areas: [],
            turnos: [],
            grupos: [],
            sedes: [],

            cargas: [],
            horarios: [],

            color: "",
            turnoGrupo: "",
            dia: "",
            fechaSelect: "",

            link: "",
            sesion: null,
            tema: "",
            docente: "",
            telefono: "",

            plantillaInicio: [],
            plantillaFin: [],
            cargaRegularizar: "",
            inicioRegularizar: "",
            finRegularizar: "",
            cantidadRegularizar: "",
            estadoRegularizar: 1,
            observacionRegularizar: "",
            cargasRegularizadas: [],
            placeHolderImage: "Selecionar Imagen..."
            // check:null
        };
    },
    methods: {
        modalAsistencia: function(carga, docente, event) {
            // console.log(carga,docente)
            this.errors = {};
            this.placeHolderImage = "Selecionar Imagen...";
            this.fields.imagen = "";
            this.fields.fecha_tema = this.fecha;
            this.fields.tema = "";
            this.fields.observacion = "";
            this.fields.modalidad = "";
            // this.statusTema = false;
            this.virtual = false;
            (this.fields.estado = 1), (this.fields.sesion = "");
            (this.fields.observacion = ""),
                axios
                    .get("/intranet/get-sesiones", {
                        params: {
                            carga: carga,
                            docente: docente,
                            fecha: this.fecha
                        }
                    })
                    .then(response => {
                        this.docente = response.data.docente.paterno + " " + response.data.docente.materno + "" + response.data.docente.nombres;
                        this.telefono = response.data.docente.celular;
                        this.link = response.data.carga.link;
                        this.sesion = response.data.sesion;
                        if (this.sesion) {
                            this.statusTema = true;
                            this.tema = this.sesion.tema;
                            this.fields.sesion = this.sesion.id;
                        } else {
                            this.tema = "";
                            this.statusTema = false;
                            this.fields.sesion = "";
                        }
                        this.fields.docente = response.data.docente.id;
                        this.fields.carga = response.data.carga.id;
                        this.fields.fecha = this.fecha;
                        // this.grupo = response.data[0].id;
                        // this.grupos = response.data;
                    });
            $("#ModalAsistencia").modal("show");

            event.preventDefault();
        },
        guardar: function() {
            // console.log(this.fields);
            $(".loader").show();
            this.errors = {};
            if(!this.statusTema){
                this.fields.status_tema = 2;
            }else{
                this.fields.status_tema = 1;
            }
            // if (this.fields.imagen !== null) {
            let formData = new FormData();
            formData.append("estado", typeof this.fields.estado !== "undefined" ? this.fields.estado : "");
            formData.append("sesion", typeof this.fields.sesion !== "undefined" ? this.fields.sesion : null);
            formData.append("observacion", typeof this.fields.observacion !== "undefined" ? this.fields.observacion : null);
            formData.append("cantidad_estudiantes", typeof this.fields.cantidad_estudiantes !== "undefined" ? this.fields.cantidad_estudiantes : "");
            formData.append("imagen", typeof this.fields.imagen !== "undefined" ? this.fields.imagen : "");
            formData.append("docente", typeof this.fields.docente !== "undefined" ? this.fields.docente : "");
            formData.append("carga", typeof this.fields.carga !== "undefined" ? this.fields.carga : "");
            formData.append("fecha", typeof this.fields.fecha !== "undefined" ? this.fields.fecha : "");
            formData.append("horas_asistidas", typeof this.fields.horas_asistidas !== "undefined" ? this.fields.horas_asistidas : "");
            formData.append("fecha_tema", typeof this.fields.fecha_tema !== "undefined" ? this.fields.fecha_tema : "");
            formData.append("tema", typeof this.fields.tema !== "undefined" ? this.fields.tema : "");
            formData.append("status_tema", typeof this.fields.status_tema !== "undefined" ? this.fields.status_tema : "");
            axios
                .post("/intranet/auxiliar/asistencia/docente", formData)
                .then(response => {
                    $(".loader").hide();
                    // console.log(response.data.status);
                    if (response.data.status == true) {
                        this.getCargaAcademicaAsistencia();
                        toastr.success(response.data.message);
                        $("#ModalAsistencia").modal("hide");
                        // window.location.replace(response.data.url);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            // } else {
            //     this.errors.imagen = "La imagen es obligatoria";
            //     $(".loader").hide();
            // }
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
        getGrupoAulas: function() {
            axios
                .get("/intranet/get-grupo-aulas-auxiliar", {
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
        changeArea: function() {
            this.getGrupoAulas();
            this.getTurnos();
        },
        changeTurno: function() {
            this.getGrupoAulas();
            // this.changeDocente();
        },
        getCargaAcademicaAsistencia: function() {
            axios
                .get("/intranet/get-carga-academica-asistencia", {
                    params: {
                        grupo: this.grupo,
                        fecha: this.fecha
                    }
                })
                .then(response => {
                    this.cargas = response.data.cargaAcademica;
                    this.horarios = response.data.horario;
                    this.turnoGrupo = response.data.turno.denominacion;
                    this.dia = response.data.dia;
                    this.fechaSelect = response.data.fecha;
                });
            this.urlParte = "/intranet/reporte/docente-parte/pdf?grupo="+this.grupo+"&fecha="+this.fecha;
            this.urlExport = "/intranet/reporte/docente-asistencia/pdf/"+this.grupo+"/"+this.fecha;
        },
        getSedes:function(){
            axios.get("/intranet/get-sedes",{
                params: {
                    all: true
                },
            }).then(response => {
                this.sedes  = response.data;
            });
        },
        changeSede:function(){
            this.getGrupoAulas();
        },
        changeGrupo: function() {
            this.getCargaAcademicaAsistencia();
            this.curso = "";
            this.color = "";
            this.dia = [];
            $(".checksH").css("background", "none");
        },
        changeFecha: function() {
            this.getCargaAcademicaAsistencia();
            this.curso = "";
            this.color = "";
            this.dia = [];
            $(".checksH").css("background", "none");
        },
        pintarEstado: function(estado) {
            var color = ["light", "Pendiente"];
            switch (estado) {
                case "1":
                    color[0] = "success";
                    color[1] = "Presente";
                    break;
                case "2":
                    color[0] = "warning";
                    color[1] = "Tarde";
                    break;
                case "3":
                    color[0] = "danger";
                    color[1] = "Falta";
                    break;

                default:
                    color[0] = "light";
                    color[1] = "light";
                    break;
            }
            return color;
        },
        decrementHoras(){
            if(this.fields.horas_asistidas>0)
                this.fields.horas_asistidas--
        },
        incrementHoras(){
            if(this.fields.horas_asistidas<4)
                this.fields.horas_asistidas++
        },
        // regularizar: function() {
        //     this.cargaRegularizar = "";
        //     this.inicioRegularizar = "";
        //     this.finRegularizar = "";
        //     this.cantidadRegularizar = "";
        //     this.estadoRegularizar = 1;
        //     this.observacionRegularizar = "";
        //     if (this.grupo != "") {
        //         axios
        //             .get("/intranet/get-carga-academica-regularizar", {
        //                 params: {
        //                     grupo: this.grupo,
        //                     fecha: this.fecha
        //                 }
        //             })
        //             .then(response => {
        //                 // console.log(response.data.plantilla);
        //                 this.cargasRegularizadas = response.data.cargaAcademica;
        //                 this.plantillaInicio = response.data.plantilla;
        //                 this.plantillaFin = response.data.plantilla;
        //             });
        //         $("#ModalRegularizar").modal("show");
        //     } else {
        //         alert("Seleccionar un grupo");
        //     }
        // },
        guardarRegularizar: function() {
            // $(".loader").show();
            var datos = {
                cargaR: this.cargaRegularizar,
                inicioR: this.inicioRegularizar,
                finR: this.finRegularizar,
                cantidadR: this.cantidadRegularizar,
                estadoR: this.estadoRegularizar,
                observacionR: this.observacionRegularizar,
                fecha: this.fecha
            };
            axios
                .post("/intranet/auxiliar/asistencia/docente/regularizar", datos)
                .then(response => {
                    // $(".loader").hide();
                    if (response.data.status == true) {
                        this.getCargaAcademicaAsistencia();
                        toastr.success(response.data.message);
                        $("#ModalRegularizar").modal("hide");
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
        },
        GetImage: function(e) {
            let imagen = e.target.files[0];
            this.placeHolderImage = imagen.name;
            this.fields.imagen = imagen;
        },
        changeModalidad: function (params) {
            if(this.fields.modalidad==1){
                this.virtual = true;
                this.presencial = false;
            }
            if(this.fields.modalidad==2){
                this.virtual = false;
                this.presencial = true;
            }
        }
    },
    mounted: function() {
        this.getAreas();
        this.getTurnos();
        this.getGrupoAulas();
        this.getSedes();
        // this.getDocente();
    }
};
</script>
<style>
.disponibilidad th,
.disponibilidad td {
    padding: 0px;
}
.horario-auxiliar td {
    position: relative;
    /* color:white; */
}
.horario-auxiliar td a {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    height: 100%;
    font-weight: 500;
    cursor: pointer;
}
.horario-auxiliar td label {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    height: 100%;
    font-weight: 500;
    cursor: no-drop;
}
.horario-auxiliar td a:hover {
    color: white;
}
.disabled {
    pointer-events: none;
    cursor: default;
}
.custom-file-input:lang(en) ~ .custom-file-label::after {
    content: "Buscar";
}
</style>
