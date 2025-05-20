<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0"> Lista de temarios</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                            <div>


                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr style="background-color: #004d40 !important; color:white">
                                        <th scope="col">Cursos</th>
                                        <th> Archivo Pdf</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in cursos" :key="index">
                                        <td scope="row"> {{item.curso}} </td>
                                        <td class="p-1" style="text-align: center;vertical-align: middle;">
                                            <template v-for="temario in item.temarios" >
                                                <a :key="temario.id"  id="" class="btn btn-link p-0" style="font-size:20px" :href="'/storage/documentos/'+temario.path" role="button" download>
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
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
        submit: function () {
            // $(".loader").show();
            this.errors = {};
            axios
            .post("cursos/carga-update", this.fields)
            .then((response) => {

                console.log(response.data.status);
                if (response.data.status == true) {
                    // this.$refs.table.refresh();
                    toastr.success(response.data.message);
                    // $("#modelId").modal("hide");
                    this.getCarga();

                } else {
                    toastr.warning(response.data.message, "Aviso");
                }
                $(".loader").hide();
            })
            .catch((error) => {
                $(".loader").hide();
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
            });
        },
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
            axios.get('get-cursos-docente-temario')
            .then(function (response) {
                this.cursos = response.data.temarios;
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
