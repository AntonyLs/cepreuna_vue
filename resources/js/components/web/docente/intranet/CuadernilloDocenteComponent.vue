<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Listado de cuadernillos por semana</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <span style="font-size:20px; color:#002448"><i class="fas fa-file-pdf"></i><b style="font-size:12px"> Cuadernillo Docente</b></span>
                        <span style="font-size:20px; color:#d50000;margin-left:10px"><i class="fas fa-file-pdf"></i> <b style="font-size:12px"> Cuadernillo Estudiante</b></span>
                        <div>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr style="background-color: #fff !important; color:#505050;text-align: center;">
                                        <th></th>
                                        <th colspan="16">Semanas</th>
                                    </tr>
                                    <tr style="background-color: #004d40 !important; color:white">
                                        <th scope="col">Cursos</th>
                                        <th v-for="n in 16" :key="n">{{ n }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in cursos" :key="index">
                                        <td scope="row">{{ item.curso }}</td>
                                        <td v-for="n in 16" :key="n" class="p-1" style="text-align: center;vertical-align: middle;">
                                            <template v-for="cuadernillo in item.cuadernillos">
                                                <template v-if="cuadernillo.semana == n">
                                                    <a
                                                        :key="cuadernillo.id"
                                                        id=""
                                                        class="btn btn-link p-0"
                                                        style="font-size:20px"
                                                        :href="'/storage/documentos/' + cuadernillo.path"
                                                        role="button"
                                                        download
                                                    >
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                </template>
                                            </template>
                                            <template v-for="cuadernilloEstudiante in item.cuadernillosEstudiante">
                                                <template v-if="cuadernilloEstudiante.semana == n">
                                                    <a
                                                        :key="cuadernilloEstudiante.id"
                                                        id=""
                                                        class="btn btn-link p-0"
                                                        style="font-size:20px; color:#d50000"
                                                        :href="'/storage/documentos/' + cuadernilloEstudiante.path"
                                                        role="button"
                                                        download
                                                    >
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
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
</template>

<script>
import "vue-select/dist/vue-select.css";
import toastr from "toastr";
import $ from "jquery";

export default {
    data: () => ({
        carga: [],
        fields: {
            id: ""
        },
        cursos: [],
        cursosEstudiante: [],
        errors: {}
    }),
    methods: {
        submit: function() {
            // $(".loader").show();
            this.errors = {};
            axios
                .post("cursos/carga-update", this.fields)
                .then(response => {
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
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        getCarga: function() {
            axios.get("cursos/get-carga").then(
                function(response) {
                    // console.log(response.data);
                    this.carga = response.data;
                }.bind(this)
            );
        },
        idCarga: function(id) {
            this.fields.id = id;
        },
        getCursos: function() {
            axios.get("get-cursos-docente").then(
                function(response) {
                    console.log(response.data);
                    this.cursos = response.data.cuadernillos;
                    this.cursosEstudiante = response.data.cuadernillosEstudiante;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getCursos();
    }
};
</script>

<style></style>
