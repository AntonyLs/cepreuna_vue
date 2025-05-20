<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Sedes
                        <button class="btn btn-primary float-right" @click="nuevo">
                            <i class="fa fa-plus"></i> Nuevo
                        </button>
                    </header>
                    <div class="card-body">
                        <v-server-table
                            ref="table"
                            :columns="columns"
                            :options="options"
                            url="/intranet/configuracion/sede/lista/data"
                        >
                            <div slot="estado" slot-scope="props">
                                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
                            <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="actions" slot-scope="props">
                            <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                            <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="denominacion">Denominación</label>
                                        <input type="text" class="form-control" id="denominacion" v-model="fields.denominacion"/>
                                        <div v-if="errors && errors.denominacion" class="text-danger">
                                            {{ errors.denominacion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Estado</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                        <label>
                                            <input type="radio" v-model="fields.estado" :value="1" checked/>
                                            Activo
                                        </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                        <label>
                                            <input type="radio" v-model="fields.estado" :value="0"/>
                                                Inactivo
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.estado" class="text-danger">
                                        {{ errors.estado[0] }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="direccion">Direccion</label>
                                        <input type="text" class="form-control" id="direccion" v-model="fields.direccion"/>
                                        <div v-if="errors && errors.direccion" class="text-danger">
                                            {{ errors.direccion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                <div class="form-group col-12">
                                <label for="estado">Estado</label>
                                <select type="text" class="form-control" id="estado" v-model="fields.estado">
                                <option value="">--Seleccionar--</option>
                                <option value="0">Activo</option>
                                <option value="1" selected>Inactivo</option>
                                </select>
                                </div>
                                </div> -->

                                <div class="row">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="departamento">Departamento</label>
                                            <v-select
                                                :options="departamentos"
                                                :reduce="
                                                    (departamento) => departamento.codigo_departamento
                                                "
                                                v-model="fields.departamento"
                                                label="departamento"
                                                @input="changeDepartamento"
                                            ></v-select>
                                            <div v-if="errors && errors.departamento" class="text-danger" >
                                                {{ errors.departamento[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="provincia">Provincia</label>

                                            <v-select
                                                :disabled="disabledProvincia"
                                                :options="provincias"
                                                :reduce="(provincia) => provincia.codigo_provincia"
                                                v-model="fields.provincia"
                                                label="provincia"
                                                @input="changeProvincia"
                                            ></v-select>
                                            <div v-if="errors && errors.provincia" class="text-danger" >
                                                {{ errors.provincia[0] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="distrito">Distritito </label>

                                            <v-select
                                                :disabled="disabledDistrito"
                                                :options="distritos"
                                                :reduce="(distrito) => distrito.id"
                                                label="distrito"
                                                v-model="fields.distrito"
                                                @input="changeDistrito"
                                            ></v-select>
                                            <div v-if="errors && errors.ubigeo" class="text-danger">
                                                {{ errors.ubigeo[0] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" > Cerrar </button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
        edit:0,
        id:0,
        titulo: "",
        fields: {
            estado: "1" ,
            denominacion:"",
            direccion:"",
            departamento:null,
            provincia:null,
            distrito:null,
            ubigeo:0,
            },
        errors: {},
        departamentos: [],
        provincias: [],
        distritos: [],
        disabledProvincia: true,
        disabledDistrito: true,
        distrito: "",
        columns: [
            "id",
            "denominacion",
            "estado",
            "ubigeo.departamento",
            "ubigeo.provincia",
            "ubigeo.distrito",
            "actions",
        ],
        options: {
            headings: {
                id: "id",
                denominacion: "Denominacion",
                estado: "Estado",
                "ubigeo.departamento": "Departamento",
                "ubigeo.provincia": "Provincia",
                "ubigeo.distrito": "Distrito",
                actions: "Acciones",
            },
            sortable: ["id", "denominacion", "estado"],
            // filterable: ['correlativo','num_mat','paterno'],
            // customFilters: ['correlativo','num_mat']
            // filterByColumn:true
            },
        };
    },

    methods: {
        nuevo: function () {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nueva Sede";
            this.fields.estado = "1" ;
            this.fields.denominacion ="";
            this.fields.direccion ="";
            this.fields.departamento =null;
            this.fields.provincia =null;
            this.fields.distrito =null;
            this.fields.ubigeo = 0;
            this.disabledProvincia = true;
            this.disabledDistrito = true;
            $("#ModalFormulario").modal("show");
        },
        editar: function (id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Sede";
            axios.get("sede/"+id+"/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.direccion = response.data.direccion;
                this.fields.estado = response.data.estado;
                this.fields.departamento = response.data.ubigeo.codigo_departamento;
                this.fields.provincia = response.data.ubigeo.codigo_provincia;
                this.fields.distrito = response.data.ubigeo.id;
                this.fields.ubigeo = response.data.ubigeo.id;
                this.disabledProvincia = false;
                this.disabledDistrito = false;
                this.getProvincias();
                this.getDistritos();
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        submit: function () {
            this.errors = {};
            if(this.edit==0)
                axios
                .post("sede", this.fields)
                .then((response) => {
                    // $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalFormulario").modal("hide");
                        // window.location.replace(response.data.url);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch((error) => {
                    // $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            else
            {
                axios
                .put("sede/"+this.id, this.fields)
                .then((response) => {
                    // $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalFormulario").modal("hide");
                        // window.location.replace(response.data.url);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch((error) => {
                    // $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            }
            // console.log("hols");
        },
        changeDepartamento: function (departamento) {
            // this.fields.departamento = departamento

            if (departamento != null) {
                this.disabledProvincia = false;
                this.disabledDistrito = true;
            } else {
                this.disabledProvincia = true;
                this.disabledDistrito = true;
            }
            this.fields.provincia = null;
            this.distrito = null;

            this.getProvincias();
        },
        changeProvincia: function (provincia) {
            if (provincia != null) {
                this.disabledDistrito = false;
            } else {
                this.disabledDistrito = true;
            }
            this.distrito = null;
            this.getDistritos();
        },
        changeDistrito: function (distrito) {
            this.fields.ubigeo = distrito;
            console.log(distrito);
        },
        getDepartamentos: function () {
            axios.
            get("/inscripciones/get-departamentos").
            then(function (response){
                this.departamentos = response.data;
                }.
                bind(this)
            );
        },
        getProvincias: function () {
            axios
            .get("/inscripciones/get-provincias", {
                params: {
                codigo: this.fields.departamento,
                },
            })
            .then(
                function (response) {
                    this.provincias = response.data;
                }.
                bind(this)
            );
        },
        getDistritos: function () {
            axios
            .get("/inscripciones/get-distritos", {
                params: {
                    codigo: this.fields.provincia,
                },
            })
            .then(
                function (response) {
                this.distritos = response.data;
                }.bind(this)
            );
        },
    },
    mounted() {
    // console.log("Component mounted.");
    // this.getTipoDocumentos();
    // this.getPaises();
    this.getDepartamentos();
    // this.getColegios();
    // this.getSedes();
    // this.getAreas();
    // this.getParentescos();
    // this.getTurnos();
    },
};
</script>

<style>
</style>
