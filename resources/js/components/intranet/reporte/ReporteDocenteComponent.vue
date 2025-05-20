<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <label for="sede">Condicion</label>
                                <select class="form-control" v-model="condicion" @change="changeCondicion">
                                    <option value="">--Seleccionar--</option>
                                    <option value="1">Particular</option>
                                    <option value="2">Unap</option>
                                </select>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <excel-export
                            v-if="permissions.includes('descargar reporte docentes')"
                            class="btn btn-success"
                            :data="json_data"
                            :columns="json_fields"
                            :filename="'ReporteDocente'"
                            :sheetname="'Reporte'"
                        >
                            Descargar excel
                        </excel-export>
                        <a :href="ruta_rpt_docente_horas" class="btn btn-primary">Descargar docentes horas <span class="badge badge-light">new</span></a>
                        

                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/reporte/docente/lista" class="mt-3">
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
                                <template v-if="props.row.docente.condicion == '1'">
                                    Particular
                                </template>
                                <template v-else>
                                    Unap
                                </template>
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
                                            <object
                                                class="embed-responsive-item"
                                                type="application/pdf"
                                                :data="url"
                                                allowfullscreen
                                                width="100%"
                                                height="500"
                                                style="height: 85vh;"
                                            ></object>
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
import $ from "jquery";
// import toastr from "toastr";

export default {
    props: ["permissions","ruta_rpt_docente_horas","ruta_rpt_docente_sedes"],
    data() {
        return {
            url: "",
            condicion: "",
            columns: [
                "id",
                "apto",
                "docente.condicion",
                "docente.nro_documento",
                "docente.paterno",
                "docente.materno",
                "docente.nombres",
                "grado.denominacion",
                "programa.denominacion",
                "horas",
                "actions"
            ],
            options: {
                headings: {
                    id: "id",
                    apto: "apto",
                    "docente.condicion": "Condicion",
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    "grado.denominacion": "Grado Academico",
                    "programa.denominacion": "Programa",
                    horas: "Horas Asistidas",
                    actions: "Acciones"
                },
                sortable: ["id", "apto"],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true
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
                    label: "Condicion",
                    field: "docente.condicion",
                    dataFormat: this.formatCondicion
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
                },
                {
                    label: "Grado Academico",
                    field: "grado.denominacion"
                },
                {
                    label: "Programa",
                    field: "programa.denominacion"
                },
                {
                    label: "Horas Asistidas",
                    field: "horas"
                }
            ],
            json_data: []
        };
    },

    methods: {
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "/intranet/inscripcion/docentes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        formatApto: function(value) {
            if (value == "1") {
                return "Si";
            }
            return "No";
        },
        formatCondicion: function(value) {
            if (value == "2") {
                return "Unap";
            }
            return "Particular";
        },
        getDataExcel: function() {
            axios
                .get("/intranet/reporte/docente/lista", {
                    params: {
                        all: true,
                        condicion: this.condicion
                    }
                })
                .then(response => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.json_data = response.data;
                    // console.log(response.data);
                });
        },
        changeCondicion: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ condicion: this.condicion });
        }
    },
    mounted() {
        this.getDataExcel();
    }
};
</script>

<style></style>
