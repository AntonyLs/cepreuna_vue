<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Lista General de Cursos</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <table class="table table-bordered col table-striped">
                                <thead class="table-primary">
                                    <tr style="background-color: #0D4172 !important;">
                                        <th scope="col" colspan="2">Cursos</th>
                                        <th scope="col">Docente</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Link Meet</th>
                                        <th scope="col">Encuesta</th>
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
                                            <template v-if="carga.docente"> {{ carga.docente.paterno }} {{ carga.docente.materno }} {{ carga.docente.nombres }} </template>
                                        </td>
                                        <td>
                                            <h6 v-if="carga.tipo == '2'"><span class="badge badge-secondary">Remplazo</span></h6>
                                        </td>
                                        <td>
                                            <label class="form-check-label" v-bind:for="'Radio-' + carga.id">
                                                <a class="btn btn-link text-success" :href="carga.link" target="_blank">
                                                    {{ carga.link }}
                                                </a>
                                            </label>
                                        </td>
                                        <td>
                                            <!-- Button trigger modal -->
                                            <button
                                                v-if="data.map(d => d.carga_academicas_id == carga.id)[0]"
                                                type="button"
                                                class="btn btn-dark btn-sm"
                                                data-toggle="modal"
                                                data-target="#ModalFormulario"
                                                @click="calificar(carga)"
                                            >
                                                <i class="fas fa-check"></i>
                                                Calificar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="ModalFormulario" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h5 class="modal-title text-white">Formulario de Calificación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <th>Docente</th>
                                            <td>: {{ docente.paterno }} {{ docente.materno }} {{ docente.nombres }}</td>
                                            <th>Curso</th>
                                            <td>: {{ curso.denominacion }}</td>
                                            <th>Fecha</th>
                                            <td>: <fecha-format :fecha="asistencia.fecha" :format="'DD/MM/YYYY'"></fecha-format></td>
                                        </tr>
                                        <tr>
                                            <th>Hora Inicio</th>
                                            <td>: <hora-format :hora="asistencia.hora_inicio" :format="'hh:mm A'"></hora-format></td>
                                            <th>Hora Fin</th>
                                            <td>: <hora-format :hora="asistencia.hora_fin" :format="'hh:mm A'"></hora-format></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="alert alert-info" role="alert">
                                Califique del 1 al 5 al docente asignado al curso de acuerdo a los siguientes criterios de evaluación.
                            </div>
                            <div class="row">
                                <ol v-if="criterios.length != 0">
                                    <li v-for="criterio in criterios" class="font-weight-bold">
                                        {{ criterio.denominacion }}
                                        <div class="form-group mt-3">
                                            <!-- <input class="form-control" style="width:80px" type="number" v-model="preguntas[criterio.id]" max="10" min="1" /> -->
                                            <RadioImage :link="criterio.id + '1'" :src="'/images/reacciones/1f621.svg'" v-model="preguntas[criterio.id]" :value="'1'" />
                                            <RadioImage :link="criterio.id + '2'" :src="'/images/reacciones/2639.svg'" v-model="preguntas[criterio.id]" :value="'2'" />
                                            <RadioImage :link="criterio.id + '3'" :src="'/images/reacciones/1f610.svg'" v-model="preguntas[criterio.id]" :value="'3'" />
                                            <RadioImage :link="criterio.id + '4'" :src="'/images/reacciones/1f642.svg'" v-model="preguntas[criterio.id]" :value="'4'" />
                                            <RadioImage :link="criterio.id + '5'" :src="'/images/reacciones/1f600.svg'" v-model="preguntas[criterio.id]" :value="'5'" />
                                        </div>
                                    </li>
                                </ol>
                                <div v-else class="col-md-12 text-center"><em> Actualmente no existen criterios de evaluación</em></div>
                            </div>

                            <div v-if="errorForm" class="alert alert-danger" role="alert">
                                <strong>Verifique que cada respuesta sea valida</strong>
                                <ul>
                                    <li>Debe de responder todas las preguntas</li>
                                    <li>El puntaje valido es de 1 a 5</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button v-if="criterios.length != 0" type="button" class="btn btn-primary" @click="GuardarCalificacion">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import toastr from "toastr";
import $ from "jquery";

import RadioImage from "./RadioImage.vue";

export default {
    data: () => ({
        cargas: [],
        fields: {
            id: ""
        },
        errors: {},
        docente: {
            paterno: "",
            materno: "",
            nombres: ""
        },
        curso: {
            denominacion: ""
        },
        preguntas: [],
        criterios: {},
        asistencia: {},
        calificacionId: "",
        errorForm: false,
        cargaId: ""
    }),
    props: ["carga", "data"],
    methods: {
        getCarga: function() {
            axios.get("cursos/get-carga").then(
                function(response) {
                    // console.log(response.data);
                    this.cargas = response.data.carga;
                }.bind(this)
            );
        },
        getCriterios: function() {
            axios.get("cursos/get-criterios-docente").then(
                function(response) {
                    // this.criterios = response.data.filter(d => d.tipo == "1");
                    this.criterios = response.data;
                }.bind(this)
            );
        },
        idCarga: function(id) {
            this.fields.id = id;
        },
        calificar: function(data) {
            // console.log(data);
            this.cargaId = data.id;
            let datosCalificacion = this.data.reduce(d => d.carga_academicas_id == data.id);
            this.calificacionId = datosCalificacion.id;
            this.docente = datosCalificacion.docente;
            this.curso = data.curso;
            this.asistencia = datosCalificacion.asistencia_docente;
            console.log(datosCalificacion);
        },
        GuardarCalificacion: function() {
            $(".loader").show();
            let preguntasValidar = [];
            let cont = 0;
            this.preguntas.filter((p, i) => {
                if (p != null) {
                    preguntasValidar[cont] = parseInt(p);
                    cont++;
                }
            });
            this.errorForm = false;
            // console.log(preguntasValidar);
            let cantidadCriterios = this.criterios.length;
            let cantidadRespuestas = preguntasValidar.length;

            if (cantidadCriterios == cantidadRespuestas) {
                axios
                    .post("cursos/calificar-docente/" + this.calificacionId, { preguntasValidar: preguntasValidar, preguntas: this.preguntas })
                    .then(response => {
                        console.log(response.data);
                        if (response.data.status) {
                            toastr.success(response.data.message);
                            // let index = this.data.findIndex(d => d.carga_academicas_id == this.cargaId);
                            // this.data.splice(index, 1);
                            setTimeout(function() {
                                location.reload();
                                $(".loader").hide();
                            }, 1000);
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        $(".loader").hide();
                        console.log(error.response.data);
                        if (error.response.status === 422) {
                            // this.errors = error.response.data.errors || {};
                            this.errorForm = true;
                        }
                    });
            } else {
                $(".loader").hide();
                this.errorForm = true;
            }
        }
    },
    mounted() {
        this.getCarga();
        this.getCriterios();
        // const tiempoTranscurrido = Date.now();
        // const hoy = new Date(tiempoTranscurrido);
        // this.fecha = hoy.toLocaleDateString();
    },
    components: {
        RadioImage
    }
};
</script>

<style></style>
