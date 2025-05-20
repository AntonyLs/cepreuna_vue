<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Sincronizacion de Correos de Electronicos
                        <button class="btn btn-success float-right" @click="sincronizar" :disabled="disabledButton"><i class="fas fa-sync-alt"></i> Sincronizar Registros</button>
                    </header>
                    <div class="card-body">
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
                    </div>
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
            disabledButton: true,
            porcentaje: 0
        };
    },

    methods: {
        sincronizar: function() {
            axios.get("sincronizar-correo/").then(response => {
                // console.log(response);
            });
            this.disabledButton = true;
        },
        control: function() {
            axios.get("control-correo/").then(response => {
                if (response.data.status) {
                    this.url = "/storage/crons/" + response.data.query.url;
                    this.disabledButton = true;

                    this.porcentaje = Math.round((response.data.query.ejecutado_registros * 100) / response.data.query.total_registros, 0);
                } else {
                    this.disabledButton = false;
                }
            });
            document.getElementById("recarga_cron").contentWindow.location.reload();
            let scroll = document.getElementById("recarga_cron");
            scroll.scrollTop = scroll.scrollHeight - scroll.clientHeight;
        }
    },

    mounted() {
        setInterval(() => {
            this.control();
        }, 1000);
    }
};
</script>

<style></style>
