<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Asignacion de temas por curso
                        <button class="btn btn-primary float-right" @click="nuevo">
                            <i class="fa fa-plus"></i> Nuevo
                        </button>
                    </header>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Filtrar por Curso</label>
                                        <select class="form-control" v-model="curso" @change="changeFiltroCurso">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="curso in filtroCursos" :value="curso.id" :key="curso.id">{{curso.curso}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table
                            ref="table"
                            :columns="columns"
                            :options="options"
                            url="/docente/cursos/sesion/lista/data"
                        >

                            <div slot="actions" slot-scope="props">
                            <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    <i class="fas fa-edit"></i> Editar
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
                                        <label for="cursos">Curso</label>
                                        <v-select v-model="fields.carga_academica" :options="cursos" :reduce="curso => curso.id"  label="curso"></v-select>
                                        <div v-if="errors && errors.carga_academica" class="text-danger">{{ errors.carga_academica[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fecha">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" v-model="fields.fecha"/>
                                        <div v-if="errors && errors.fecha" class="text-danger">
                                            {{ errors.fecha[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="">Tema</label>
                                        <textarea v-model="fields.tema" class="form-control" name="" id="" rows="3"></textarea>
                                        <div v-if="errors && errors.tema" class="text-danger">
                                            {{ errors.tema[0] }}
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
            fecha: "" ,
            tema:"",
            carga_academica:"",
            },
        errors: {},
        cursos: [],
        filtroCursos: [],
        curso:'',
        columns: [
            "fecha",
            "curso",
            "grupo",
            "tema",
            "actions",
        ],
        options: {
            headings: {
                fecha: "Fecha",
                curso: "Curso",
                grupo: "Grupo",
                tema: "Tema",
                actions: "Acciones",
            },
            sortable: ["fecha", "curso","grupo"],
            filterable: [],
            customFilters: [],
            filterByColumn:true
            },
        };
    },

    methods: {
        nuevo: function () {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Agregar Nuevo Tema";
            this.fields.fecha = "";
            this.fields.tema = "" ;
            this.fields.carga_academica = "";
            this.selectAdjunto = 'Selecione';
            $("#ModalFormulario").modal("show");
        },
        editar: function (id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Tema";
            axios.get("sesion/"+id+"/edit").then(response => {
                console.log(response.data);
                this.fields.fecha = response.data.fecha;
                this.fields.tema = response.data.tema;
                this.fields.carga_academica = response.data.carga_academicas_id;
            });
            this.fields.file ="";
            this.selectAdjunto = 'Selecione';
            $("#ModalFormulario").modal("show");
        },
        filesChange(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjunto = 'Selecione';
            }
            else{
                this.selectAdjunto = file.name;
                this.fields.file = file;
            }

        },
        submit: function () {
            $(".loader").show();
            this.errors = {};

            if(this.edit==0){
                axios
                .post("store-sesion", this.fields)
                .then((response) => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalFormulario").modal("hide");
                    } else {
                        console.log(response.data.error)
                        if(response.data.error.errorInfo !== undefined)
                        {
                            if (response.data.error.errorInfo[1] = 1062) {
                                toastr.warning('Registro Duplicado', "Aviso");
                            }
                        }else
                        {
                            toastr.warning(response.data.message, "Aviso");
                        }

                    }
                })
                .catch((error) => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            }
            else
            {
                axios
                .put("update-sesion/"+this.id, this.fields)
                .then((response) => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalFormulario").modal("hide");
                    } else {
                        console.log(response.data.error)
                        if(response.data.error.errorInfo !== undefined)
                        {
                            if (response.data.error.errorInfo[1] = 1062) {
                                toastr.warning('Registro Duplicado', "Aviso");
                            }
                        }else
                        {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    }
                })
                .catch((error) => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
            }
            // console.log("hols");
        },
        getCursos: function (area) {
            axios.
            get("get-cursos-carga").
            then(function (response){
                console.log(response)
                this.filtroCursos = response.data;
                this.cursos = response.data;
                }.
                bind(this)
            );
        },
        changeFiltroCurso: function(){
            this.$refs.table.setCustomFilters({"curso":this.curso});
        },


    },
    mounted() {

    this.getCursos();

    },
};
</script>

<style>
</style>
