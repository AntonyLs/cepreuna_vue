<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <!-- <label for="sede">Condicion</label>
                                <select class="form-control" v-model="condicion" @change="changeCondicion">
                                    <option value="">--Seleccionar--</option>
                                    <option value="1">Particular</option>
                                    <option value="2">Unap</option>
                                </select> -->
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/reporte/docente-calificacion/lista">
                            <div slot="actions" slot-scope="props">
                                <button class="p-0 m-0 h5 btn btn-link text-success" title="detalles" @click="detalles(props.row.id)">
                                    <i class="fas fa-list"></i>
                                </button>
                                <!-- <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarInscripcion(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                                    <i class="fas fa-file-signature"></i>
                                </button> -->
                            </div>
                            <div slot="asistencia_docente.fecha" slot-scope="props">
                                <fecha-format :fecha="props.row.asistencia_docente.fecha" :format="'DD/MM/YYYY'"></fecha-format>
                            </div>
                            <div slot="asistencia_docente.hora_inicio" slot-scope="props">
                                <hora-format :hora="props.row.asistencia_docente.hora_inicio" :format="'hh:mm A'"></hora-format>
                            </div>
                            <div slot="asistencia_docente.hora_fin" slot-scope="props">
                                <hora-format :hora="props.row.asistencia_docente.hora_fin" :format="'hh:mm A'"></hora-format>
                            </div>
                        </v-server-table>

                        <div class="modal fade" id="ModalFicha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Detalles de calificación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="mb-0">Docente</label>
                                                    <p class="mb-0">
                                                        {{ calificacion.docente.nro_documento }} | {{ calificacion.docente.paterno }} {{ calificacion.docente.materno }}
                                                        {{ calificacion.docente.nombres }}
                                                    </p>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="mb-0">Curso</label>
                                                    <p class="mb-0">{{ calificacion.curso.denominacion }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="mb-0">Fecha</label>
                                                    <p class="mb-0">
                                                        <fecha-format :fecha="calificacion.asistencia_docente.fecha" :format="'DD/MM/YYYY'"></fecha-format>
                                                    </p>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="mb-0">Hora Inicio</label>
                                                    <p class="mb-0">
                                                        <hora-format :hora="calificacion.asistencia_docente.hora_inicio" :format="'hh:mm A'"></hora-format>
                                                    </p>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="mb-0">Hora Fin</label>
                                                    <p class="mb-0">
                                                        <hora-format :hora="calificacion.asistencia_docente.hora_fin" :format="'hh:mm A'"></hora-format>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4">
                                                    <label class="mb-0">Participantes</label>
                                                    <p class="mb-0">{{ calificacion.participantes }}</p>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="mb-0">Promedio Total</label>
                                                    <p class="mb-0">{{ calificacion.promedio }}</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr class="bg-info">
                                                            <th scope="col">#</th>
                                                            <th scope="col">Criterio</th>
                                                            <th scope="col" style="width:200px" class="text-right">Promedio</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(criterio, i) in criterios" :key="criterio.id">
                                                            <th scope="row">{{ i + 1 }}</th>
                                                            <td>{{ criterio.nombre }}</td>
                                                            <td class="text-right">{{ criterio.promedio }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal  Editar de Inscripcion-->
    </div>
</template>

<script>
import axios from "axios";
// import $ from "jquery";
// import toastr from "toastr";

export default {
    props: ["permissions"],
    data() {
        return {
            url: "",
            condicion: "",
            columns: [
                "id",
                "docente.nro_documento",
                "docente.paterno",
                "docente.materno",
                "docente.nombres",
                "curso.denominacion",
                "asistencia_docente.fecha",
                "asistencia_docente.hora_inicio",
                "asistencia_docente.hora_fin",
                "participantes",
                "promedio",
                "actions"
            ],
            options: {
                headings: {
                    id: "id",
                    apto: "apto",
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    "curso.denominacion": "Curso",
                    "asistencia_docente.fecha": "Fecha",
                    "asistencia_docente.hora_inicio": "Inicio",
                    "asistencia_docente.hora_fin": "Fin",
                    participantes: "Participantes",
                    promedio: "Promedio",
                    actions: "Acciones"
                },
                sortable: ["id", "apto"],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true
            },
            criterios: [],
            calificacion: {
                docente: {},
                curso: {},
                asistencia_docente: {}
            }
        };
    },

    methods: {
        detalles: function(id) {
            axios.get("/intranet/reporte/docente-calificacion/detalle/" + id).then(response => {
                // this.url = "/intranet/inscripcion/docentes/pdf/"+response.data;
                this.criterios = response.data.criterios;
                this.calificacion = response.data.calificacion;
                console.log(response.data);
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        }
    },
    mounted() {}
};
</script>

<style></style>
