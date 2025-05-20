<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <h4>Reclamaciones Presentadas</h4>
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <div class="form-group mb-0 col-4">
                                        <label for="">Estado</label>
                                        <select class="form-control" v-model="estado" @change="changeEstado">
                                            <option value="">--Seleccionar--</option>
                                            <option value="0">Pendiente</option>
                                            <option value="1">Atendido</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <div class="card-body">
                        <v-server-table
                            ref="table"
                            :columns="columns"
                            :options="options"
                            url="/intranet/libro-reclamaciones/lista/data"
                        >
                            <!-- <div slot="asunto" slot-scope="props"></div>
                            <div slot="fecha" slot-scope="props"></div> -->
                            <div slot="estado" slot-scope="props">
                                <template v-if="props.row.estado == '0'">
                                    <span class="badge badge-secondary"
                                        >Pendiente</span
                                    >
                                </template>
                                <template v-else-if="props.row.estado == '1'">
                                    <span class="badge badge-success"
                                        >Atendido</span
                                    >
                                </template>
                            </div>
                            <div slot="accion" slot-scope="props">
                                <button
                                    v-if="props.row.estado == '0'"
                                    type="button"
                                    class="p-0 m-0 h5 btn btn-link text-info"
                                    @click="responderReclamo(props.row.id)"
                                    data-toggle="modal"
                                    data-target="#modalresponderReclamo"
                                >
                                    <i class="fas fa-envelope-open"></i>
                                </button>
                                <button
                                    v-if="props.row.estado == '1'"
                                    type="button"
                                    class="p-0 m-0 h5 btn btn-link text-info"
                                    @click="infoReclamo(props.row.id)"
                                    data-toggle="modal"
                                    data-target="#modalinfoReclamo"
                                >
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </v-server-table>
                        <form @submit.prevent="submit">
                            <div
                                class="modal fade"
                                id="modalresponderReclamo"
                                data-keyboard="false"
                                tabindex="-1"
                                role="dialog"
                                aria-labelledby="exampleModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div
                                            class="modal-header bg-info text-white"
                                        >
                                            <h5
                                                class="modal-title"
                                                id="exampleModalLabel"
                                            >
                                                Responder Reclamo
                                            </h5>
                                            <button
                                                type="button"
                                                class="close"
                                                data-dismiss="modal"
                                                aria-label="Close"
                                            >
                                                <span aria-hidden="true"
                                                    >&times;</span
                                                >
                                            </button>
                                        </div>
                                        <div class="modal-body bg-gray-100">
                                            <div class="container p-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card mb-2 shadow-sm rounded">
                                                            <div class="card-body py-2">
                                                                <template>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Descripcion/Asunto <a class="btn btn-info btn-sm text-white" @click="verEvidencia(datosReclamo.id)">Ver Evidencia</a></label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.descripcion" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="">Detalle</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.detalle" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="">Pedido</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.pedido" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Respuesta al Reclamo</label>
                                                                            <div>
                                                                                <input type="hidden" :v-model="respuesta.id = datosReclamo.id">
                                                                                <textarea name="" v-model="respuesta.texto" class="form-control" id="" rows="2" placeholder="Ingrese la respuesta al reclamo."></textarea>
                                                                                <small style="color: #FF0000">{{errors[0]}}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                            <div class="card-footer text-right">
                                                                <button type="submit" class="btn btn-success">
                                                                    Guardar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card mb-1 shadow-sm rounded">
                                                            <div class="card-header text-center py-1">
                                                                <b class="card-title mb-0">Visualizar Evidencia</b>
                                                            </div>
                                                            <div class="card-body py-2">
                                                            <!-- <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="600" style="height: 85vh;"></object> -->
                                                                <object :data="url" class="embed-responsive-item" type="image/jpg" allowfullscreen width="100%" height="600" style="height: 85vh;"></object>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form>
                            <div
                                class="modal fade"
                                id="modalinfoReclamo"
                                data-keyboard="false"
                                tabindex="-1"
                                role="dialog"
                                aria-labelledby="exampleModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div
                                            class="modal-header bg-info text-white"
                                        >
                                            <h5
                                                class="modal-title"
                                                id="exampleModalLabel"
                                            >
                                                Información del Reclamo
                                            </h5>
                                            <button
                                                type="button"
                                                class="close"
                                                data-dismiss="modal"
                                                aria-label="Close"
                                            >
                                                <span aria-hidden="true"
                                                    >&times;</span
                                                >
                                            </button>
                                        </div>
                                        <div class="modal-body bg-gray-100">
                                            <div class="container p-0">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card mb-2 shadow-sm rounded">
                                                            <div class="card-body py-2">
                                                                <template>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Descripcion/Asunto <a class="btn btn-info btn-sm text-white" @click="verEvidencia(datosReclamo.id)">Ver Evidencia</a></label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.descripcion" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="">Detalle</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.detalle" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group col-12">
                                                                            <label for="">Pedido</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.pedido" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Respuesta al Reclamo</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="2" :placeholder="datosReclamo.respuesta" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Usuario que respondió al reclamo</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="1" :placeholder="datosReclamo.user_name + ' ' + datosReclamo.user_paterno + ' ' + datosReclamo.user_materno" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="form-group col-12">
                                                                            <label for="">Fecha de Respuesta</label>
                                                                            <div>
                                                                                <textarea name="" class="form-control" id="" rows="1" :placeholder="datosReclamo.fecha_respuesta" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card mb-1 shadow-sm rounded">
                                                            <div class="card-header text-center py-1">
                                                                <b class="card-title mb-0">Visualizar Evidencia</b>
                                                            </div>
                                                            <div class="card-body py-2">
                                                            <!-- <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="600" style="height: 85vh;"></object> -->
                                                                <object :data="url" class="embed-responsive-item" type="image/jpg" allowfullscreen width="100%" height="600" style="height: 85vh;"></object>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
    props: ["permissions", "external_url"],
    data() {
        return {
            url: "",
            errors: [],
            estado: "",
            responder: false,
            columns: [
                "id",
                "dni",
                "paterno",
                "materno",
                "nombres",
                "descripcion",
                "fecha_ingreso",
                "estado",
                "accion"
            ],
            options: {
                headings: {
                    id: "ID",
                    dni: "DNI",
                    paterno: "Paterno",
                    materno: "Materno",
                    nombres: "Nombres",
                    descripcion: "Asunto",
                    fecha_ingreso: "Fecha de Ingreso",
                    estado: "Estado"
                },
                sortable: ["id"],
                filterable: ["dni", "paterno", "materno", "nombres","descripcion"],
                filterByColumn: true
            },
            json_data: [],
            datosReclamo: [],
            respuesta: {id:null, texto:null,fecha_respuesta:""},
        };
    },
    methods: {
        verEvidencia: function(id){
            // console.log(id);
            this.url = this.external_url + "/lr-show-evidencia/" + id;
        },
        submit(){
            $(".loader").show();
            // console.log(this.respuesta);
            if(this.respuesta.texto != null){
                var today = new Date();
                this.respuesta.fecha_respuesta = today.getFullYear() + "/" + String(today.getMonth() + 1).padStart(2, "0") + "/" + String(today.getDate()).padStart(2, "0");
                axios.post("/intranet/libro-reclamaciones/responder-reclamo/", this.respuesta)
                .then(response => {
                    if(response.data.status){
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        console.log(response.data.reclamo);
                        $("#modalresponderReclamo").modal("hide");
                        $("#modalresponderReclamo").data("bd.modal",null);
                        $(".modal-backdrop").remove();
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    $(".loader").hide();
                })
            } else {
                this.errors.push('La respuesta es obligatoria.');
                $(".loader").hide();
            }
        },
        responderReclamo: function(id) {
            axios.get("/intranet/libro-reclamaciones/get-datos-reclamo/" + id)
                .then(response => {
                    this.datosReclamo = response.data[0];
                // console.log(this.datosReclamo);
                });
            $("modalresponderReclamo").modal("show");
        },
        changeEstado: function(){
            this.$refs.table.setCustomFilters({estado: this.estado});
        },
        infoReclamo: function(id) {
            axios.get("/intranet/libro-reclamaciones/get-info-reclamo/" + id)
                .then(response => {
                    this.datosReclamo = response.data[0];
                // console.log(this.datosReclamo);
                });
            $("modalinfoReclamo").modal("show");
        },
    },
};
</script>
