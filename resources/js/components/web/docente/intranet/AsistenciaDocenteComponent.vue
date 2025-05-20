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
                        <vue-cal class="vuecal--green-theme" locale="es"
                            :disable-views="['years', 'year']"
                            :time-from="7 * 60"
                            :time-to="22 * 60"
                            :events="events"
                            :on-event-click="onEventClick"
                        />
                    </div>

                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="modal-obs" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white" :class="detalles.estado == 1 ? 'bg-success': detalles.estado == 2 ? 'bg-warning': 'bg-danger'">
                        <h5 class="modal-title p-0">Detalles de asistencia <span v-if="detalles.tipo == 2" class="badge badge-primary">Remplazo</span></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">

                            <!-- {{ observacion }} -->
                            <div class="row">
                                <div class="col-md-12">
                                    <b for="curso"> Curso y Grupo </b>
                                    <p>{{ detalles.title }}</p>
                                    <b for="curso"> Fecha </b>
                                    <p>{{ detalles.fecha_asistencia }}</p>
                                    <b for="curso"> Horario </b>
                                    <p>{{ detalles.hora_inicio }} - {{ detalles.hora_fin }}</p>
                                    <b for="curso"> Observación </b>
                                    <div class="alert alert-secondary" role="alert">
                                        <p>{{ detalles.obs }}</p>
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
        onEventClick (event, e) {
            this.selectedEvent = event
            this.showDialog = true
            // if (event['obs'] != null) {
                this.detalles = event
                $("#modal-obs").modal("show");
                console.log(this.detalles);
            // }

            // Prevent navigating to narrower view (default vue-cal behavior).
            e.stopPropagation()
        }
    },
    mounted() {
        this.getAsistencias()
    }
}
</script>

<style>
</style>
