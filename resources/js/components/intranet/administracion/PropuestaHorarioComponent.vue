<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sede">Sede</label>
                                    <select class="form-control" id="sede" v-model="sede" @change="changeSede">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
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
                                    <select class="form-control" id="turno" v-model="turno" @change="changeTurno">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{turno.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" id="cantidad" v-model="cantidad" class="form-control" @change="changeCantidad">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="grupo">Grupo</label>
                                    <select class="form-control" id="grupo" v-model="grupo" @change="changeGrupo">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{grupo.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <div class="row justify-content-md-center">
                                            <div class="col-12">
                                                <div v-if="errors && errors.carga" class="text-danger">{{ errors.carga[0] }}</div>
                                            </div>
                                            <table class="table table-bordered col table-sm">
                                                <thead class="table-primary">
                                                    <tr>
                                                    <th scope="col" colspan="2">Cursos</th>
                                                    <th scope="col">Documento</th>
                                                    <th scope="col">Docentes</th>
                                                    <th scope="col">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="carga in cargas" :key="carga.id">
                                                        <td v-bind:style="{background:carga.color}"></td>
                                                        <td>
                                                            <label class="form-check-label" v-bind:for="'Radio-' + curso.id">
                                                                {{carga.denominacion}}
                                                            </label>
                                                        </td>
                                                        <td>
                                                            {{carga.docente.documento}}
                                                        </td>
                                                        <td>
                                                            {{carga.docente.docente}}
                                                        </td>
                                                        <td>
                                                            <template v-if="carga.docente.docente">
                                                                <button @click="eliminar(carga.id,carga.docente.id)" class="btn btn-danger btn-sm" title="Enviar al final de cola">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </template>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- <div class="card-footer text-muted">
                                        <div class="row">
                                            <button class="btn btn-success btn-lg col" @click="submit">Guardar Horario</button>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row justify-content-md-center">
                                    <!-- <h6 class="text-primary col-12 text-center">Horario  {{turno.turno}}</h6><br> -->
                                    <!-- <div v-if="errors && errors.horario" class="text-danger">{{ errors.horario[0] }}</div> -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered horario">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th scope="col" width="120px">Hora</th>
                                                    <th scope="col">Lunes</th>
                                                    <th scope="col">Martes</th>
                                                    <th scope="col">Miercoles</th>
                                                    <th scope="col">Jueves</th>
                                                    <th scope="col">Viernes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="hora in horarios" :key="hora.id">
                                                    <td scope="col">{{hora.hora_inicio}}-{{hora.hora_fin}}</td>
                                                    <td scope="col" class="text-center" v-for="i in 5" :key="i">

                                                        <template v-for="val in hora.horario">
                                                            <template v-if="i.toString()==val.dia">
                                                                <label v-bind:style="{background:val.curso.color}" :key="val.id">
                                                                    <!-- <button @click="handleClick" class="btn btn-sm btn-danger p-0" :key="val.id">
                                                                        <i class="fa fa-times"></i>
                                                                    </button> -->
                                                                    {{val.curso.denominacion}}
                                                                </label>

                                                            </template>
                                                        </template>

                                                    </td>
                                                </tr>

                                            </tbody>
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
</template>

<script>

import 'vue-select/dist/vue-select.css';
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";

export default {

    data() {
        return {
            errors: {},
            url:'',
            area:'',
            sede:'',
            turno:'',
            grupo:'',
            docente:'',
            cargas:[],
            curso:'',
            cursoSelect:'',
            areas:[],
            sedes:[],
            turnos:[],
            grupos:[],
            docentes:[],
            disponibilidad:[],
            disponibilidadHorario:[],
            disponibilidadCursos:[],
            cursos:[],
            horarios:[],
            vacio:false,
            dia:[],
            color:'',
            cantidad:0,
            turnoGrupo:''
            // dia2:[],
            // dia3:[],
            // dia4:[],
            // dia5:[],

        };
    },
    methods: {
        // submit:function(){
        //     $(".loader").show();
        //     var datos = {
        //         curso: this.cursoSelect,
        //         horario:this.dia,
        //         area:this.area
        //     }
        //     axios.post("horario-inscripcion",datos)
        //     .then(response => {
        //     // this.data = response.data.data;
        //         if(response.data.status==true){

        //             toastr.success(response.data.message);
        //             this.getCurriculaDetalle();
        //             // this.changeDocente();
        //             this.cursoSelect = '';
        //             this.color = '';
        //             // this.docente = null;
        //             this.disponibilidad = [];
        //             this.dia = [];
        //             $(".checksH").css("background","none");
        //         }else{
        //             toastr.error(response.data.message);
        //         }
        //         $(".loader").hide();
        //     }).catch(error => {
        //             $(".loader").hide();
        //             if(error.response.status ===422){
        //                 this.errors = error.response.data.errors || {};
        //                 // toastr.error();
        //             }
        //         });
        // },
        getAreas:function(){
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getSedes:function(){
            axios.get("/intranet/get-sedes",{
                params: {
                    all: true,
                },
            }).then(response => {
                this.sedes = response.data;
            });
        },
        getTurnos:function(){
            axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        // getCurriculaDetalle:function(){
        //     axios.get("/intranet/get-curricula-detalle/"+this.area).then(response => {
        //         this.cursos = response.data.cursos;
        //         this.horarios = response.data.horarios;
        //     });
        // },
        changeArea:function(){
            // this.getCurriculaDetalle();
            this.generateGrupos();
            this.curso = '';
            this.color = '';
            this.dia = [];
            $(".checksH").css("background","none");
        },
        changeSede:function(){
            // this.getCurriculaDetalle();
            this.curso = '';
            this.color = '';
            this.dia = [];
            $(".checksH").css("background","none");
        },
        changeTurno:function(){
            this.generateGrupos();
            this.curso = '';
            this.color = '';
            this.dia = [];
            $(".checksH").css("background","none");
        },
        changeCantidad:function(){
            this.generateGrupos();
        },
        generateGrupos:function(){
            this.grupos = [];
            let turno_abr = 0;
            let area_abr = "";
            switch(this.area) {
                case 1:
                    area_abr = "B"
                    break;
                case 2:
                    area_abr = "I"
                    break;
                case 3:
                    area_abr = "S"
                    break;
                default:
                    area_abr = ""
            }
            switch(this.turno) {
                case 1:
                    turno_abr = 100
                    break;
                case 2:
                    turno_abr = 200
                    break;
                case 3:
                    turno_abr = 300
                    break;
                default:
                    turno_abr = 0
            }
            let contador = parseInt(this.cantidad) + parseInt(turno_abr);
            console.log(contador);
            let index = 1;
            for (let i = (turno_abr+1); i <= contador; i++) {
                // const element = array[index];
                let grupo = area_abr+'-'+i;
                this.grupos.push({id:index,denominacion:grupo})
                index +=1;
            }
        },
        changeGrupo:function(){
            this.generarHorario();
        },
        generarHorario:function(){
            axios.get("/intranet/get-propuesta-horario",{
                params: {
                    area: this.area,
                    turno: this.turno,
                    sede: this.sede,
                    grupo: this.grupo
                },
            }).then(response => {
                this.cargas = response.data.cargas;
                this.horarios = response.data.horarios;
            });

        },
        // checkHorario:function(dia,horario){
        //     var value = false;
        //     for (const val in horario.horario) {
        //         if(dia == horario.horario[val].dia){
        //             value = true;
        //         }
        //     }
        //     return value;
        // },
        // changeHorario:function(event){
        //     var elemento = event.path[1];
        //     // console.log(event)
        //     if (event.target.checked) {
        //         elemento.style.background = this.color;
        //     }else{
        //         elemento.style.background = 'white';
        //     }
        // },
        changeCurso:function(color){
            this.color = color;
            // this.$refs.checksH.style.background = 'white';
            // console.log(this.$refs.checksH);
            $(".checksH").css("background","none");
            this.dia = [];
        },
        // changeHorario:function(event){
        //     var elemento = event.path[1];
        //     // console.log(event)
        //     if (event.target.checked) {
        //         elemento.style.background = this.color;
        //     }else{
        //         elemento.style.background = 'white';
        //     }
        // },
        eliminar:function(detalle,docente){
            this.$confirm({
                title: 'Alerta',
                message: '¿Esta seguro de enviar al docente al final de la cola?',
                button: {
                    no: 'NO',
                    yes: 'SI'
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        var datos = {
                            docente: docente
                        }
                        axios.put("propuesta-horario/"+detalle,datos)
                        .then(response => {
                            if(response.data.status==true){

                                toastr.success(response.data.message);
                                this.generarHorario();
                                // this.changeDocente();
                                this.cursoSelect = '';
                                this.color = '';
                                // this.docente = null;
                                // this.disponibilidad = [];
                                this.dia = [];
                                $(".checksH").css("background","none");
                            }else{
                                toastr.error(response.data.message);
                            }
                            $(".loader").hide();
                        }).catch(error => {
                            $(".loader").hide();
                        });
                    }
                }
            })
        }

    },
    mounted: function () {
        this.getAreas();
        this.getSedes();
        this.getTurnos();


    }

};
</script>
<style>
    .disponibilidad th,.disponibilidad td{
        padding: 0px;
    }
    .horario td{
        position: relative;
        /* color:white; */
    }
    .horario td label{
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        height: 100%;
        font-weight: 500;
    }
</style>
