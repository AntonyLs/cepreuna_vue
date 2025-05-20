<template>
    <div>
        <!-- <excel-export
            class="btn btn-success"
            :data="json_data"
            :columns="json_fields"
            :filename="'filename'"
            :sheetname="'sheetname'"
            >
            Descargar excel
        </excel-export> -->
        <v-server-table
            ref="table"
            :columns="columns"
            :options="options"
            url='/intranet/administracion/docente/lista/data'
        >
            <div slot="actions" slot-scope="props">
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="editarDocente(props.row.id)">
                    <i class="fas fa-file-signature"></i>
                </button>
            </div>
            <div slot="apto" slot-scope="props">
                <template v-if="props.row.apto == '1'">
                    <span class="badge badge-success">SI</span>
                </template>
                <template v-else>
                    <span class="badge badge-danger">NO</span>
                </template>
            <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
            </div>
        </v-server-table>
        <!-- Modal  Editar de Inscripcion-->
        <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Editar Docente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-8 col-xs-12">
                                        <div class="form-group">
                                            <label for="tipo documento">Tipo Documento</label>
                                            <v-select v-model="fields.tipo_documento" :options="tipoDocumentos" :reduce="tipo_documento => tipo_documento.id" label="denominacion"></v-select>
                                            <div v-if="errors && errors.tipo_documento" class="text-danger">{{ errors.tipo_documento[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="nro_documento">Número de Documento</label>
                                            <input type="text" class="form-control" name="nro_documento" id="nro_documento" v-model="fields.nro_documento" />
                                            <div v-if="errors && errors.nro_documento" class="text-danger">{{ errors.nro_documento[0] }}</div>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="nombres">Nombres</label>
                                            <input type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" />
                                            <div v-if="errors && errors.nombres" class="text-danger">{{ errors.nombres[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="paterno">Apellido Paterno</label>
                                            <input type="text" class="form-control" name="paterno" id="paterno" v-model="fields.paterno" />
                                            <div v-if="errors && errors.paterno" class="text-danger">{{ errors.paterno[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="materno">Apellido Materno</label>
                                            <input type="text" class="form-control" name="materno" id="materno" v-model="fields.materno" />
                                            <div v-if="errors && errors.materno" class="text-danger">{{ errors.materno[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 col-xs-12">
                                        <div class="form-group">
                                            <label for="condicion">Condición</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" v-model="fields.condicion" name="condicion" id="condicion" value="1" @change="codigoVaciar">
                                                        Particular
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                        <input type="radio" class="form-check-input" v-model="fields.condicion" name="condicion" id="condicion" value="2" @change="codigoVaciar">
                                                        Unap
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="codigo_unap">Código Unap</label>
                                            <input type="codigo_unap" class="form-control" name="codigo_unap" id="codigo_unap" v-model="fields.codigo_unap"  :disabled="fields.condicion==1?true:false"/>
                                            <div v-if="errors && errors.codigo_unap" class="text-danger">{{ errors.codigo_unap[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="grado">Nivel Académico</label>
                                            <v-select v-model="fields.grado" :options="gradosAcademicos" :reduce="grado => grado.id" label="denominacion"></v-select>
                                            <div v-if="errors && errors.grado" class="text-danger">{{ errors.grado[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-xs-12">
                                        <div class="form-group">
                                            <label for="carrera">Carrera</label>
                                            <!-- <input type="text" class="form-control" name="pais" id="pais" v-model="fields.pais" /> -->
                                            <v-select v-model="fields.programa" :options="programas" :reduce="programa => programa.id" label="denominacion"></v-select>
                                            <div v-if="errors && errors.programa" class="text-danger">{{ errors.programa[0] }}</div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" name="email" id="email" v-model="fields.email" />
                                            <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="celular">Celular</label>
                                            <input type="text" class="form-control" name="celular" id="celular" v-model="fields.celular" />
                                            <div v-if="errors && errors.celular" class="text-danger">{{ errors.celular[0] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group">
                                            <label for="ruc">Ruc</label>
                                            <input type="text" class="form-control" name="ruc" id="ruc" v-model="fields.ruc" />
                                            <div v-if="errors && errors.ruc" class="text-danger">{{ errors.ruc[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 col-xs-12">
                                        <div class="form-group">
                                            <label for="cci">CCI</label>
                                            <input type="text" class="form-control" name="cci" id="cci" v-model="fields.cci" />
                                            <div v-if="errors && errors.cci" class="text-danger">{{ errors.cci[0] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" > Cerrar </button>
                        <button type="button" class="btn btn-primary" @click="guardar">Guardar</button>
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

export default {

//   props:['tipoDocumento'],
  data() {
    return {
        id:0,
        errors:{},
        fields:{
            tipo_documento:null,
            nro_documento:'',
            paterno:'',
            materno:'',
            nombres:'',
            condicion:1,
            codigo_unap:'',
            grado:null,
            programa:null,
            email:'',
            celular:'',
            ruc:'',
            cci:'',
            gsuite:''
            },
        tipoDocumentos:[],
        gradosAcademicos: [],
        programas: [],
      ///table//
        columns : ['id','nro_documento','paterno','materno','nombres','condicion','codigo_unap','grado_academico.denominacion','programa.denominacion','actions'],
        options:{
            headings:{
                'id': 'id',
                'nro_documento': 'Documento',
                'paterno': 'Apellido Paterno',
                'materno': 'Apellido Materno',
                'nombres': 'Nombres',
                'condicion': 'Condicion',
                'codigo_unap': 'Codigo Unap',
                'grado_academico.denominacion': 'Grado Academico',
                'programa.denominacion': 'Programa',
                'actions': 'Acciones'
            },
            sortable: [
                'id',
                'nro_documento',
                'paterno',
                'materno',
                'nombres',
                'condicion'
                ],
            filterable: [
                'nro_documento',
                'paterno',
                'materno',
                'nombres',
                'condicion'
            ],
            listColumns: {
                condicion: [{
                        id: "1",
                        text: 'Particular'
                    },
                    {
                        id: "2",
                        text: 'Unap'
                    }

                ]
            },
            filterByColumn:true,
        }
    };
  },




    methods: {
        editarDocente:function(id){
            this.id = id;
            this.errors = {};
            axios.get("docente/"+id+"/edit").then(response => {
                console.log(response);
                // this.fields.denominacion = response.data.denominacion;
                // this.fields.direccion = response.data.direccion;
                this.fields.ruc = response.data.ruc;
                this.fields.cci = response.data.cci
                this.fields.tipo_documento = response.data.tipo_documentos_id;
                this.fields.tipo_documento = response.data.tipo_documentos_id;
                this.fields.nro_documento = response.data.nro_documento;
                this.fields.paterno = response.data.paterno;
                this.fields.materno = response.data.materno;
                this.fields.nombres = response.data.nombres;
                this.fields.condicion = parseInt(response.data.condicion);
                this.fields.codigo_unap = response.data.codigo_unap;
                this.fields.grado = response.data.grado_academicos_id;
                this.fields.programa = response.data.programas_id;
                this.fields.email = response.data.email;
                this.fields.celular = response.data.celular;
                this.fields.gsuite = response.data.gsuite;
                // this.fields.ubigeo = response.data.ubigeo.id;
                // this.disabledProvincia = false;
                // this.disabledDistrito = false;
                // this.getProvincias();
                // this.getDistritos();
            });
            $("#ModalFormulario").modal("show");
        },
        guardar:function(){
            this.errors = {};
            $(".loader").show();
            axios
            .put("docente/"+this.id, this.fields)
            .then((response) => {
                $(".loader").hide();
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
                $(".loader").hide();
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
            });

        },
        getTipoDocumentos: function(){
            axios.get('/intranet/get-tipo-documentos')
            .then(function (response) {
                this.tipoDocumentos = response.data;
                // console.log(this.tipoDocumentos);
            }.bind(this));
        },
        getGradosAcademicos: function(){
            axios.get('/intranet/get-grados-academicos')
            .then(function (response) {
                this.gradosAcademicos = response.data;
            }.bind(this));
        },
        getProgramas: function(){
            axios.get('/intranet/get-programas')
            .then(function (response) {
                this.programas = response.data;
            }.bind(this));
        },
        codigoVaciar: function(){
            this.fields.codigo_unap = "";
        },

    },
    mounted(){
        this.getTipoDocumentos();
        this.getGradosAcademicos();
        this.getProgramas();
    }
};
</script>

<style>

</style>
