<style>
.table thead tr:nth-child(1) {
    background-color: #303c54;
    color: white;
}
</style>
<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
      <i class="fa fa-plus"></i> Agregar
    </button>  -->
        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/inscripcion/estudiante/lista/data">
            <div slot="actions" slot-scope="props">
                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarInscripcion(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                    <i class="fas fa-file-signature"></i>
                </button>

                <template v-if="permissions.includes('ver generador de reportes')">
                    <button type="button" class="p-0 m-0 h5 btn btn-link text-success p-1 m-0 h5" @click="agregarTarifario(props.row.estudiantes_id)">
                        <i class="fas fa-sync"></i>
                    </button>
                </template>

                <template v-if="permissions.includes('retirar estudiante')">
                    <button v-if="props.row.estado == '1'" class="btn btn-sm btn-link text-danger p-1 m-0 h5" title="Retirar" @click="eliminarCorreo(props.row.id)">
                        <i class="fas fa-user-minus"></i>
                    </button>
                </template>
            </div>
            <div slot="estado" slot-scope="props">
                <template v-if="props.row.estado == '0'">
                    Pre Inscrito
                </template>
                <template v-else-if="props.row.estado == '1'">
                    Inscrito
                </template>
                <template v-else-if="props.row.estado == '2'">
                    Retirado
                </template>
            </div>
        </v-server-table>
        <!-- Modal Ficha de inscripción -->
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
                        <h5 class="modal-title" id="exampleModalLabel">Editar Inscripción de un Estudiante</h5>
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

                                    <button v-if="permissions.includes('ver pagos estudiante')" type="button" @click="verPago" class="btn btn-info">
                                        <i class="fas fa-dollar-sign"></i> Ver Pagos
                                    </button>
                                    <button v-if="permissions.includes('editar inscripcion estudiante')" type="button" @click="actualizarInscripcion" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Editar Inscripción
                                    </button>
                                    <button v-if="permissions.includes('agregar pago estudiante')" type="button" @click="agregarPago" class="btn btn-success">
                                        <i class="far fa-check-square"></i> Agregar Pago
                                    </button>
                                    <button v-if="permissions.includes('agregar mora estudiante')" type="button" @click="agregarMora" class="btn btn-primary">
                                        <i class="far fa-check-square"></i> Agregar Mora
                                    </button>
                                    <a target="_blank" type="button" :href="'../../storage/inscripcion/' + infoEstudiante.DNI" class="btn btn-dark"><i class="fas fa-user"></i> Ver DNI</a>
                                    <a target="_blank" type="button" :href="'../../storage/inscripcion/' + infoEstudiante.certificado" class="btn btn-secondary mt-1">
                                        <i class="fa fa-list-alt"></i> Ver Certificado de Estudios
                                    </a>
                                </div>

                                <div class="col-md-5  col-xs-12">
                                    <div v-if="statusPago && permissions.includes('ver pagos estudiante')">
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
                                                                                <a
                                                                                    v-if="permissions.includes('ver voucher estudiante')"
                                                                                    class="btn btn-dark btn-sm"
                                                                                    :href="'../../storage/vouchers/' + pago.voucher"
                                                                                    target="_blank"
                                                                                    >Ver Voucher</a
                                                                                >
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <hr />
                                                                    <div class="text-center">
                                                                        <button
                                                                            v-if="permissions.includes('eliminar voucher')"
                                                                            class="btn btn-danger btn-sm"
                                                                            @click="eliminarVoucher(pago.id, infoInscripcion.id)"
                                                                        >
                                                                            Eliminar Voucher
                                                                        </button>
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

                                    <i-agregar-pago-estudiante
                                        :idInscripcion="infoInscripcion.id"
                                        :tipoColegio="infoEstudiante.tipo_colegio"
                                        :modalidad="infoInscripcion.modalidad"
                                        v-if="statusAgregarPago"
                                        @result="pagoRegistrado = $event"
                                    >
                                    </i-agregar-pago-estudiante>
                                    <i-agregar-mora-estudiante
                                        :idInscripcion="infoInscripcion.id"
                                        :tipoColegio="infoEstudiante.tipo_colegio"
                                        :modalidad="infoInscripcion.modalidad"
                                        v-if="statusAgregarMora"
                                        @result="pagoRegistrado = $event"
                                    >
                                    </i-agregar-mora-estudiante>
                                    <i-actualizar-inscripcion-estudiante v-if="statusActualizar" :inscripcion="infoInscripcion" @result="inscripcionEditada = $event">
                                    </i-actualizar-inscripcion-estudiante>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit">
            <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="card mb-1" v-for="(tarifario, key) in tarifarios" :key="tarifario.id">
                                    <div class="card-header py-1">
                                        <b class="text-info">{{ cuotas[tarifario.nro_cuota] }}</b>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Monto</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.monto[key]" />
                                                </div>
                                                <div v-if="errors && errors.inicio" class="text-danger">
                                                    {{ errors.inicio[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Pagado</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.pagado[key]" />
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="inicio">Mora</label>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-sm">S/.</span>
                                                    </div>
                                                    <input type="text" class="form-control" v-model="fields.mora[key]" />
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 mb-0">
                                                <label for="inicio">Modalidad</label>
                                                <div class="input-group input-group-sm">
                                                    <select id="" class="form-control" v-model="fields.modalidad[key]">
                                                        <option v-for="(modalidad, index) in options.listColumns.modalidad" :key="index" :value="modalidad.id">{{ modalidad.text }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4 mb-0">
                                                <label for="inicio">Tipo Estudiante</label>
                                                <div class="input-group input-group-sm">
                                                    <select id="" class="form-control" v-model="fields.tipo_estudiante[key]">
                                                        <option v-for="(tipo, index) in options.listColumns.tipo_estudiante" :key="index" :value="tipo.id">{{ tipo.text }}</option>
                                                    </select>
                                                </div>
                                                <div v-if="errors && errors.fin" class="text-danger">
                                                    {{ errors.fin[0] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                id: [],
                monto: [],
                pagado: [],
                mora: [],
                nro_cuota: [],
                modalidad: [],
                tipo_estudiante: []
            },
            errors: {},
            tarifarios: {},
            cuotas: {},
            url: "",
            ///table//
            columns: [
                "id",
                "correlativo",
                "estado",
                "estudiante.nro_documento",
                "estudiante.paterno",
                "estudiante.materno",
                "estudiante.nombres",
                "sede.denominacion",
                "area.denominacion",
                "turno.denominacion",
                "actions"
            ],
            options: {
                headings: {
                    id: "id",
                    correlativo: "Correlativo",
                    estado: "Estado",
                    "estudiante.nro_documento": "Documento",
                    "estudiante.paterno": "Apellido Paterno",
                    "estudiante.materno": "Apellido Materno",
                    "estudiante.nombres": "Nombres",
                    "sede.denominacion": "Sede",
                    "area.denominacion": "Area",
                    "turno.denominacion": "Turno",
                    //   'periodo.denominacion': 'Periodo',
                    actions: "Acciones"
                },
                sortable: ["id", "correlativo", "estado"],
                filterable: [
                    "correlativo",
                    "estado",
                    "estudiante.nro_documento",
                    "estudiante.paterno",
                    "estudiante.materno",
                    "estudiante.nombres",
                    "sede.denominacion",
                    "area.denominacion",
                    "turno.denominacion"
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
                        },
                        {
                            id: "2",
                            text: "Retirado"
                        }
                    ],
                    tipo_estudiante: [
                        {
                            id: "1",
                            text: "Normal"
                        },
                        {
                            id: "2",
                            text: "Hijo Trabajador"
                        },
                        {
                            id: "3",
                            text: "Descuento por Trabajador UNA"
                        },
                        {
                            id: "4",
                            text: "Hermanos"
                        },
                        {
                            id: "5",
                            text: "Inscripción por RR"
                        },
                        {
                            id: "6",
                            text: "Servicio Militar"
                        }
                    ],
                    modalidad: [
                        {
                            id: "1",
                            text: "Virtual"
                        },
                        {
                            id: "2",
                            text: "Presencial"
                        }
                    ]
                },
                // customFilters: ['correlativo','num_mat']
                filterByColumn: true
            },
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
                tipo_colegio: "",
                DNI: "",
                certificado: ""
            },
            statusPago: true,
            statusActualizar: false,
            statusAgregarPago: false,
            statusAgregarMora: false,
            pagoRegistrado: "",
            inscripcionEditada: [],
            rol: 1,
            sumatoriaPagos: 0,
            sumatoriaTarifas: 0
        };
    },

    methods: {
        agregarTarifario: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Tarifario";
            axios.get("/intranet/administracion/tarifario-estudiante/" + id + "/edit").then(response => {
                this.tarifarios = response.data.tarifarios;
                this.cuotas = response.data.cuotas;
                this.fields.id = response.data.id;
                this.fields.monto = response.data.monto;
                this.fields.pagado = response.data.pagado;
                this.fields.mora = response.data.mora;
                this.fields.nro_cuota = response.data.nro_cuota;
                this.fields.modalidad = response.data.modalidad;
                this.fields.tipo_estudiante = response.data.tipo_estudiante;
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "estudiantes/pdf/" + response.data;
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
                this.infoEstudiante.DNI = estudiante.path_dni;
                this.infoEstudiante.certificado = estudiante.pathCertificado;

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
                this.sumatoriaPagos = parseFloat(sumaPagoVouchers + response.data.adicional).toFixed(2);

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
            });

            // this.infoInscripcion.id = id;
            $("#modalEditarInscripcion").modal("show");
        },
        eliminarVoucher(id, inscripcion) {
            this.$confirm({
                title: "Eliminar Pago",
                message: "¿Esta seguro de eliminar pago?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios.post("/intranet/inscripcion/eliminar-voucher/" + id).then(response => {
                            if (response.data.status) {
                                toastr.success(response.data.message);
                                this.editarInscripcion(inscripcion);
                                $(".loader").hide();
                            } else {
                                toastr.warning(response.data.message, "Error");
                                $(".loader").hide();
                            }
                        });
                    }
                }
            });
        },
        verPago: function() {
            this.statusPago = true;
            this.statusActualizar = false;
            this.statusAgregarPago = false;
            this.statusAgregarMora = false;
        },
        actualizarInscripcion: function() {
            this.statusPago = false;
            this.statusActualizar = true;
            this.statusAgregarPago = false;
            this.statusAgregarMora = false;
        },
        agregarPago: function() {
            this.statusPago = false;
            this.statusActualizar = false;
            this.statusAgregarPago = true;
            this.statusAgregarMora = false;
        },
        agregarMora: function() {
            this.statusPago = false;
            this.statusActualizar = false;
            this.statusAgregarPago = false;
            this.statusAgregarMora = true;
        },
        getRol: function() {
            axios.get("../get-rol/").then(response => {
                this.rol = response.data.role_id;
            });
        },
        eliminarCorreo: function(id) {
            // console.log("holi");
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de Retirar al estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .delete("/intranet/inscripcion/estudiante/retirar/" + id)
                            .then(response => {
                                $(".loader").hide();
                                // console.log(response.data);
                                if (response.data.status) {
                                    toastr.success(response.data.message);
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
        },
        submit: function() {
            this.errors = {};
            if (this.edit == 0)
                axios
                    .post("/intranet/administracion/cronograma-pagos", this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                            // window.location.replace(response.data.url);
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
            else {
                axios
                    .put("/intranet/administracion/tarifario-estudiante/" + this.id, this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            $("#ModalFormulario").modal("hide");
                            // window.location.replace(response.data.url);
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
            }
        }
    },
    mounted() {
        this.getRol();
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
