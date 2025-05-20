<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Sincronización de Habilitación de Encuesta Docente
                        <button class="btn btn-success float-right" @click="activar(1)" :disabled="disabledButton"><i class="fas fa-sync-alt"></i> Habilitar encuesta</button>
                        <button class="btn btn-danger float-right mx-1" @click="activar(0)" :disabled="disabledButton"><i class="fas fa-sync-alt"></i> Desactivar encuesta</button>
                    </header>
                    <!-- <div class="card-body">
                        <div class="progress" style="height: 30px;">
                            <div
                                class="progress-bar progress-bar-striped progress-bar-animated bg-dark"
                                role="progressbar"
                                :aria-valuenow="porcentaje"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                :style="`width: ${porcentaje}%`"
                            >
                                <h5 class="mb-0">{{ porcentaje }}%</h5>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card" style="height:250px">
                                    <iframe :src="url" width="100%" height="250" id="recarga_cron">
                                        Not supported
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";
import "vue-select/dist/vue-select.css";

export default {
    // props:[],
    data() {
        return {
            ///table//
            url: "",
            disabledButton: false,
            porcentaje: 0
        };
    },

    methods: {
        activar: function(e) {
            axios.post("estado-encuesta-cambiar", { estado: e }).then(response => {
                // console.log(response);
            });
            // this.disabledButton = true;
        }
    },

    mounted() {
        /* setInterval(() => {
            this.control();
        }, 1000); */
    }
};
</script>

<style></style>
