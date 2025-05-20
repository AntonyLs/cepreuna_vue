<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0"> Listado de cuadernillos por semana</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                            <div>


                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr style="background-color: #fff !important; color:#505050;text-align: center;">
                                        <th></th>
                                        <th colspan="16">Semanas</th>
                                    </tr>
                                    <tr style="background-color: #0D4172 !important; color:white">
                                        <th scope="col">Cursos</th>
                                        <th v-for="n in 16" :key="n">{{n}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in cursos" :key="index">
                                        <td scope="row"> {{item.curso}} </td>
                                        <td v-for="n in 16" :key="n" class="p-1" style="text-align: center;vertical-align: middle;">
                                            <template v-for="cuadernillo in item.cuadernillos" >
                                                <template v-if="cuadernillo.semana == n">
                                                    <a :key="cuadernillo.id"  id="" class="btn btn-link p-0" style="font-size:20px" :href="'/storage/documentos/'+cuadernillo.path" role="button" download>
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </template>
                                            </template>
                                            <!-- <w-cuadernilloe-pdf :curso="item.id" :semana="n"></w-cuadernilloe-pdf> -->
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

</template>

<script>
import 'vue-select/dist/vue-select.css';
import toastr from "toastr";
import $ from 'jquery';

export default {
    data:()=>({
        carga:[],
        fields:{
            id:''
        },
        cursos:[],
        errors:{}
    }),
    methods:{

        getCarga: function(){
            axios.get('cursos/get-carga')
            .then(function (response) {
                // console.log(response.data);
                this.carga = response.data;
            }.bind(this));
        },
        idCarga: function(id){

            this.fields.id = id;
        },
        getCursos: function(){
            axios.get('get-cursos-estudiante')
            .then(function (response) {
                this.cursos = response.data.cuadernillos;
            }.bind(this));
        }
    },
    mounted() {
        this.getCursos();
    }
}
</script>

<style>
</style>
