<template>
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"> Matricularse en Google Classroom </h5>
            </div>
            <div class="container">
                <!-- <br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert">
                            <strong>Atencion:</strong>
                            <p>Por el momento no es posible habilitar cursos en classroom, por favor ingrese mediante el link que se muestra en su modulo de cursos.</p>

                        </div>
                    </div>
                </div> -->
            </div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered ">
                            <tbody>
                                <tr>
                                <th scope="col">Curso</th>
                                <th scope="col">Estado</th>
                                </tr>
                                <tr v-for="(carga, index) in cargasAcademica" :key="carga.id">
                                    <td>
                                        {{carga.curso.denominacion}}
                                    </td>
                                    <td>
                                        <template v-if="carga.status === true">
                                            <span class="badge badge-success">Matriculado</span>
                                        </template>
                                        <template v-else-if="carga.status === false">
                                            <button class="btn btn-danger" @click="matricular(index)" :disabled="carga.loading==true?true:false">
                                                <i :class="carga.loading==true?'fas fa-spinner fa-pulse':'fab fa-google'"></i>
                                                Habilitar en Classroom
                                            </button>
                                        </template>
                                        <template v-else>
                                            espere....
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




</template>

<script>

import toastr from "toastr";
import $ from "jquery";

export default {

    props: ['carga'],
    data() {
        return {
            estudiante:[],
            hide:false,
            bar:0,
            cargasAcademica:[],
            ids:[]
        }
    },
    methods:{
        matricular: function(val){
            this.cargasAcademica[val].loading = true;
            this.hide = true;
            $(".loader").show();

             axios.put("/estudiante/matricular/"+this.cargasAcademica[val].idclassroom)
            .then(response => {
                this.cargasAcademica[val].status = '';
                if(response.data.status == true)
                {
                    toastr.success(response.data.message);
                }else{
                    toastr.error(response.data.message);
                }
                this.checkMatricula(this.cargasAcademica[val].idclassroom,val);
                this.cargasAcademica[val].loading = false;
                $(".loader").hide();
            }).catch(error => {
                //  setTimeout(() => {
                //     this.barra(100);
                //     window.location.href = "/estudiante/dashboard";
                // }, 8000);
                toastr.error("Curso matriculado en ClassRoom.");
                $(".loader").hide();
            });
        },
        barra:function(x,consulta = false){
            if(consulta){

            }
            for (var index = this.bar; index <= x; index++) {
                this.bar = index;
                console.log(index);

            }

        },
        getCargaAcademica:function(){
            axios.get("/estudiante/get-carga-academica").then(response => {
                this.cargasAcademica = response.data.carga;
            });
        },
        checkMatricula:function(carga,id){
            var status = false;
            axios.get("/estudiante/check-matricula/"+carga).then(response => {
                this.cargasAcademica[id].status = response.data.status;
            });

        }


    },
    mounted() {
        // console.log(this.carga);
        this.getCargaAcademica();

        // var acade = JSON.parse(this.carga);
        // console.log(acade);
        // this.cargasAcademica = acade;
        // for (const val in acade) {
        //     console.log(acade[val]);
        //     axios.get("/estudiante/check-matricula/"+acade[val].idclassroom).then(response => {

        //         acade[val].status = response.data.status;
        //     });

        // }
        // console.log(this.cargasAcademica);
        setTimeout(() => {
                console.log(this.cargasAcademica);

            for (const val in this.cargasAcademica) {
                console.log(val);
                this.checkMatricula(this.cargasAcademica[val].idclassroom,val);

            }
        }, 4000);
    }
};
</script>
