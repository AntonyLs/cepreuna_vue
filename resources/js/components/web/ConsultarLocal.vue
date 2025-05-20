<template>
    <div class="container">
        <div class="col-xs-12 text-center">
            <h4>Consultar Local</h4>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-10 offset-md-1 col-xs-12 contain">
                <div class="col-md-10 offset-md-1 col-xs-12">
                    <div class="form-row">
                        <label class="col-md-6 col-xs-12 control-label"><b>Ingrese su número de documento (DNI):</b> </label>
                        <div class="col-md-3 col-xs-12">
                            <input type="text" class="form-control" v-model="dni" />
                        </div>
                        <div class="col-md-3 col-xs-12 float-left">
                            <button @click="getLocal()" class="btn btn-primary">Consultar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="row" v-if="nombres">
            <div class="col-md-10 offset-md-1 col-xs-12" style="padding:0" id="view">
                <div class="card text-center">
                    <div class="card-header">
                        <b>Resultado</b>
                    </div>
                    <div class="card-body">
                        <table style="width: 100%">
                            <tbody>
                                <tr>
                                    <td class="text-right"><b>Apellidos y Nombres: </b></td>
                                    <td class="text-left" id="solicitante">
                                        <b class="text-success">{{ nombres }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Area: </b></td>
                                    <td class="text-left">
                                        <b class="text-success">{{ area }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Turno: </b></td>
                                    <td class="text-left">
                                        <b class="text-success">{{ turno }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Grupo: </b></td>
                                    <td class="text-left">
                                        <b class="text-success">{{ grupo }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Sede: </b></td>
                                    <td class="text-left">
                                        <b class="text-success">{{ sede }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Local: </b></td>
                                    <td class="text-left" id="tarea">
                                        <b class="text-success"> {{ local }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Dirección: </b></td>
                                    <td class="text-left" id="estado">
                                        <b class="text-success"> {{ direccion }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right"><b>Aula: </b></td>
                                    <td class="text-left" id="estado">
                                        <b class="text-success"> {{ aula }}</b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="col-md-10 offset-md-1 col-xs-12" v-if="foto">
                            <img :src="foto" class="img-fluid" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-10 offset-md-1 col-xs-12" style="padding:0" v-if="error">
            <div id="error" class="alert alert-danger" role="alert">{{ error }}</div>
        </div>
    </div>
</template>

<script>
import $ from "jquery";
export default {
    data() {
        return {
            fields: {},
            errors: {},
            result: {
                pago: []
            },
            dni: null,
            nombres: "",
            sede: "",
            local: "",
            aula: "",
            direccion: "",
            error: "",
            foto: "",
            area: "",
            turno: "",
            grupo: ""
        };
    },

    methods: {
        getLocal() {
            axios
                .get("get-local-estudiante/" + this.dni)
                .then(response => {
                    console.log(response.data);
                    this.error = "";
                    if (response.data.status) {
                        this.nombres = response.data.result.nombres;
                        this.sede = response.data.result.sede;
                        this.local = response.data.result.local;
                        this.aula = response.data.result.aula;
                        this.direccion = response.data.result.direccion;
                        this.error = response.data.result.error;
                        this.foto = "/images/locales/" + response.data.result.foto;
                        this.area = response.data.result.area;
                        this.turno = response.data.result.turno;
                        this.grupo = response.data.result.grupo;
                    } else {
                        this.error = "No se encontraron resultados";
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        }
    },
    mounted() {
        console.log("Component mounted.");
        // console.log(this.tarifa);
        // this.fields.documento = this.documento;
    }
};
</script>
