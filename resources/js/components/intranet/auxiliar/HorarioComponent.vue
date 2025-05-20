<template>
    <div>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="turno">Turno</label>
                                    <select class="form-control" v-model="turno" @change="changeTurno">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{turno.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sede">Sede</label>
                                    <select class="form-control" v-model="sede" @change="changeSede">
                                        <option value="">--Seleccionar--</option>
                                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </header>
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-md-4">
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card text-center">
                                    <div class="card-header">
                                        <h5>Horario</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row justify-content-md-center">
                                            <div class="col-md-8">
                                                <table class="table table-bordered col table-sm">
                                                    <thead class="table-primary">
                                                        <tr>
                                                        <th scope="col" colspan="2">Cursos</th>
                                                        <th scope="col">Docente</th>
                                                        <th scope="col">Tipo</th>
                                                        <th scope="col">Datos</th>
                                                        <th scope="col">Enlace</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="carga in cargas" :key="carga.id">
                                                            <td v-bind:style="{background:carga.curso.color}"></td>
                                                            <td>
                                                                <label class="form-check-label" v-bind:for="'Radio-' + carga.id">
                                                                    {{carga.curso.denominacion}}
                                                                </label>
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.docente">
                                                                    {{carga.docente.paterno}} {{carga.docente.materno}} {{carga.docente.nombres}} {{carga.docente.id}}
                                                                </template>
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.docente!==null">
                                                                    <template v-if="carga.tipo=='1'">
                                                                        Principal
                                                                    </template>
                                                                    <template v-else-if="carga.tipo=='2'">
                                                                        Suplente
                                                                    </template>
                                                                </template>
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.docentes_id != null" >
                                                                    <i-datos-docente :datosDocente="carga.docente" :usuario="carga.usuario"></i-datos-docente>
                                                                </template>
                                                                <!-- <button :id="carga.docente.id" class="btn btn-sm btn-primary">Datos</button> -->
                                                            </td>
                                                            <td>
                                                                <template v-if="carga.link!=null">
                                                                <a
                                                                    style="font-size:12px"
                                                                    :href="carga.link"
                                                                    :title="carga.link">
                                                                    {{carga.link.substr(0,30)+'...'}}
                                                                </a>
                                                                </template>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="row justify-content-md-center">
                                            <h6 class="text-primary col-12 text-center">Horario  {{turnoGrupo}}</h6><br>
                                            <div v-if="errors && errors.horario" class="text-danger">{{ errors.horario[0] }}</div>
                                            <table class="table table-bordered horario">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th scope="col">Hora</th>
                                                        <th scope="col">Lunes</th>
                                                        <th scope="col">Martes</th>
                                                        <th scope="col">Miercoles</th>
                                                        <th scope="col">Jueves</th>
                                                        <th scope="col">Viernes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="hora in horarios" :key="hora.id">
                                                        <th scope="col">{{hora.hora_inicio}} - {{hora.hora_fin}}</th>
                                                        <td scope="col" v-for="i in 5" :key="i">

                                                            <template v-for="val in hora.horario">
                                                                <template v-if="i.toString()==val.dia">
                                                                    <label v-bind:style="{background:val.curso.color}">{{val.curso.denominacion}}</label>
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
            sede:'',
            docente:'',
            cargaAcademica:'',
            curso:'',
            areas:[],
            turnos:[],
            grupos:[],
            sedes:[],
            disponibilidad:[],
            disponibilidadHorario:[],
            disponibilidadCursos:[],
            cargas:[],
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
        getGrupoAulas:function(){
            axios.get("/intranet/get-grupo-aulas-auxiliar",{
                params: {
                area: this.area,
                turno: this.turno,
                sede: this.sede
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
        changeSede:function(){
            this.getGrupoAulas();
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
        getCargaAcademica:function(){
                axios.get("/intranet/get-carga-academica",{
                params: {
                    grupo: this.grupo,
                    estado:true
                },
            }).then(response => {
                this.cargas = response.data.cargaAcademica;
                this.horarios = response.data.horario;
                this.turnoGrupo = response.data.turno.denominacion;
            });
        },
        changeGrupo:function(){
            this.getCargaAcademica();
            this.curso = '';
            this.color = '';
            this.dia = [];
            $(".checksH").css("background","none");
        },
        changeCurso:function(color){
            this.color = color;
            // this.$refs.checksH.style.background = 'white';
            // console.log(this.$refs.checksH);
            $(".checksH").css("background","none");
            this.dia = [];
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
        changeHorario:function(event){
            var elemento = event.path[1];
            // console.log(event)
            if (event.target.checked) {
                elemento.style.background = this.color;
            }else{
                elemento.style.background = 'white';
            }
        }

    },
    mounted: function () {
        this.getAreas();
        // this.getTurnos();
        this.getSedes();
        this.getGrupoAulas();
        // this.getDocente();


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
