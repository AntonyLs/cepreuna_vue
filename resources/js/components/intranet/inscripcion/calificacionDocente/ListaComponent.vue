<template>
    <div>
        <div class="row">

            <div class="form-group mb-0 col-2">
                <label for="area">Area</label>
                <select class="form-control" v-model="area" @change="changeArea">
                    <option value="">--Seleccionar--</option>
                    <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                </select>
            </div>
            <div class="col-2">

                <excel-export
                    class="btn btn-success"
                    :data="json_data"
                    :columns="json_fields"
                    :filename="'filename'"
                    :sheetname="'sheetname'"
                    >
                    Descargar excel
                </excel-export>
            </div>
        </div>
        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/inscripcion/calificacion-docente/lista/data">
            <div slot="actions" slot-scope="props">
                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="calificarDocente(props.row.id)" data-toggle="modal" data-target="#modalcalificarDocente">
                    <i class="fas fa-file-signature"></i>
                </button>
            </div>
            <div slot="apto" slot-scope="props">
                <template v-if="props.row.apto == '1'">
                    <span class="badge badge-success">SI</span>
                </template>
                <template v-else>
                    <span class="badge badge-danger">NO</span>
                </template>
                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
            </div>
            <div slot="sedes" slot-scope="props">
                {{getSedes(props.row.disponibilidades)}}
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
        <!-- Modal  Editar de Inscripcion-->
        <form @submit.prevent="submit">
            <div
            class="modal fade"
                id="modalcalificarDocente"
                data-backdrop="static"
                data-keyboard="false"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="exampleModalLabel">Calificacion Inscripcion Docente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                            <div class="modal-body bg-gray-100">
                                <div class="container p-0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-2 shadow-sm rounded">
                                                <div class="card-body py-2">
                                                    <template v-if="calificado">
                                                        <div class="row" v-for="criterio in criterios">
                                                            <div class="form-group col-12">
                                                                <label for="puntaje">{{criterio.denominacion}}</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon3"> 0 de a {{criterio.puntaje}}</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="puntaje" v-model="fields.puntaje[criterio.id]" :max="criterio.puntaje" min="0" required />
                                                                <!-- <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3"> -->
                                                                </div>
                                                                <div v-if="errors && errors.puntaje" class="text-danger">
                                                                {{ errors.puntaje[0] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="row" v-for="criterio in criterios">
                                                            <div class="form-group col-12">
                                                                <label for="puntaje">{{criterio.denominacion}}</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text" id="basic-addon3"> 0 de a {{criterio.puntaje}}</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" id="puntaje" v-model="fields.puntaje[criterio.id]" :max="criterio.puntaje" min="0" value="0" required />
                                                                <!-- <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3"> -->
                                                                </div>
                                                                <div v-if="errors && errors.puntaje" class="text-danger">
                                                                {{ errors.puntaje[0] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                                <div class="card-footer text-right">
                                                    <button type="submit" class="btn btn-success">Guardar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-1 shadow-sm rounded">
                                                <div class="card-header text-center py-1"><b class="card-title mb-0">Titulo y Grado Academico</b></div>
                                                <div class="card-body py-2">
                                                    <table class="table table-sm table-borderless">
                                                        <tr v-for="value in grado">
                                                            <th>{{value.grado_academico.denominacion}}</th>
                                                            <td><a :href="'/storage/inscripcion/'+value.path" target="_blank">{{value.programa.denominacion}}</a></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card mb-1 shadow-sm rounded">
                                                <div class="card-header text-center py-1"><b class="card-title mb-0">Experiencia CepreUna</b></div>
                                                <div class="card-body py-2">
                                                    <table class="table table-sm table-borderless">
                                                        <tr v-for="value in experiencia">
                                                            <!-- <th>{{value.experiencia.denominacion}}</th> -->
                                                            <td><a :href="'/storage/inscripcion/'+value.path" target="_blank">{{value.experiencia.denominacion}}</a></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card mb-1 shadow-sm rounded">
                                                <div class="card-header text-center py-1"><b class="card-title mb-0">Actualizaciones y Capacitaciones</b></div>
                                                <div class="card-body py-2">
                                                    <table class="table table-sm table-borderless">
                                                        <tr v-for="value in capacitacion">
                                                            <th>{{value.capacitacion_tipo.denominacion}}</th>
                                                            <td><a :href="'/storage/inscripcion/'+value.path" target="_blank">{{value.titulo}}</a></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="card mb-1 shadow-sm rounded">
                                                <div class="card-header text-center py-1"><b class="card-title mb-0">Producción Intelectual</b></div>
                                                <div class="card-body py-2">
                                                    <table class="table table-sm table-borderless">
                                                        <tr v-for="value in produccion">
                                                            <th>{{value.produccion.denominacion}}</th>
                                                            <td><a :href="'/storage/inscripcion/'+value.path" target="_blank">{{value.titulo}}</a></td>
                                                        </tr>
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
            url: "",
            errors: {},
            calificar: false,
            fields:{puntaje:[],id:0},
            criterios:[],
            calificado:false,
            areas:[],
            area:"",
            ///table//
            columns: ["id", "apto", "puntaje", "docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres","area.denominacion",'cursos',"sedes" ,"actions"],
            options: {
                headings: {
                    id: "id",
                    apto: "apto",
                    puntaje: "puntaje",
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    "area.denominacion": "Area",
                    "cursos": "Cursos",
                    sedes: "Sedes",
                    actions: "Acciones"
                },
                sortable: ["id", "apto", "puntaje"],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true
            },
            grado:[],
            experiencia:[],
            capacitacion:[],
            produccion:[],
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
                    label: "puntaje",
                    field: "puntaje"
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
                    label: "Condicion",
                    field: "docente.condicion",
                    dataFormat:this.getCondicion
                },
                {
                    label: "Area",
                    field: "area.denominacion"
                },
                {
                    label: "Cursos",
                    field: "cursos"
                },
                {
                    label: "Sedes",
                    field: "disponibilidades",
                    dataFormat: this.getSedes
                },
                {
                    label: "Grados",
                    field: "grados",
                    dataFormat: this.getGrados
                },
                {
                    label: "Experiencias",
                    field: "experiencias",
                    dataFormat: this.getExperiencias
                },
            ],
            json_data: []

        };
    },

    methods: {
        ficha: function(id) {
            axios.get("/intranet/encrypt/" + id).then(response => {
                this.url = "docentes/pdf/" + response.data;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        submit(){
            $(".loader").show();
            if(this.calificado){
                axios
                    .put("/intranet/inscripcion/calificacion-docente/"+this.fields.id, this.fields)
                    .then(response => {
                        if (response.data.status) {
                            // this.editCursos = false;
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            // this.infoInscripcion.apto = this.fieldsCalificar.apto;
                            // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                            $("#modalcalificarDocente").modal("hide");
                            $('#modalcalificarDocente').data('bs.modal',null);
                            $(".modal-backdrop").remove();
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                        $(".loader").hide();
                    })
                    .catch(error => {
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }else{
                axios
                    .post("/intranet/inscripcion/calificacion-docente", this.fields)
                    .then(response => {
                        if (response.data.status) {
                            // this.editCursos = false;
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            // this.infoInscripcion.apto = this.fieldsCalificar.apto;
                            // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                            $("#modalcalificarDocente").modal("hide");
                            $('#modalcalificarDocente').data('bs.modal',null);
                            $(".modal-backdrop").remove();
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                        $(".loader").hide();
                    })
                    .catch(error => {
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });

            }
        },
        calificarDocente:function(id){
            this.fields.id = id;
            axios
            .get("/intranet/get-criterio-docente-inscripcion/"+id)
            .then(response => {
                // console.log(response.data);
                this.criterios = response.data.data;
                this.calificado = response.data.status;
                this.fields.puntaje = response.data.puntaje;
                // this.allCursos = cursos;

            });

            axios
            .get("/intranet/get-inscripcion-file/"+id)
            .then(response => {
                this.grado = response.data.grado
                this.experiencia = response.data.experiencia
                this.capacitacion = response.data.capacitacion
                this.produccion = response.data.produccion
            });
            $("#modalcalificarDocente").modal("show");

        },
        formatApto: function(value) {
            if (value == "1") {
                return "Si";
            }
            return "No";
        },
        changeArea: function() {
            this.getDataExcel();
            this.$refs.table.setCustomFilters({
                // sede: this.sede,
                area: this.area,
                // turno: this.turno,
                // tipo_descuento: this.tipo_descuento,
                // estado: this.estado
            });
        },
        getAreas: function() {
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getDataExcel: function() {
            axios
                .get("/intranet/inscripcion/calificacion-docente/lista/data", {
                    params: {
                        all: true,
                        area: this.area,
                    }
                })
                .then(response => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.json_data = response.data;
                    // console.log(response.data);
                });
        },
        getSedes:function(dispo){
            let hash = {};
            var temp = {}
            temp = dispo.filter(f => {
                return hash[f.sedes_id] ? false : (hash[f.sedes_id] = true);
            });
            let sedes = "";
            temp.forEach(element => {
                sedes+=element.sede.denominacion+"|";
            });
            return sedes.slice(0,-1)
            // console.log(temp);
        },
        getGrados:function(datos){

            let grados = "";
            datos.forEach(element => {
                grados+=element.grado_academico.denominacion+":"+element.programa.denominacion+"|";
            });
            return grados.slice(0,-1)
            // console.log(temp);
        },
        getExperiencias:function(datos){

            let experiencias = "";
            datos.forEach(element => {
                experiencias+=element.experiencia.denominacion+"|";
            });
            return experiencias.slice(0,-1)
            // console.log(temp);
        },
        getCondicion:function(datos){

            if(datos=='1'){
                return "Particular";
            }else{
                return "Unap";
            }
            // console.log(temp);
        }
        // getCriterios:function(){
        //     axios.get('/intranet/get-criterio-docente-inscripcion')
        //     .then(function (response) {
        //         this.criterios = response.data;
        //     }.bind(this));
        // }
    },
    mounted() {
        this.getAreas();
        this.getDataExcel();
        // axios.get("/intranet/inscripcion/calificacion-docente/lista/data?all=true").then(response => {
        //     // this.url = "docentes/pdf/"+response.data;
        //     this.json_data = response.data;
        //     // console.log(response.data);
        // });
    }
};
</script>

<style></style>
