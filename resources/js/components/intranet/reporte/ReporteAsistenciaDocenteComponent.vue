<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Inicio</label>
                                <input type="date" class="form-control" v-model="fecha_ini" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Fin</label>
                                <input type="date" class="form-control" v-model="fecha_fin" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <excel-export
                            v-if="permissions.includes('descargar reporte asistencia docentes')"
                            class="btn btn-success"
                            :data="json_data"
                            :columns="json_fields"
                            :filename="'ReporteDocente'"
                            :sheetname="'Reporte'"
                        >
                            Descargar excel
                        </excel-export>

                        <a @click="getDataHoraSede()" class="btn btn-info">Descargar horas por sede <span class="badge badge-dark">new</span></a>
                        <a @click="getDataHorasVirtual()" class="btn btn-outline-primary">Descargar horas presencial | virtual <span class="badge badge-dark">new</span></a>
                        <button v-if="permissions.includes('descargar reporte asistencia docentes')" class="btn btn-danger" @click="exportarPDF">Exportar PDF</button>

                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/reporte/asistencia-docente/lista" class="mt-3">
                            <div slot="actions" slot-scope="props">
                                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <!-- <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarInscripcion(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                                    <i class="fas fa-file-signature"></i>
                                </button> -->
                            </div>
                            <div slot="apto" slot-scope="props">
                                <template v-if="props.row.apto == '1'">
                                    <span class="badge badge-success">SI</span>
                                </template>
                                <template v-else>
                                    <span class="badge badge-danger">NO</span>
                                </template>
                            </div>
                            <div slot="docente.condicion" slot-scope="props">
                                <template v-if="props.row.docente.condicion == '1'"> Particular </template>
                                <template v-else> Unap </template>
                            </div>
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
                                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh"></object>
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
        <div class="modal fade" id="ModalReporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reporte</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh"></object>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
// import toastr from "toastr";

export default {
    props: ["permissions", "ruta_rpt_docente_sedes"],
    data() {
        return {
            url: "",
            fecha_ini: "",
            fecha_fin: "",
            columns: [
                "docente.nro_documento",
                "docente.paterno",
                "docente.materno",
                "docente.nombres",
                "docente.celular",
                "docente_apto.usuario",
                "carga.area.denominacion",
                "carga.grupo.denominacion",
                "carga.curso.denominacion",
                "horas_presente",
                "horas_tarde",
                "horas_falta",
                "observacion",
            ],
            options: {
                headings: {
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    "docente.celular": "Celular",
                    "docente_apto.usuario": "Email",
                    "carga.area.denominacion": "Area",
                    "carga.grupo.denominacion": "Grupo",
                    "carga.curso.denominacion": "Curso",
                    horas_presente: "Horas Presente",
                    horas_tarde: "Horas Tarde",
                    horas_falta: "Horas Falta",
                    observacion: "Observación",
                },
                sortable: [],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true,
            },
            json_fields: [
                {
                    label: "Documento",
                    field: "docente.nro_documento",
                },
                {
                    label: "Apellido Paterno",
                    field: "docente.paterno",
                },
                {
                    label: "Apellido Materno",
                    field: "docente.materno",
                },
                {
                    label: "Nombres",
                    field: "docente.nombres",
                },
                {
                    label: "Celular",
                    field: "docente.celular",
                },
                {
                    label: "Email",
                    field: "docente_apto.usuario",
                },
                {
                    label: "Sede",
                    field: "carga.grupo_aula.aula.local.sede.denominacion",
                },
                {
                    label: "Area",
                    field: "carga.area.denominacion",
                },
                {
                    label: "Codicion",
                    field: "carga.tipo",
                    dataFormat: this.formatTipo,
                },
                {
                    label: "Grupo",
                    field: "carga.grupo.denominacion",
                },
                {
                    label: "Curso",
                    field: "carga.curso.denominacion",
                },
                {
                    label: "Horas Presente",
                    field: "horas_presente",
                },
                {
                    label: "Horas Tarde",
                    field: "horas_tarde",
                },
                {
                    label: "Horas Falta",
                    field: "horas_falta",
                },
                {
                    label: "Observación",
                    field: "observacion",
                },
            ],
            json_data: [],
        };
    },

    methods: {
        ficha: function (id) {
            axios.get("/intranet/encrypt/" + id).then((response) => {
                this.url = "/intranet/inscripcion/docentes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        formatApto: function (value) {
            if (value == "1") {
                return "Si";
            }
            return "No";
        },
        formatCondicion: function (value) {
            if (value == "2") {
                return "Unap";
            }
            return "Particular";
        },
        getDataExcel: function () {
            axios
                .get("/intranet/reporte/asistencia-docente/lista", {
                    params: {
                        all: true,
                        fecha_ini: this.fecha_ini,
                        fecha_fin: this.fecha_fin,
                    },
                })
                .then((response) => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.json_data = response.data;
                    // console.log(response.data);
                });
        },

        // ---------------------------------
        getDataHoraSede() {
            axios
                .post(
                    "/intranet/reporte/docente-horas-sede",
                    {
                        params: {
                            all: true,
                            fecha_ini: this.fecha_ini,
                            fecha_fin: this.fecha_fin,
                        },
                    },
                    { responseType: "blob" }
                ) // Indicar responseType como 'blob' para recibir una respuesta binaria
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "nombre_del_archivo.xlsx"); // Establecer el nombre del archivo
                    document.body.appendChild(link);
                    link.click();
                });
        },

        getDataHorasVirtual() {
            axios
                .post(
                    "/intranet/reporte/docente-horas-virtual",
                    {
                        params: {
                            all: true,
                            fecha_ini: this.fecha_ini,
                            fecha_fin: this.fecha_fin,
                        },
                    },
                    { responseType: "blob" }
                ) // Indicar responseType como 'blob' para recibir una respuesta binaria
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", "nombre_del_archivo.xlsx"); // Establecer el nombre del archivo
                    document.body.appendChild(link);
                    link.click();
                });
        },
        // ----------------------------------

        exportarPDF: function () {
            this.url = "/intranet/reporte/asistencia-docente/pdf?fecha_ini=" + this.fecha_ini + "&fecha_fin=" + this.fecha_fin;
            $("#ModalReporte").modal("show");
        },
        changeFecha: function () {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ fecha_ini: this.fecha_ini, fecha_fin: this.fecha_fin });
        },
        formatTipo: function (value) {
            if (value == "1") {
                return "Principal";
            }
            return "Suplente";
        },
    },
    mounted() {
        this.getDataExcel();
    },
};
</script>

<style></style>
