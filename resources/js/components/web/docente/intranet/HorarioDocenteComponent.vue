<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <h4>Horario</h4>
                    </header>
                    <div class="card-body container">

                        <div class="row justify-content-md-center" v-for="horario in disponibilidadHorario" :key="horario.id">
                            <h6 class="text-primary col-12 text-center">Turno  {{horario.turno}}</h6><br>
                            <div class="table-responsive col-md-12">
                                <table class="table table-bordered text-center">
                                    <thead >
                                        <tr style="background:#003e37">
                                            <th width="150px">Hora</th>
                                            <th >Lunes</th>
                                            <th >Martes</th>
                                            <th >Miercoles</th>
                                            <th >Jueves</th>
                                            <th >Viernes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="hora in horario.disponibilidad" :key="hora.id">
                                            <th  width="150px">{{hora.hora_inicio}}-{{hora.hora_fin}}</th>
                                            <td v-for="i in 5" :key="i">

                                                <template v-for="val in hora.horario">
                                                    <template v-if="i.toString()==val.dia">
                                                        <!-- <h5 class="my-0"> -->
                                                            <span style="font-size:14px" class="badge" v-bind:style="{background:val.curso.color}"><span class="badge badge-dark">{{val.grupo}}</span> {{val.curso.denominacion}}</span>
                                                        <!-- </h5> -->
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

            disponibilidadHorario:[],


        };
    },
    methods: {



        getHorario:function(){
            axios.get("/docente/horario/get-horario").then(response => {
                // this.grupo = response.data[0].id;
                this.disponibilidadHorario = response.data.horario;
                // this.disponibilidad = response.data.disponibilidad;
                // this.disponibilidadCursos = response.data.cursos;
            });
        },


    },
    mounted: function () {
        this.getHorario();
        // this.getTurnos();
        // this.getGrupoAulas();
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
