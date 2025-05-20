<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="form-group mb-0 col-2">
                                <label for="sede">Estado</label>
                                <select class="form-control" v-model="estado" @change="changeEstado">
                                    <option value="">--Todos--</option>
                                    <option value="0">PreInscrito</option>
                                    <option value="1">Inscrito</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-2">
                                <label for="sede">Cuota 1</label>
                                <select class="form-control" v-model="cuota1" @change="changeCuota1">
                                    <option value="">--Todos--</option>
                                    <option value="0">Completo</option>
                                    <option value="1">Faltante</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-2">
                                <label for="sede">Cuota 2</label>
                                <select class="form-control" v-model="cuota2" @change="changeCuota2">
                                    <option value="">--Todos--</option>
                                    <option value="0">Completo</option>
                                    <option value="1">Faltante</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-2">
                                <label for="sede">Cuota 3</label>
                                <select class="form-control" v-model="cuota3" @change="changeCuota3">
                                    <option value="">--Todos--</option>
                                    <option value="0">Completo</option>
                                    <option value="1">Faltante</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-2">
                                <label for="sede">Cuota 4</label>
                                <select class="form-control" v-model="cuota4" @change="changeCuota4">
                                    <option value="">--Todos--</option>
                                    <option value="0">Completo</option>
                                    <option value="1">Faltante</option>
                                </select>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <excel-export
                            v-if="permissions.includes('descargar reporte pagos')"
                            class="btn btn-success"
                            :data="json_data"
                            :columns="json_fields"
                            :filename="'ReportePagos'"
                            :sheetname="'Reporte'"
                        >
                            Descargar excel
                        </excel-export>
                        <button v-if="permissions.includes('descargar reporte pagos')" class="btn btn-danger" @click="exportarPDF">Exportar PDF</button>
                        <a :href="ruta_rpt_pagos" class="btn btn-primary">Descargar pagos <span class="badge badge-dark">new</span></a>

                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/reporte/pagos/lista">
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Inscrito" : "Pre Inscrito" }}
                            </div>
                            <div slot="pago_total" slot-scope="props">
                                <span v-if="!props.row.pago_total">0.00</span>
                                <span v-else>{{ props.row.pago_total }}</span>
                            </div>
                            <div slot="pago_matricula" slot-scope="props">
                                <span v-if="!props.row.pago_matricula">0.00</span>
                                <span v-else :class="props.row.pago_matricula.split('|')[0] - props.row.pago_matricula.split('|')[1] == 0 ? 'text-success' : 'text-danger'">{{
                                    props.row.pago_matricula
                                }}</span>
                            </div>
                            <div slot="primera_mensualidad" slot-scope="props">
                                <span v-if="!props.row.primera_mensualidad">0.00</span>
                                <span v-else :class="props.row.primera_mensualidad.split('|')[0] - props.row.primera_mensualidad.split('|')[1] == 0 ? 'text-success' : 'text-danger'">{{
                                    props.row.primera_mensualidad
                                }}</span>
                            </div>
                            <div slot="segunda_mensualidad" slot-scope="props">
                                <span v-if="!props.row.segunda_mensualidad">0.00</span>
                                <span v-else :class="props.row.segunda_mensualidad.split('|')[0] - props.row.segunda_mensualidad.split('|')[1] == 0 ? 'text-success' : 'text-danger'">{{
                                    props.row.segunda_mensualidad
                                }}</span>
                            </div>
                            <div slot="tercera_mensualidad" slot-scope="props">
                                <span v-if="!props.row.tercera_mensualidad">0.00</span>
                                <span v-else :class="props.row.tercera_mensualidad.split('|')[0] - props.row.tercera_mensualidad.split('|')[1] == 0 ? 'text-success' : 'text-danger'">{{
                                    props.row.tercera_mensualidad
                                }}</span>
                            </div>
                            <div slot="cuarta_mensualidad" slot-scope="props">
                                <span v-if="!props.row.cuarta_mensualidad">0.00</span>
                                <span v-else :class="props.row.cuarta_mensualidad.split('|')[0] - props.row.cuarta_mensualidad.split('|')[1] == 0 ? 'text-success' : 'text-danger'">{{
                                    props.row.cuarta_mensualidad
                                }}</span>
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
                                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh;"></object>
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
                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh;"></object>
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
    props: ["permissions", "ruta_rpt_pagos"],
    data() {
        return {
            url: "",
            fecha_ini: "",
            fecha_fin: "",
            columns: [
                "nro_documento",
                "paterno",
                "materno",
                "nombres",
                "area",
                "turno",
                "grupo",
                "descuento",
                "tipo_colegio",
                "estado",
                "pago_total",
                "pago_matricula",
                "primera_mensualidad",
                "segunda_mensualidad",
                "tercera_mensualidad",
                "cuarta_mensualidad"
            ],
            options: {
                headings: {
                    nro_documento: "Documento",
                    paterno: "Apellido Paterno",
                    materno: "Apellido Materno",
                    nombres: "Nombres",
                    area: "Area",
                    turno: "Turno",
                    grupo: "Grupo",
                    descuento: "Descuento",
                    tipo_colegio: "Tipo Colegio",
                    estado: "Estado",
                    pago_total: "Pago Total",
                    pago_matricula: "Pago Matricula",
                    primera_mensualidad: "Primera Mensualidad",
                    segunda_mensualidad: "Segunda Mensualidad",
                    tercera_mensualidad: "Tercera Mensualidad",
                    cuarta_mensualidad: "Cuarta Mensualidad",
                    asistencia: "Asistencias"
                },
                sortable: [],
                filterable: ["nro_documento", "paterno", "materno", "nombres"],
                filterByColumn: true
            },
            json_fields: [
                {
                    label: "Documento",
                    field: "nro_documento"
                },
                {
                    label: "Apellido Paterno",
                    field: "paterno"
                },
                {
                    label: "Apellido Materno",
                    field: "materno"
                },
                {
                    label: "Nombres",
                    field: "nombres"
                },
                {
                    label: "Celular",
                    field: "celular"
                },
                {
                    label: "Sede",
                    field: "sede"
                },
                {
                    label: "Area",
                    field: "area"
                },
                {
                    label: "Turno",
                    field: "turno"
                },
                {
                    label: "Grupo",
                    field: "grupo"
                },

                {
                    label: "Descuento",
                    field: "descuento"
                },
                {
                    label: "Tipo Colegio",
                    field: "tipo_colegio"
                },
                {
                    label: "Estado",
                    field: "estado"
                },
                {
                    label: "Pago Total",
                    field: "pago_total"
                },
                {
                    label: "Pago Matrícula",
                    field: "pago_matricula"
                },
                {
                    label: "Primera Mensualidad",
                    field: "primera_mensualidad"
                },
                {
                    label: "Segunda Mensualidad",
                    field: "segunda_mensualidad"
                },
                {
                    label: "Tercera Mensualidad",
                    field: "tercera_mensualidad"
                },
                {
                    label: "Cuarta Mensualidad",
                    field: "cuarta_mensualidad"
                },
                {
                    label: "Asistencias",
                    field: "asistencia"
                    // dataFormat: this.formatApto
                }
            ],
            json_data: [],
            estado: "",
            cuota1: "",
            cuota2: "",
            cuota3: "",
            cuota4: ""
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
        // getDataExcel: function() {
        //     axios
        //         .get("/intranet/reporte/pagos/lista-excel", {
        //             params: {
        //                 all: true,
        //                 estado: this.estado
        //             }
        //         })
        //         .then(response => {
        //             // this.url = "docentes/pdf/"+response.data;
        //             this.json_data = response.data;
        //             // console.log(response.data);
        //         });
        // },
        getDataExcel: function() {
            console.log("Solicitando datos..."); // Debug
            axios
                .get("/intranet/reporte/pagos/lista-excel", {
                    params: {
                        all: true,
                        estado: this.estado
                    }
                })
                .then(response => {
                    // console.log("Datos recibidos:", response.data); // Debug
                    this.json_data = response.data;
                })
                .catch(error => {
                    console.error("Error al obtener datos:", error); // Debug
                });
        },
        exportarPDF: function() {
            this.url = "/intranet/reporte/pagos/pdf";
            $("#ModalReporte").modal("show");
        },
        changeFecha: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ fecha_ini: this.fecha_ini, fecha_fin: this.fecha_fin });
        },
        changeEstado: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ estado: this.estado });
            console.log("data");
        },
        changeCuota1: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ cuota1: this.cuota1 });
        },
        changeCuota2: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ cuota2: this.cuota2 });
        },
        changeCuota3: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ cuota3: this.cuota3 });
        },
        changeCuota4: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({ cuota4: this.cuota4 });
        }
    },
    mounted() {
        console.log("Componente montado - llamando a getDataExcel");
        this.getDataExcel();
    }
};
</script>

<style></style>
