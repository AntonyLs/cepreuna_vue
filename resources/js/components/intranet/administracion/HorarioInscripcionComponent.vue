<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <select class="form-control" id="area" v-model="area" @change="changeArea">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="area in areas" :value="area.id" :key="area.id">{{area.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Horario</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row justify-content-md-center">
                                            <div class="col-12">
                                                <div v-if="errors && errors.carga" class="text-danger">{{ errors.carga[0] }}</div>
                                            </div>
                                            <table class="table table-bordered col table-sm">
                                                <thead class="table-primary">
                                                    <tr>
                                                    <th scope="col" colspan="2">Cursos</th>
                                                    <th scope="col">Marcar</th>
                                                    <th scope="col">Opciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="curso in cursos" :key="curso.id">
                                                        <td v-bind:style="{background:curso.curso.color}"></td>
                                                        <td>
                                                            <label class="form-check-label" v-bind:for="'Radio-' + curso.id">
                                                                {{curso.curso.denominacion}}
                                                            </label>
                                                        </td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input
                                                                    type="radio"
                                                                    v-bind:id="'Radio-' + curso.id" name="customRadio"
                                                                    class="form-check-input" :value="curso.id"
                                                                    v-model="cursoSelect"
                                                                    checked="false"
                                                                    :disabled="curso.horario_inscripcion==='0'?false:true"
                                                                    @change="changeCurso(curso.curso.color)"
                                                                    >
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <template v-if="curso.horario_inscripcion=='1'">
                                                                <button @click="eliminar(curso.id)" class="btn btn-danger btn-sm" title="desmatricular">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </template>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="card-footer text-muted">
                                        <div class="row">
                                            <button class="btn btn-success btn-lg col" @click="submit">Guardar Horario</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row justify-content-md-center" v-for="turno in horarios">
                                    <h6 class="text-primary col-12 text-center">Horario  {{turno.turno}}</h6><br>
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
                                                <tr v-for="hora in turno.plantilla" :key="hora.id">
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
                                                        <template v-if="!checkHorario(i.toString(),hora)">

                                                            <label
                                                                :for="i+'-'+hora.id+'-'+cursoSelect"
                                                                class="checksH"
                                                            >
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    v-model="dia"
                                                                    :value="i+'-'+hora.id+'-'+cursoSelect"
                                                                    :id="i+'-'+hora.id+'-'+cursoSelect"
                                                                    @change="changeHorario($event)"
                                                                >
                                                            </label>
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
            turno:'',
            grupo:'',
            docente:'',
            cargaAcademica:'',
            curso:'',
            cursoSelect:'',
            areas:[],
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
            turnoGrupo:''
            // dia2:[],
            // dia3:[],
            // dia4:[],
            // dia5:[],

        };
    },
    methods: {
        submit:function(){
            $(".loader").show();
            var datos = {
                curso: this.cursoSelect,
                horario:this.dia,
                area:this.area
            }
            axios.post("horario-inscripcion",datos)
            .then(response => {
            // this.data = response.data.data;
                if(response.data.status==true){

                    toastr.success(response.data.message);
                    this.getCurriculaDetalle();
                    // this.changeDocente();
                    this.cursoSelect = '';
                    this.color = '';
                    // this.docente = null;
                    this.disponibilidad = [];
                    this.dia = [];
                    $(".checksH").css("background","none");
                }else{
                    toastr.error(response.data.message);
                }
                $(".loader").hide();
            }).catch(error => {
                    $(".loader").hide();
                    if(error.response.status ===422){
                        this.errors = error.response.data.errors || {};
                        // toastr.error();
                    }
                });
        },
        getAreas:function(){
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getCurriculaDetalle:function(){
            axios.get("/intranet/get-curricula-detalle/"+this.area).then(response => {
                this.cursos = response.data.cursos;
                this.horarios = response.data.horarios;
            });
        },
        changeArea:function(){
            this.getCurriculaDetalle();
            this.curso = '';
            this.color = '';
            this.dia = [];
            $(".checksH").css("background","none");
        },
        checkHorario:function(dia,horario){
            var value = false;
            for (const val in horario.horario) {
                if(dia == horario.horario[val].dia){
                    value = true;
                }
            }
            return value;
        },
        changeHorario:function(event){
            var elemento = event.path[1];
            // console.log(event)
            if (event.target.checked) {
                elemento.style.background = this.color;
            }else{
                elemento.style.background = 'white';
            }
        },
        changeCurso:function(color){
            this.color = color;
            // this.$refs.checksH.style.background = 'white';
            // console.log(this.$refs.checksH);
            $(".checksH").css("background","none");
            this.dia = [];
        },
        changeHorario:function(event){
            var elemento = event.path[1];
            // console.log(event)
            if (event.target.checked) {
                elemento.style.background = this.color;
            }else{
                elemento.style.background = 'white';
            }
        },
        eliminar:function(curso){
            this.$confirm({
                title: 'Alerta',
                message: '¿Esta seguro de eliminar Horario?',
                button: {
                    no: 'NO',
                    yes: 'SI'
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        var datos = {
                            area: this.area
                        }
                        axios.put("horario-inscripcion/"+curso,datos)
                        .then(response => {
                            if(response.data.status==true){

                                toastr.success(response.data.message);
                                this.getCurriculaDetalle();
                                // this.changeDocente();
                                this.cursoSelect = '';
                                this.color = '';
                                // this.docente = null;
                                this.disponibilidad = [];
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
