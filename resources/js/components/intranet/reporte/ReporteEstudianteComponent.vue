<style>
.table thead tr:nth-child(1) {
    background-color: #303c54;
    color: white;
}
</style>
<template>
    <div>
        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
      <i class="fa fa-plus"></i> Agregar
    </button>  -->
        <header class="card-header">
            <div class="row">
                <div class="col-10">
                    <div class="row">
                        <div class="form-group mb-0 col-2">
                            <label for="sede">Sede</label>
                            <select class="form-control" v-model="sede" @change="changeSede">
                                <option value="">--Seleccionar--</option>
                                <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{ sede.denominacion }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0 col-2">
                            <label for="area">Area</label>
                            <select class="form-control" v-model="area" @change="changeArea">
                                <option value="">--Seleccionar--</option>
                                <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0 col-2">
                            <label for="turno">Turno</label>
                            <select class="form-control" v-model="turno" @change="changeTurno">
                                <option value="">--Seleccionar--</option>
                                <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0 col-3">
                            <label for="turno">Tipo Descuento</label>
                            <select class="form-control" v-model="tipo_descuento" @change="changeDescuento">
                                <option value="">--Seleccionar--</option>
                                <option v-for="tipo_descuento in tipo_descuentos" :value="tipo_descuento.id" :key="tipo_descuento.id">{{ tipo_descuento.denominacion }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0 col-3">
                            <label for="turno">Estado</label>
                            <select class="form-control" v-model="estado" @change="changeEstado">
                                <option value="">--Seleccionar--</option>
                                <option v-for="estado in estados" :value="estado.id" :key="estado.id">{{ estado.denominacion }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <excel-export
            v-if="permissions.includes('descargar reporte estudiantes')"
            class="btn btn-success mt-2"
            :data="json_data"
            :columns="json_fields"
            :filename="'ReporteEstudiante'"
            :sheetname="'Reporte'"
        >
            Descargar excel
        </excel-export>
        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/reporte/estudiante/lista">
            <div slot="actions" slot-scope="props">
                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarInscripcion(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                    <i class="fas fa-file-signature"></i>
                </button>
            </div>
            { denominacion:"Normal (sin descuento)", id:1 }, { denominacion:"Descuento por Hijo de trabajador", id:2 }, { denominacion:"Descuento por planilla", id:3 }, {
            denominacion:"Descuento por hermanos", id:4 }, { denominacion:"Descuento por Resolucion Rectoral", id:5 }
            <div slot="tipo_estudiante" slot-scope="props">
                <div v-if="props.row.tipo_estudiante == 1">
                    Normal (sin descuento)
                </div>
                <div v-else-if="props.row.tipo_estudiante == 2">
                    Hijo de trabajador
                </div>
                <div v-else-if="props.row.tipo_estudiante == 3">
                    Descuento por planilla
                </div>
                <div v-else-if="props.row.tipo_estudiante == 4">
                    Descuento por hermanos
                </div>
                <div v-else-if="props.row.tipo_estudiante == 5">
                    Resolucion Rectoral
                </div>
            </div>
            <div slot="estado" slot-scope="props">
                {{ props.row.estado == "1" ? "Inscrito" : "Pre Inscrito" }}
            </div>
        </v-server-table>
        <!-- Modal Ficha de inscripción -->
        <div class="modal fade" id="ModalFicha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Ficha
                        </h5>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal  Editar de Inscripcion-->
        <div class="modal fade" id="modalEditarInscripcion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Editar Inscripción de un Estudiante
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-gray-100">
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-md-7 col-xs-12">
                                    <div class="card mb-2 shadow-sm rounded">
                                        <div class="row no-gutters">
                                            <div class="col-md-4 col-xs-12">
                                                <img :src="'../../storage/fotos/' + infoEstudiante.foto" class="card-img border-right" alt="" />
                                            </div>
                                            <div class="col-md-8 col-xs-12">
                                                <div class="card-header text-white text-center bg-info py-1">
                                                    <b class="card-title mb-0">Datos Personales del Estudiante</b>
                                                </div>
                                                <div class="card-body py-2">
                                                    <b>Nombres y Apellidos:</b>
                                                    <p class="m-0">
                                                        {{ infoEstudiante.nombres + " " + infoEstudiante.paterno + " " + infoEstudiante.materno }}
                                                    </p>
                                                    <b>Número de Documento:</b>
                                                    <p class="m-0">
                                                        {{ infoEstudiante.nro_documento }}
                                                    </p>
                                                    <b>Celular:</b>
                                                    <p class="m-0">
                                                        {{ infoEstudiante.celular }}
                                                    </p>
                                                    <b>Correo Electrónico</b>
                                                    <p class="m-0">
                                                        {{ infoEstudiante.email }}
                                                    </p>
                                                    <b>Cuenta Institucional</b>
                                                    <p class="m-0">
                                                        {{ infoEstudiante.usuario }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-2 shadow-sm rounded">
                                        <div class="card-header text-white text-center bg-info py-1">
                                            <b class="card-title mb-0">Datos de Inscripción</b>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-6">
                                                    <b>Correlativo</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.correlativo }}
                                                    </p>
                                                    <b>Tipo Descuento por Estudiante</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.tipo_estudiante }}
                                                    </p>
                                                    <b>Cantidad de Veces Inscrito</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.cantidad_inscrito }}
                                                    </p>
                                                    <b>Estado</b>
                                                    <h6>
                                                        <span class="badge" :class="[infoInscripcion.estado == 1 ? 'badge-success' : 'badge-warning']">{{
                                                            infoInscripcion.estado == 1 ? "INSCRITO" : "PRE-INSCRITO"
                                                        }}</span>
                                                    </h6>
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <b>Sede</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.sede }}
                                                    </p>
                                                    <b>Área</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.area }}
                                                    </p>
                                                    <b>Turno</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.turno }}
                                                    </p>
                                                    <b>Periodo</b>
                                                    <p class="m-0">
                                                        {{ infoInscripcion.periodo }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5  col-xs-12">
                                    <div v-if="statusPago">
                                        <div class="card mb-2 shadow-sm rounded">
                                            <div class="card-header text-white text-center bg-info py-1">
                                                <b class="card-title mb-0">Detalle de Vouchers</b>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    <div class="col-xs-12 col-md-12">
                                                        <div v-if="infoInscripcion.pagos.length != 0">
                                                            <nav>
                                                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                                                    <a
                                                                        v-for="(pago, i) in infoInscripcion.pagos"
                                                                        :key="pago.id"
                                                                        class="nav-link"
                                                                        :class="i + 1 == 1 ? 'active' : ''"
                                                                        id="nav-home-tab"
                                                                        data-toggle="tab"
                                                                        :href="'#nav-' + i"
                                                                        role="tab"
                                                                        aria-controls="nav-home"
                                                                        aria-selected="true"
                                                                        >V{{ i + 1 }}</a
                                                                    >
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content" id="nav-tabContent">
                                                                <div
                                                                    v-for="(pago, i) in infoInscripcion.pagos"
                                                                    :key="pago.id"
                                                                    class="tab-pane fade show"
                                                                    :class="i + 1 == 1 ? 'active' : ''"
                                                                    :id="'nav-' + i"
                                                                    role="tabpanel"
                                                                    aria-labelledby="nav-home-tab"
                                                                >
                                                                    <div class="container-fluid">
                                                                        <div class="row">
                                                                            <div class="col-xs-12 col-md-6">
                                                                                <b>Secuencia</b>
                                                                                <p class="m-0">
                                                                                    {{ pago.secuencia }}
                                                                                </p>
                                                                                <b>Fecha</b>
                                                                                <p class="m-0">
                                                                                    {{ pago.fecha.split("-")[2] + "-" + pago.fecha.split("-")[1] + "-" + pago.fecha.split("-")[0] }}
                                                                                </p>
                                                                                <div v-if="pago.folio != null && pago.folio != ''">
                                                                                    <b>Folio</b>
                                                                                    <p class="m-0">
                                                                                        {{ pago.folio }}
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xs-12 col-md-6">
                                                                                <b>Monto</b>
                                                                                <p class="m-0">
                                                                                    {{ pago.monto }}
                                                                                </p>
                                                                                <b>Tipo de Pago</b>
                                                                                <p class="m-0">
                                                                                    {{ pago.tipo_pago == 1 ? "Deposito Normal" : "Por Descuento" }}
                                                                                </p>
                                                                                <a
                                                                                    v-if="rol == 1 || rol == 2"
                                                                                    class="btn btn-dark btn-sm"
                                                                                    :href="'../../storage/vouchers/' + pago.voucher"
                                                                                    target="_blank"
                                                                                    >Ver Voucher</a
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-else>
                                                            <div class="alert alert-warning" role="alert">
                                                                <strong>No existen vouchers para esta inscripción</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card mb-2 shadow-sm rounded">
                                            <div class="card-header text-white text-center bg-info py-1">
                                                <b class="card-title mb-0">Detalle de Pagos</b>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    <div class="col-xs-12 col-md-12">
                                                        <div v-if="infoInscripcion.inscripcionPagos.length != 0">
                                                            <div class="row">
                                                                <table class="table table-sm table-borderless">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>
                                                                                Concepto
                                                                            </th>
                                                                            <th>
                                                                                Monto
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="pago in infoInscripcion.inscripcionPagos" :key="pago.id">
                                                                            <td scope="row">
                                                                                {{ pago.concepto_pago.denominacion }}
                                                                            </td>
                                                                            <td class="float-right">
                                                                                {{ pago.monto }}
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>
                                                                                <b>TOTAL</b>
                                                                            </td>
                                                                            <td class="float-right">
                                                                                <b>{{ infoInscripcion.sumaTotalPagos }}</b>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div v-else>
                                                            <div class="alert alert-warning" role="alert">
                                                                <strong>No existen pagos para esta inscripción</strong>
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
            ///table//
            columns: [
                "id",
                "estado",
                "estudiante.nro_documento",
                "estudiante.paterno",
                "estudiante.materno",
                "estudiante.nombres",
                "asistencia",
                "sede.denominacion",
                "area.denominacion",
                "turno.denominacion",
                "tipo_estudiante",
                "monto",
                "actions"
            ],
            options: {
                headings: {
                    id: "id",
                    estado: "Estado",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Apellido Paterno",
                    "estudiante.materno": "Apellido Materno",
                    "estudiante.nombres": "Nombres",
                    asistencia: "Asistencia",
                    "sede.denominacion": "Sede",
                    "area.denominacion": "Area",
                    "turno.denominacion": "Turno",
                    tipo_estudiante: "TipoDescuento",
                    monto: "Importe",
                    actions: "Acciones"
                },
                sortable: ["id", "estado"],
                filterable: [
                    "estado",
                    "estudiante.nro_documento",
                    "estudiante.paterno",
                    "estudiante.materno",
                    "estudiante.nombres",
                    "sede.denominacion",
                    "area.denominacion",
                    "turno.denominacion",
                    "monto"
                ],
                listColumns: {
                    estado: [
                        {
                            id: "1",
                            text: "Inscrito"
                        },
                        {
                            id: "0",
                            text: "Pre Inscrito"
                        }
                    ]
                },
                // customFilters: ['correlativo','num_mat']
                filterByColumn: true
            },
            json_fields: [
                {
                    label: "id",
                    field: "id"
                },
                {
                    label: "Estado",
                    field: "estado",
                    dataFormat: this.formatEstado
                },
                {
                    label: "Documento",
                    field: "estudiante.nro_documento"
                },
                {
                    label: "Apellido Paterno",
                    field: "estudiante.paterno"
                },
                {
                    label: "Apellido Materno",
                    field: "estudiante.materno"
                },
                {
                    label: "Nombres",
                    field: "estudiante.nombres"
                },
                {
                    label: "Asistencias",
                    field: "asistencia"
                    // dataFormat: this.formatApto
                },
                {
                    label: "Sede",
                    field: "sede.denominacion"
                },
                {
                    label: "Area",
                    field: "area.denominacion"
                },
                {
                    label: "Turno",
                    field: "turno.denominacion"
                },
                {
                    label: "Tipo Descuento",
                    field: "tipo_estudiante",
                    dataFormat: this.formatTipoDescuento
                },
                {
                    label: "Importe",
                    field: "monto"
                }
            ],
            json_data: [],
            infoInscripcion: {
                id: "",
                correlativo: "",
                tipo_estudiante: "",
                estado: "",
                cantidad_inscrito: "",
                area: "",
                sede: "",
                periodos: "",
                turno: "",
                pagos: [],
                inscripcionPagos: [],
                sumaTotalPagos: 0
            },
            infoEstudiante: {
                nombres: "",
                paterno: "",
                materno: "",
                nro_documento: "",
                celular: "",
                email: "",
                usuario: "",
                foto: "",
                tipo_colegio: ""
            },
            statusPago: true,
            statusActualizar: false,
            statusAgregarPago: false,
            pagoRegistrado: "",
            inscripcionEditada: [],
            rol: 1,
            tipo_descuentos: [
                {
                    denominacion: "Normal (sin descuento)",
                    id: 1
                },
                {
                    denominacion: "Descuento por Hijo de trabajador",
                    id: 2
                },
                {
                    denominacion: "Descuento por planilla",
                    id: 3
                },
                {
                    denominacion: "Descuento por hermanos",
                    id: 4
                },
                {
                    denominacion: "Descuento por Resolucion Rectoral",
                    id: 5
                }
            ],
            estados: [
                {
                    denominacion: "Pre Inscrito",
                    id: 0
                },
                {
                    denominacion: "Inscrito",
                    id: 1
                }
            ],
            areas: [],
            sedes: [],
            turnos: [],
            area: "",
            sede: "",
            turno: "",
            tipo_descuento: "",
            estado: ""
        };
    },

    methods: {
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "/intranet/inscripcion/estudiantes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },

        editarInscripcion: function(id) {
            this.verPago();
            axios.get("/intranet/inscripcion/estudiante/" + id).then(response => {
                let inscripcion = response.data.inscripcion;
                let estudiante = response.data.estudiante;
                let area = response.data.area;
                let sede = response.data.sede;
                let periodo = response.data.periodo;
                let turno = response.data.turno;
                let pagos = response.data.pagos;
                let inscripcionPagos = response.data.inscripcionPagos;

                // console.log(response.data);

                this.infoEstudiante.nombres = estudiante.nombres;
                this.infoEstudiante.paterno = estudiante.paterno;
                this.infoEstudiante.materno = estudiante.materno;
                this.infoEstudiante.nro_documento = estudiante.nro_documento;
                this.infoEstudiante.celular = estudiante.celular;
                this.infoEstudiante.email = estudiante.email;
                this.infoEstudiante.usuario = estudiante.usuario;
                this.infoEstudiante.foto = estudiante.foto;
                this.infoEstudiante.tipo_colegio = estudiante.colegio.tipo_colegios_id;

                this.infoInscripcion.id = inscripcion.id;
                this.infoInscripcion.correlativo = inscripcion.correlativo;

                switch (inscripcion.tipo_estudiante) {
                    case "2":
                        this.infoInscripcion.tipo_estudiante = "Descuento por Hijo de trabajador";
                        break;
                    case "3":
                        this.infoInscripcion.tipo_estudiante = "Descuento por planilla";
                        break;
                    case "4":
                        this.infoInscripcion.tipo_estudiante = "Descuento por hermanos";
                        break;
                    case "5":
                        this.infoInscripcion.tipo_estudiante = "Descuento por Resolucion Rectoral";
                        break;
                    default:
                        this.infoInscripcion.tipo_estudiante = "Normal (sin descuento)";
                        break;
                }
                this.infoInscripcion.estado = inscripcion.estado;
                this.infoInscripcion.cantidad_inscrito = inscripcion.cantidad_inscrito;

                this.infoInscripcion.area = area.denominacion;
                this.infoInscripcion.sede = sede.denominacion;
                this.infoInscripcion.periodo = periodo.inicio_ciclo + " - " + periodo.fin_ciclo;
                this.infoInscripcion.turno = turno.denominacion;
                this.infoInscripcion.pagos = pagos;
                this.infoInscripcion.inscripcionPagos = inscripcionPagos;
                this.infoInscripcion.sumaTotalPagos = response.data.sumaTotalPagos;
            });

            // this.infoInscripcion.id = id;
            $("#modalEditarInscripcion").modal("show");
        },
        verPago: function() {
            this.statusPago = true;
            this.statusActualizar = false;
            this.statusAgregarPago = false;
        },
        actualizarInscripcion: function() {
            this.statusPago = false;
            this.statusActualizar = true;
            this.statusAgregarPago = false;
        },
        agregarPago: function() {
            this.statusPago = false;
            this.statusActualizar = false;
            this.statusAgregarPago = true;
        },
        getRol: function() {
            axios.get("../get-rol/").then(response => {
                this.rol = response.data.role_id;
            });
        },
        formatEstado: function(value) {
            if (value == "1") {
                return "Inscrito";
            } else {
                return "Pre Inscrito";
            }
        },
        getDataExcel: function() {
            axios
                .get("/intranet/reporte/estudiante/lista", {
                    params: {
                        all: true,
                        sede: this.sede,
                        area: this.area,
                        turno: this.turno,
                        tipo_descuento: this.tipo_descuento,
                        estado: this.estado
                    }
                })
                .then(response => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.json_data = response.data;
                    // console.log(response.data);
                });
        },
        changeSede: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                sede: this.sede,
                area: this.area,
                turno: this.turno,
                tipo_descuento: this.tipo_descuento,
                estado: this.estado
            });
        },
        changeArea: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                sede: this.sede,
                area: this.area,
                turno: this.turno,
                tipo_descuento: this.tipo_descuento,
                estado: this.estado
            });
        },
        changeTurno: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                sede: this.sede,
                area: this.area,
                turno: this.turno,
                tipo_descuento: this.tipo_descuento,
                estado: this.estado
            });
        },
        changeDescuento: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                sede: this.sede,
                area: this.area,
                turno: this.turno,
                tipo_descuento: this.tipo_descuento,
                estado: this.estado
            });
        },
        changeEstado: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                sede: this.sede,
                area: this.area,
                turno: this.turno,
                tipo_descuento: this.tipo_descuento,
                estado: this.estado
            });
        },
        getSedes: function() {
            axios.get("/intranet/get-sedes").then(response => {
                this.sedes = response.data;
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
        formatTipoDescuento: function(value) {
            switch (value) {
                case "2":
                    return "Descuento por Hijo de trabajador";
                    break;
                case "3":
                    return "Descuento por planilla";
                    break;
                case "4":
                    return "Descuento por hermanos";
                    break;
                case "5":
                    return "Descuento por Resolucion Rectoral";
                    break;
                default:
                    return "Normal (sin descuento)";
                    break;
            }
        }
    },
    mounted() {
        // this.getRol();
        this.getDataExcel();
        this.getSedes();
        this.getAreas();
        this.getTurnos();
    },
    watch: {
        pagoRegistrado: function() {
            // watch it
            this.editarInscripcion(this.infoInscripcion.id);
            this.$refs.table.refresh();
        },
        inscripcionEditada: function() {
            // watch it
            this.editarInscripcion(this.infoInscripcion.id);
            this.$refs.table.refresh();
        }
    }
};
</script>

<style></style>
