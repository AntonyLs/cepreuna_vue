<template>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"> Asistencia</h5>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <span class="badge badge-success">P</span> Presente
                        <span class="badge badge-warning text-white">T</span> Tarde
                        <span class="badge badge-danger">F</span> Falta
                    </div>
                </div>
            </div>
            <br>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <vue-cal class="vuecal--blue-theme" locale="es"
                            :disable-views="['years', 'year']"
                            :time-from="(inicio * 60)"
                            :time-to="(fin * 60)+60"
                            :events="events"
                        />
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
        errors:{},
        events: [],
        detalles:'',
        inicio:0,
        fin:0
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
            axios.get('get-cursos-docente')
            .then(function (response) {
                this.cursos = response.data.cuadernillos;
            }.bind(this));
        },
        getAsistencias(){
            axios.get('asistencia/get-asistencias')
            .then(function (response) {
                this.events = response.data.asistencias;
            }.bind(this));
        },
        getRangoFechas(){
            axios.get('asistencia/get-rango-fechas')
            .then(function (response) {
                this.inicio = (response.data.inicio).split(':')[0];
                this.fin = (response.data.fin).split(':')[0];
                console.log(response);
            }.bind(this));
        },
    },
    mounted() {
        this.getAsistencias()
        this.getRangoFechas();
    }
}
</script>

<style>
</style>
