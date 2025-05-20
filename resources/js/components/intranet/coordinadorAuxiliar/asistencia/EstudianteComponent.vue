<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <select class="form-control" id="area" v-model="area" @change="changeArea">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="area in areas" :value="area.id" :key="area.id">{{area.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <select class="form-control" v-model="turno" @change="changeTurno">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{turno.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sede">Sede</label>
                                    <select class="form-control" v-model="sede" @change="changeSede">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grupo">Grupo</label>
                                    <select class="form-control" v-model="grupo" @change="changeGrupo">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{grupo.grupo}}</option>
                                    </select>
                                    <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-md-4 offset-md-8">
                                <div class="form-group">
                                    <label for="grupo">Fecha</label>
                                    <input type="date" class="form-control" v-model="fecha" @change="changeFecha">
                                    <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Asistencia</h5>
                                        <a :href="urlLista" class="btn btn-danger float-right" v-if="visible" target="_blank">Pdf Lista</a>
                                        <excel-export
                                            class="btn btn-success float-right mr-1"
                                            :data="json_data_lista"
                                            :columns="json_fields_lista"
                                            :filename="'Lista'"
                                            :sheetname="'Exportar Lista Excel'"
                                            v-if="visible"
                                            >
                                            Exportar Lista Excel
                                        </excel-export>
                                    </div>
                                    <div class="card-body">
                                        <template v-if="!revisar">
                                            <table class="table table-bordered table-striped col table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Estudiante</th>
                                                        <th colspan="3">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,i) in estudiantes" :key="i">
                                                        <td>{{i+1}}</td>
                                                        <td>{{item.estudiante.paterno}} {{item.estudiante.materno}} {{item.estudiante.nombres}}</td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                    <input type="radio" v-model="estado[item.estudiante.id]" :value="1" :checked="true"/>
                                                                        Presente
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                <input type="radio" v-model="estado[item.estudiante.id]" :value="2"/>
                                                                    Tarde
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                <input type="radio" v-model="estado[item.estudiante.id]" :value="3"/>
                                                                    Falta
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="form-group col-12">
                                                    <label for="observacion">Observación</label>
                                                    <textarea class="form-control" id="observacion" v-model="observacion"></textarea>
                                                    <!-- <div v-if="errors && errors.denominacion" class="text-danger">
                                                        {{ errors.denominacion[0] }}
                                                    </div> -->
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <table class="table table-bordered table-striped col table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Estudiante</th>
                                                        <th colspan="4">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,i) in estudiantes" :key="i">
                                                        <td>{{i+1}}</td>
                                                        <td>{{item.estudiante.paterno}} {{item.estudiante.materno}} {{item.estudiante.nombres}}</td>
                                                        <td>
                                                            <span class="badge" :class="'badge-'+getEstado(item.estado)[0]">{{getEstado(item.estado)[1]}}</span>
                                                        </td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                    <input type="radio" v-model="estado[item.id]" :value="1" :checked="true"/>
                                                                        Presente
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                <input type="radio" v-model="estado[item.id]" :value="2"/>
                                                                    Tarde
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="radio">
                                                                <label>
                                                                <input type="radio" v-model="estado[item.id]" :value="3"/>
                                                                    Falta
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class="row">
                                                <div class="form-group col-12">
                                                    <label for="observacion">Observación</label>
                                                    <textarea class="form-control" id="observacion" v-model="observacion"></textarea>
                                                    <!-- <div v-if="errors && errors.denominacion" class="text-danger">
                                                        {{ errors.denominacion[0] }}
                                                    </div> -->
                                                </div>
                                            </div>
                                            <div class="row justify-content-end">
                                                <div class="col-md-4 text-right">
                                                    <button class="btn btn-danger" @click="exportarPDF">Exportar PDF</button>
                                                    <excel-export
                                                        class="btn btn-success"
                                                        :data="json_data"
                                                        :columns="json_fields"
                                                        :filename="'AsistenciaEstudiante'"
                                                        :sheetname="'Reporte'"
                                                        >
                                                        Descargar excel
                                                    </excel-export>
                                                    <!-- <button class="btn btn-success" @click="exportarExcel">Exportar Excel</button> -->
                                                </div>
                                            </div>
                                        </template>


                                    </div>
                                    <template>
                                        <div class="card-footer text-muted">
                                            <div class="row">
                                                <button class="btn btn-success btn-lg col" @click="guardar">Guardar Lista</button>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ModalReporte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Reporte de Asistencia</h5>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" > Cerrar </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

import 'vue-select/dist/vue-select.css';
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";

