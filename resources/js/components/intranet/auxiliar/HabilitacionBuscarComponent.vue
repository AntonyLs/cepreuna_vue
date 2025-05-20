<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Habilitacion por Documento
                    </header>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" v-model="documento" placeholder="Ingresar Documento" aria-label="Ingresar Documento" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" @click="buscar">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card" v-if="estado">
                    <div class="card-header">
                        Sincronización :
                        <template>
                            <span class="badge badge-success" v-if="matricula.habilitado_estado == '1'">SINCRONIZADO</span>
                            <span class="badge badge-warning" v-else>PENDIENTE</span>
                        </template>
                        <template v-if="matricula.habilitado_estado != '1'">
                            <button class="btn btn-success float-right" @click="habilitar" v-if="matricula.habilitado == '0'"><i class="fa fa-check"></i> Habilitar</button>
                            <button class="btn btn-danger float-right" @click="deshabilitar" v-if="matricula.habilitado == '1'"><i class="fa fa-times"></i> Deshabilitar</button>
                        </template>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success text-center" role="alert" v-if="matricula.habilitado == '1'"><strong>HABILITADO</strong></div>
                                <div class="alert alert-danger text-center" role="alert" v-if="matricula.habilitado == '0'"><strong>PENDIENTE</strong></div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-2 shadow-sm rounded">
                                    <div class="row no-gutters">
                                        <div class="col-md-4 col-xs-12">
                                            <img :src="'../../storage/fotos/' + infoEstudiante.foto" class="card-img border-right" alt="" />
                                        </div>
                                        <div class="col-md-8 col-xs-12">
                                            <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Datos Personales del Estudiante</b></div>
                                            <div class="card-body py-2">
                                                <b>Nombres y Apellidos:</b>
                                                <p class="m-0">{{ infoEstudiante.nombres + " " + infoEstudiante.paterno + " " + infoEstudiante.materno }}</p>
                                                <b>Número de Documento:</b>
                                                <p class="m-0">{{ infoEstudiante.nro_documento }}</p>
                                                <b>Celular:</b>
                                                <p class="m-0">{{ infoEstudiante.celular }}</p>
                                                <b>Correo Electrónico</b>
                                                <p class="m-0">{{ infoEstudiante.email }}</p>
                                                <b>Cuenta Institucional</b>
                                                <p class="m-0">{{ infoEstudiante.usuario }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-2 shadow-sm rounded">
                                    <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Datos de Inscripción</b></div>
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <b>Correlativo</b>
                                                <p class="m-0">{{ infoInscripcion.correlativo }}</p>
                                                <b>Tipo Descuento por Estudiante</b>
                                                <p class="m-0">{{ infoInscripcion.tipo_estudiante }}</p>
                                                <b>Cantidad de Veces Inscrito</b>
                                                <p class="m-0">{{ infoInscripcion.cantidad_inscrito }}</p>
                                                <b>Estado</b>
                                                <h6>
                                                    <template v-if="infoInscripcion.estado == '0'">
                                                        <span class="badge badge-warning">Pre Inscrito</span>
                                                    </template>
                                                    <template v-else-if="infoInscripcion.estado == '1'">
                                                        <span class="badge badge-success">Inscrito</span>
                                                    </template>
                                                    <template v-else-if="infoInscripcion.estado == '2'">
                                                        <span class="badge badge-danger">Retirado</span>
                                                    </template>
                                                </h6>
                                            </div>
                                            <div class="col-xs-12 col-md-6">
                                                <b>Sede</b>
                                                <p class="m-0">{{ infoInscripcion.sede }}</p>
                                                <b>Área</b>
                                                <p class="m-0">{{ infoInscripcion.area }}</p>
                                                <b>Turno</b>
                                                <p class="m-0">{{ infoInscripcion.turno }}</p>
                                                <b>Periodo</b>
                                                <p class="m-0">{{ infoInscripcion.periodo }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-2 shadow-sm rounded">
                                    <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Detalle de Vouchers</b></div>
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
                                                                        <p class="m-0">{{ pago.secuencia }}</p>
                                                                        <b>Fecha</b>
                                                                        <p class="m-0">
                                                                            {{ pago.fecha.split("-")[2] + "-" + pago.fecha.split("-")[1] + "-" + pago.fecha.split("-")[0] }}
                                                                        </p>
                                                                        <div v-if="pago.folio != null && pago.folio != ''">
                                                                            <b>Folio</b>
                                                                            <p class="m-0">{{ pago.folio }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-xs-12 col-md-6">
                                                                        <b>Monto</b>
                                                                        <p class="m-0">{{ pago.monto }}</p>
                                                                        <b>Tipo de Pago</b>
                                                                        <p class="m-0">{{ pago.tipo_pago == 1 ? "Deposito Normal" : "Por Descuento" }}</p>
                                                                        <a class="btn btn-dark btn-sm" :href="'../../storage/vouchers/' + pago.voucher" target="_blank">Ver Voucher</a>
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
                                    <div class="card-header text-white text-center bg-info py-1"><b class="card-title mb-0">Detalle de Pagos</b></div>
                                    <div class="card-body py-2">
                                        <div class="row">
                                            <div v-if="sumatoriaPagos > 0" class="col-xs-12 col-md-12 text-center mb-2">
                                                suma Vouchers: <span class="badge badge-secondary"> {{ sumatoriaPagos }}</span> suma Tarifa:
                                                <span class="badge badge-secondary"> {{ sumatoriaTarifas }}</span>
                                                <span v-if="sumatoriaPagos == sumatoriaTarifas" class="badge badge-success">CORRECTO</span>
                                                <span v-else class="badge badge-danger">INCORRECTO</span>
                                            </div>
                                            <div class="col-xs-12 col-md-12">
                                                <div v-if="infoInscripcion.tarifaEstudiante.length != 0" class="row">
                                                    <table role="table" class="table table-sm table-borderless">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 50px">
                                                                    <div>
                                                                        <span class="p-column-title">Cuota</span>
                                                                    </div>
                                                                </th>
                                                                <th>
                                                                    <div>
                                                                        <span class="p-column-title">Tarifa</span>
                                                                    </div>
                                                                </th>
                                                                <th>
                                                                    <div>
                                                                        <span class="p-column-title">Pagado</span>
                                                                    </div>
                                                                </th>
                                                                <th>
                                                                    <div>
                                                                        <span class="p-column-title">Mora</span>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(tarifa, i) in infoInscripcion.tarifaEstudiante" :key="i" role="row" draggable="false">
                                                                <td role="cell">{{ tarifa.nro_cuota == 0 ? "Ins." : tarifa.nro_cuota }}</td>
                                                                <td role="cell">S/ {{ tarifa.monto }}</td>
                                                                <td role="cell">S/ {{ tarifa.pagado }}</td>
                                                                <td role="cell">S/ {{ tarifa.mora }}</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- <div v-if="infoInscripcion.inscripcionPagos.length != 0">
                                                    <div class="row">
                                                        <table class="table table-sm table-borderless">
                                                            <thead>
                                                                <tr>
                                                                    <th>Concepto</th>
                                                                    <th>Monto</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="pago in infoInscripcion.inscripcionPagos" :key="pago.id">
                                                                    <td scope="row">{{ pago.concepto_pago.denominacion }}</td>
                                                                    <td class="float-right">{{ pago.monto }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>TOTAL</b></td>
                                                                    <td class="float-right">
                                                                        <b>{{ infoInscripcion.sumaTotalPagos }}</b>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> -->
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
                <div class="card" v-else>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    {{ message }}
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
    name: "TextEditor",
    props: ["permissions"],
    data() {
        return {
            edit: 0,
            id: 0,
            documento: "",
            estado: false,
            message: "Buscar Estudiante",
            matricula: {},
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
                sumaTotalPagos: 0,
                modalidad: 0,
                tarifaEstudiante: []
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
            sumatoriaPagos: 0,
            sumatoriaTarifas: 0
        };
    },
    methods: {
        buscar: function() {
            axios.get("/intranet/auxiliar/habilitacion/estudiante/" + this.documento).then(response => {
                this.estado = response.data.status;
                this.message = response.data.message;
                if (response.data.status) {
                    this.id = response.data.matricula.id;
                    this.matricula = response.data.matricula;
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
                        case "6":
                            this.infoInscripcion.tipo_estudiante = "Descuento Servicio Militar";
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
                    let sumaPagoVouchers = 0;
                    pagos.forEach(p => {
                        sumaPagoVouchers = parseFloat(sumaPagoVouchers) + parseFloat(p.monto) - 1;
                    });
                    this.sumatoriaPagos = parseFloat(sumaPagoVouchers).toFixed(2);

                    this.infoInscripcion.inscripcionPagos = inscripcionPagos;
                    this.infoInscripcion.sumaTotalPagos = response.data.sumaTotalPagos;
                    this.infoInscripcion.modalidad = inscripcion.modalidad;
                    this.infoInscripcion.tarifaEstudiante = response.data.tarifaEstudiante;

                    let tarifas = response.data.tarifaEstudiante;
                    let sumaTarifa = 0;
                    tarifas.forEach(t => {
                        sumaTarifa = parseFloat(sumaTarifa) + parseFloat(t.pagado) + parseFloat(t.mora);
                    });
                    this.sumatoriaTarifas = parseFloat(sumaTarifa).toFixed(2);
                }
            });
        },
        habilitar: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de habilitar el estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        // console.log(id);
                        $(".loader").show();
                        // var datos = {
                        //     docente: docente,
                        //     carga:carga,
                        // }
                        axios
                            .put("/intranet/auxiliar/habilitacion/habilitar/" + this.id)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    // this.$refs.table.refresh();
                                    this.buscar();
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
        deshabilitar: function(id) {
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de deshabilitar el estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        // console.log(id);
                        $(".loader").show();
                        // var datos = {
                        //     docente: docente,
                        //     carga:carga,
                        // }
                        axios
                            .put("/intranet/auxiliar/habilitacion/deshabilitar/" + this.id)
                            .then(response => {
                                if (response.data.status == true) {
                                    toastr.success(response.data.message);
                                    // this.$refs.table.refresh();
                                    this.buscar();
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
    }
};
</script>