export default {
    props:["fechaHoy"],
    data() {
        return {
            edit:0,
            id:null,
            errors: {},
            fields: {estado:1,sesion:'',observacion:''},
            area:'',
            turno:'',
            grupo:'',
            sede: "",
            visible:false,
            fecha:this.fechaHoy,

            areas:[],
            turnos:[],
            grupos:[],
            sedes: [],

            estudiantes:[],
            estado:[],
            revisar:false,
            estadoRevisado :[],
            observacion :'',
            observacionRealizada :'',

            // id:0,
            url:'',
            urlLista: "",
            json_fields:[
                {
                    label:'DNI',
                    field:'dni'
                },
                {
                    label:'Apellido Paterno',
                    field:'paterno'
                },
                {
                    label:'Apellido Materno',
                    field:'materno'
                },
                {
                    label:'Nombres',
                    field:'nombres'
                },
                {
                    label:'Fecha',
                    field:'fecha'
                },
                {
                    label:'Estado',
                    field:'estado'
                },
                {
                    label:'Usuario',
                    field:'usuario'
                },
                {
                    label:'Hora',
                    field:'hora'
                }

            ],
            json_data:[],
            json_data_lista:[],
            json_fields_lista:[
                {
                    label:'DNI',
                    field:'dni'
                },
                {
                    label:'Apellido Paterno',
                    field:'paterno'
                },
                {
                    label:'Apellido Materno',
                    field:'materno'
                },
                {
                    label:'Nombres',
                    field:'nombres'
                },
                {
                    label:'Correo',
                    field:'correo'
                },
                {
                    label:'Telefono',
                    field:'telefono'
                },
                {
                    label:'Telefono Apoderado',
                    field:'apoderado'
                }

            ]



        };
    },
    methods: {
        guardar:function(){

            // $(".loader").show();
            var estado = [];
            for (const prop in this.estado) {
                estado.push(prop+'-'+ this.estado[prop]);
            }
            if(this.edit==1){
                axios
                .put("/intranet/coordinador-auxiliar/asistencia/estudiante/"+this.id,{
                        fecha: this.fecha,
                        grupo: this.grupo,
                        observacion: this.observacion,
                        estados: estado
                })
                .then((response) => {
                    $(".loader").hide();
                    // console.log(response.data.status);
                    if (response.data.status==true) {
                        this.getAsistenciaEstudiante();
                        toastr.success(response.data.message);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch((error) => {
                });
            }else{
                axios
                .post("/intranet/coordinador-auxiliar/asistencia/estudiante",{
                        fecha: this.fecha,
                        grupo: this.grupo,
                        observacion: this.observacion,
                        estados: estado
                })
                .then((response) => {
                    $(".loader").hide();
                    // console.log(response.data.status);
                    if (response.data.status==true) {
                        this.getAsistenciaEstudiante();
                        toastr.success(response.data.message);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch((error) => {
                });
            }


            this.observacion = '';
        },
        getAreas:function(){
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getTurnos:function(){
             axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        changeSede:function(){
            this.getGrupoAulas();
        },
        getGrupoAulas:function(){
           axios.get("/intranet/get-grupo-aulas-auxiliar",{
                params: {
                area: this.area,
                turno: this.turno,
                sede: this.sede,
                },
            }).then(response => {
                // this.grupo = response.data[0].id;
                this.grupos = response.data;
            });
        },
        changeArea:function(){
            this.getGrupoAulas();
            this.getTurnos();
        },
        changeTurno:function(){
            this.getGrupoAulas();
            // this.changeDocente();
        },
        getAsistenciaEstudiante:function(){
            var estados = [];
            axios.get("/intranet/get-asistencia-estudiante",{
                params: {
                    grupo: this.grupo,
                    fecha: this.fecha
                },
            }).then(response => {
                this.estudiantes = response.data.estudiantes;
                this.revisar = response.data.status;
                if(this.revisar){
                    this.edit = 1;
                    this.id = response.data.id;
                    this.getAsistenciaEstudianteExcel(this.id);
                    this.observacion = response.data.observacion;
                    for (const prop in this.estudiantes) {
                        estados[this.estudiantes[prop].id] = this.estudiantes[prop].estado;
                        // console.log(`obj.${prop} = ${obj[prop]}`);
                    }
                }else{
                    this.edit = 0;
                    this.id = null;
                    for (const prop in this.estudiantes) {
                        estados[this.estudiantes[prop].estudiante.id] = 1;
                        // console.log(`obj.${prop} = ${obj[prop]}`);
                    }
                }

            });
            this.estado = estados;
            // setTimeout(() => {

            // }, 1000);
        },
        changeGrupo:function(){
            this.observacion = '';
            this.estado = [];
            this.visible = true;
            this.urlLista = "/intranet/reporte/estudiante-lista/pdf/" + this.grupo;
            this.getAsistenciaEstudiante();
            this.getListaEstudianteExcel();
        },
        changeFecha:function(){
            this.observacion = '';
            this.estado = [];
            this.getAsistenciaEstudiante();
        },
        getEstado:function(estado){
            var palabra = ["light","Pendiente"];
            switch (estado) {
                case "1":
                    palabra[0] = "success"
                    palabra[1] = "Presente"
                    break;
                case "2":
                    palabra[0] = "warning"
                    palabra[1] = "Tarde"
                    break;
                case "3":
                    palabra[0] = "danger"
                    palabra[1] = "Falta"
                    break;

                default:
                    break;
            }
            return palabra;
        },
        exportarPDF:function(){
            this.url = "/intranet/reporte/estudiante-asistencia/pdf/"+this.id;
            $("#ModalReporte").modal("show");
        },
        getAsistenciaEstudianteExcel:function(id){
            axios.get("/intranet/get-asistencia-estudiante-excel/"+id).then(response => {
                // this.url = "/intranet/inscripcion/docentes/pdf/"+response.data;
                this.json_data = response.data.datos;
            });
        },
        getListaEstudianteExcel:function(){
            axios.get("/intranet/get-lista-estudiante-excel/"+this.grupo).then(response => {
                // this.url = "/intranet/inscripcion/docentes/pdf/"+response.data;
                this.json_data_lista = response.data.datos;
            });
        },
        getSedes:function(){
             axios.get("/intranet/get-sedes",{
                params: {
                    all: true
                },
            }).then(response => {
                this.sedes = response.data;
            });
        },


    },
    mounted: function () {
        this.getAreas();
        this.getTurnos();
        this.getGrupoAulas();
        // this.getDocente();
        this.getSedes();
    }

};
</script>
<style>

</style>
