<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Lista de Estudiantes
                        <!-- <button class="btn btn-primary float-right" @click="nuevo">
                            <i class="fa fa-plus"></i> Nuevo
                        </button> -->
                    </header>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <!-- <div class="form-group mb-0 col-4">
                                        <label for="turno">Semana</label>
                                        <select class="form-control" v-model="semana" @change="changeFiltroSemana">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="semana in filtroSemanas" :value="semana" :key="semana">{{semana}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="area">Area</label>
                                        <select class="form-control" v-model="area" @change="changeFiltroArea">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="area in filtroAreas" :value="area.id" :key="area.id">{{area.denominacion}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Curso</label>
                                        <select class="form-control" v-model="curso" @change="changeFiltroCurso" :disabled="disabledFiltroCursos">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="curso in filtroCursos" :value="curso.id" :key="curso.id">{{curso.denominacion}}</option>
                                        </select>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administracion/estudiante/lista/data">
                            <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == '1'" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div>

                            <div slot="actions" slot-scope="props">
                                <button class="btn btn-sm btn-link text-info p-0 m-0 h5" @click="editar(props.row.id)">
                                    <i class="fas fa-file-signature"></i>
                                </button>
                                <button class="btn btn-sm btn-link text-danger p-1 m-0 h5" @click="eliminarCorreo(props.row.id)">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                                <!-- <button class="btn btn-sm btn-link text-primary p-1 m-0 h5" @click="editarFoto(props.row.id)">
                                    <i class="fas fa-camera-retro"></i>
                                </button> -->
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div
                class="modal fade"
                id="ModalFormulario"
                data-backdrop="static"
                data-keyboard="false"
                tabindex="-1"
                role="dialog"
                aria-labelledby="exampleModalLabel"
                aria-hidden="true"
            >
                <div class="modal-dialog modal-xl" role="document">
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
                                    <div class="col-md-2 col-xs-12 text-center">
                                        <img :src="'../../storage/fotos/' + fields.foto" class="card-img border-right" alt="" />
                                    </div>
                                    <div class="col-md-10 col-xs-12">
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
                                            <div class="col-md-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="sexo">Sexo</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="1" />
                                                                    M
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <label class="form-check-label">
                                                                    <input type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="2" />
                                                                    F
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-xs-12">
                                                <div class="form-group">
                                                    <label for="tipo documento">Tipo de Documento</label>
                                                    <v-select
                                                        v-model="fields.tipo_documento"
                                                        :options="tipoDocumentos"
                                                        :reduce="tipo_documento => tipo_documento.id"
                                                        label="denominacion"
                                                    ></v-select>
                                                    <div v-if="errors && errors.tipo_documento" class="text-danger">{{ errors.tipo_documento[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="nro_documento">Numero de Documento</label>
                                                    <input
                                                        type="text"
                                                        class="form-control"
                                                        name="nro_documento"
                                                        id="nro_documento"
                                                        @input="changeDocumento"
                                                        v-model="fields.nro_documento"
                                                    />
                                                    <div v-if="errors && errors.nro_documento" class="text-danger">{{ errors.nro_documento[0] }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="fecha nacimiento">Fecha de Nacimiento</label>
                                                    <input type="date" class="form-control" name="fecha_nac" id="fecha_nac" v-model="fields.fecha_nac" />
                                                    <div v-if="errors && errors.fecha_nac" class="text-danger">{{ errors.fecha_nac[0] }}</div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="email">Correo electrónico</label>
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
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 col-xs-12">
                                                <div class="form-group">
                                                    <label for="colegio">Colegio</label>
                                                    <v-select :options="colegios" label="denominacion" @search="onSearch" @input="changeColegio">
                                                        <template slot="no-options">
                                                            escriba para buscar su colegio
                                                        </template>
                                                        <template slot="option" slot-scope="option">
                                                            <div class="d-center">
                                                                {{ option.denominacion }}
                                                            </div>
                                                        </template>
                                                        <template slot="selected-option" slot-scope="option">
                                                            <div class="selected d-center">
                                                                {{ option.denominacion }}
                                                            </div>
                                                        </template>
                                                    </v-select>
                                                    <div class="text-dark">{{ textColegio }}</div>
                                                    <div v-if="errors && errors.colegio" class="text-danger">{{ errors.colegio[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-xs-12">
                                                <div class="form-group">
                                                    <label for="año de egreso">Año de egreso</label>
                                                    <input type="number" class="form-control" name="egreso" id="egreso" v-model="fields.egreso" />
                                                    <div v-if="errors && errors.egreso" class="text-danger">{{ errors.egreso[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="pais">Pais de residencia</label>
                                                    <!-- <input type="text" class="form-control" name="pais" id="pais" v-model="fields.pais" /> -->
                                                    <v-select :options="paises" :reduce="pais => pais.id" label="denominacion" @input="changePais"></v-select>
                                                    <div class="text-dark">{{ textPais }}</div>
                                                    <div v-if="errors && errors.pais" class="text-danger">{{ errors.pais[0] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="departamento">Departamento de residencia</label>

                                                    <v-select
                                                        :disabled="disabledDepartamento"
                                                        :options="departamentos"
                                                        :reduce="departamento => departamento.codigo_departamento"
                                                        v-model="fields.departamento"
                                                        label="departamento"
                                                        @input="changeDepartamento"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textDepartamento }}</div>
                                                    <div v-if="errors && errors.departamento" class="text-danger">{{ errors.departamento[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="provincia">Provincia de residencia</label>

                                                    <v-select
                                                        :disabled="disabledProvincia"
                                                        :options="provincias"
                                                        :reduce="provincia => provincia.codigo_provincia"
                                                        v-model="fields.provincia"
                                                        label="provincia"
                                                        @input="changeProvincia"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textProvincia }}</div>
                                                    <div v-if="errors && errors.provincia" class="text-danger">{{ errors.provincia[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="distrito">Distrito de residencia</label>

                                                    <v-select
                                                        :disabled="disabledDistrito"
                                                        :options="distritos"
                                                        label="distrito"
                                                        v-model="distrito"
                                                        @input="changeDistrito"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textDistrito }}</div>
                                                    <div v-if="errors && errors.ubigeo" class="text-danger">{{ errors.ubigeo[0] }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="departamento">Departamento de nacimiento</label>

                                                    <v-select
                                                        :disabled="disabledDepartamentoN"
                                                        :options="departamentosn"
                                                        :reduce="departamento => departamento.codigo_departamento"
                                                        v-model="fields.departamenton"
                                                        label="departamento"
                                                        @input="changeDepartamentoN"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textDepartamentoN }}</div>
                                                    <div v-if="errors && errors.departamenton" class="text-danger">
                                                        {{ errors.departamenton[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="provincia">Provincia de nacimiento</label>

                                                    <v-select
                                                        :disabled="disabledProvinciaN"
                                                        :options="provinciasn"
                                                        :reduce="provincia => provincia.codigo_provincia"
                                                        v-model="fields.provincian"
                                                        label="provincia"
                                                        @input="changeProvinciaN"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textProvinciaN }}</div>
                                                    <div v-if="errors && errors.provincian" class="text-danger">
                                                        {{ errors.provincian[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="distrito">Distrito de nacimiento</label>

                                                    <v-select
                                                        :disabled="disabledDistritoN"
                                                        :options="distritosn"
                                                        label="distrito"
                                                        v-model="distriton"
                                                        @input="changeDistritoN"
                                                    ></v-select>
                                                    <div class="text-dark">{{ textDistritoN }}</div>
                                                    <div v-if="errors && errors.ubigeon" class="text-danger">
                                                        {{ errors.ubigeon[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="usuario">Usuario</label>
                                                    <input type="text" class="form-control" name="usuario" id="usuario" v-model="fields.usuario" />
                                                    <div v-if="errors && errors.usuario" class="text-danger">{{ errors.usuario[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-12">
                                                <div class="form-group">
                                                    <label for="password">Contraseña</label>
                                                    <input type="text" class="form-control" name="password" id="password" v-model="fields.password" readonly />
                                                    <div v-if="errors && errors.password" class="text-danger">{{ errors.password[0] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-12">
                                                <div class="form-group">
                                                    <label for="password">GSuite</label>
                                                    <div v-if="fields.idgsuite" class="alert alert-success" role="alert" style="padding: 6px 15px;">
                                                        <strong>activo</strong>
                                                    </div>
                                                    <div v-else class="alert alert-danger" role="alert" style="padding: 6px 15px;">
                                                        <strong>inactivo</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="!fields.idgsuite" class="col-md-2 col-xs-12">
                                                <label for="password" style="color:white">.</label>
                                                <div class="form-group">
                                                    <button type="button" name="" id="" class="btn btn-success" @click="activarGsuite">activar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                nombres: "",
                paterno: "",
                materno: "",
                nro_documento: "",
                fecha_nac: "",
                celular: "",
                email: "",
                sexo: "",
                egreso: "",
                foto: "",
                usuario: "",
                password: "",
                estado: "",
                pais: "",
                ubigeo: "",
                tipo_documento: "",
                colegio: "",
                idgsuite: ""
            },
            errors: {},
            tipoDocumentos: [],
            colegios: [],
            paises: [],
            departamentos: [],
            provincias: [],
            distritos: [],
            departamentosn: [],
            provinciasn: [],
            distritosn: [],
            distrito: "",
            distriton: "",
            textDistrito: "",
            textProvincia: "",
            textDepartamento: "",
            textDistritoN: "",
            textProvinciaN: "",
            textDepartamentoN: "",
            textColegio: "",
            textPais: "",
            area: "",
            curso: "",
            filtroAreas: [],
            url: "",
            disabledCursos: true,
            disabledFiltroCursos: true,
            disabledDepartamento: true,
            disabledProvincia: true,
            disabledDistrito: true,
            disabledDepartamentoN: true,
            disabledProvinciaN: true,
            disabledDistritoN: true,
            selectAdjunto: "Selecione",
            columns: ["id", "nombres", "paterno", "materno", "nro_documento", "celular", "estado", "departamento", "provincia", "distrito", "colegio", "actions"],
            options: {
                headings: {
                    id: "id",
                    nombres: "Nombres",
                    paterno: "Paterno",
                    materno: "Materno",
                    nro_documento: "Documento",
                    celular: "Celular",
                    estado: "Estado",
                    departamento: "Departamento",
                    provincia: "Provincia",
                    distrito: "Distrito",
                    colegio: "Colegio",
                    actions: "Acciones"
                },
                sortable: ["id"],
                filterable: ["nombres", "paterno", "materno", "nro_documento", "celular", "estado"],
                listColumns: {
                    estado: [
                        {
                            id: "1",
                            text: "Activo"
                        },
                        {
                            id: "0",
                            text: "Inactivo"
                        }
                    ]
                },
                // customFilters: [],
                filterByColumn: true
            },
            image: null,
            img: false,
            modal: false,
            avatar: null,
            final: null,
            fin: null,
            coordinates: {
                width: 0,
                height: 0,
                left: 0,
                top: 0
            },
            limitations: {
                minWidth: 412,
                minHeight: 430,
                maxWidth: 413 + 50,
                maxHeight: 531 + 50
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Agregar Nuevo Cuadernillo";
            this.fields.area = "";
            this.fields.semana = "";
            this.fields.tipo = 1;
            this.fields.file = "";
            this.fields.curricula_detalle = "";
            this.selectAdjunto = "Selecione";
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            // console.log(id);
            this.fields.departamento = null;
            this.fields.provincia = null;
            this.distrito = null;
            this.fields.departamenton = null;
            this.fields.provincian = null;
            this.distriton = null;
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Estudiante";
            axios.get("estudiante/" + id + "/edit").then(response => {
                console.log(response.data);
                this.fields.nombres = response.data.nombres;
                this.fields.paterno = response.data.paterno;
                this.fields.materno = response.data.materno;
                this.fields.nro_documento = response.data.nro_documento;
                this.fields.fecha_nac = response.data.fecha_nac;
                this.fields.celular = response.data.celular;
                this.fields.email = response.data.email;
                this.fields.sexo = response.data.sexo;
                this.fields.egreso = response.data.anio_egreso;
                this.fields.foto = response.data.foto;
                this.fields.usuario = response.data.usuario;
                this.fields.password = response.data.password;
                this.fields.estado = response.data.estado;
                this.fields.pais = response.data.pais_id;
                this.textPais = response.data.pais.denominacion;
                this.textDistrito = response.data.ubigeo.distrito;
                this.textProvincia = response.data.ubigeo.provincia;
                this.textDepartamento = response.data.ubigeo.departamento;
                this.textDistritoN = response.data.ubigeo_nacimiento.distrito;
                this.textProvinciaN = response.data.ubigeo_nacimiento.provincia;
                this.textDepartamentoN = response.data.ubigeo_nacimiento.departamento;
                this.textColegio = response.data.colegio.denominacion;
                this.fields.ubigeo = response.data.ubigeos_id;
                // this.fields.departamento = response.data.ubigeo.codigo_departamento;
                // this.fields.provincia = response.data.ubigeo.codigo_provincia;
                this.fields.tipo_documento = response.data.tipo_documentos_id;
                this.fields.colegio = response.data.colegios_id;
                this.fields.idgsuite = response.data.idgsuite;
            });
            this.fields.file = "";
            this.selectAdjunto = "Selecione";
            $("#ModalFormulario").modal("show");
        },
        eliminarCorreo: function(id) {
            console.log("holi");
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de eliminar correo del estudiante?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .post("../eliminar-correo/" + id)
                            .then(response => {
                                $(".loader").hide();
                                console.log(response.data);
                                if (response.data.status) {
                                    toastr.success(response.data.message);
                                } else {
                                    toastr.warning(response.data.message, "Aviso");
                                }
                            })
                            .catch(error => {
                                $(".loader").hide();
                                toastr.warning("error al eliminar correo, intentelo de nuevo", "Aviso");
                            });
                    }
                }
            });
        },
        ver: function(id) {
            axios.get("cuadernillo/" + id).then(response => {
                this.url = "../../storage/documentos/" + response.data.path;
            });
            // console.log("hgols");
            $("#ModalFicha").modal("show");
        },
        filesChange(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjunto = "Selecione";
            } else {
                this.selectAdjunto = file.name;
                this.fields.file = file;
            }
        },
        submit: function() {
            $(".loader").show();
            this.errors = {};
            axios
                .put("estudiante/" + this.id, this.fields)
                .then(response => {
                    $(".loader").hide();

                    if (response.data.status) {
                        this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        $("#ModalFormulario").modal("hide");
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });

            // console.log("hols");
        },
        activarGsuite: function() {
            $(".loader").show();

            axios
                .post("estudiante/activar-gsuite/" + this.id)
                .then(response => {
                    $(".loader").hide();

                    if (response.data.status) {
                        this.editar(this.id);
                        toastr.success(response.data.message);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        changeAreas: function(area) {
            axios
                .get("../get-cursos-curricula", {
                    params: {
                        area: area
                    }
                })
                .then(
                    function(response) {
                        console.log(response);
                        this.cursos = response.data;
                    }.bind(this)
                );
            if (area != null) {
                this.disabledCursos = false;
            } else {
                this.disabledCursos = true;
            }
        },
        getAreas: function() {
            axios.get("../get-areas").then(
                function(response) {
                    this.areas = response.data;
                    this.filtroAreas = response.data;
                }.bind(this)
            );
        },
        getCursos: function(area) {
            axios
                .get("../get-cursos-curricula", {
                    params: {
                        area: area
                    }
                })
                .then(
                    function(response) {
                        console.log(response);
                        this.filtroCursos = response.data;
                    }.bind(this)
                );
            this.disabledFiltroCursos = false;
        },
        getPaises: function() {
            axios.get("../get-paises").then(
                function(response) {
                    this.paises = response.data;
                }.bind(this)
            );
        },
        getDepartamentos: function() {
            axios.get("../get-departamentos").then(
                function(response) {
                    this.departamentos = response.data;
                }.bind(this)
            );
        },
        getProvincias: function() {
            axios
                .get("../get-provincias", {
                    params: {
                        codigo: this.fields.departamento
                    }
                })
                .then(
                    function(response) {
                        this.provincias = response.data;
                    }.bind(this)
                );
        },
        getDistritos: function() {
            axios
                .get("../get-distritos", {
                    params: {
                        codigo: this.fields.provincia
                    }
                })
                .then(
                    function(response) {
                        this.distritos = response.data;
                    }.bind(this)
                );
        },
        changeDepartamentoN: function(departamento) {
            // this.fields.departamento = departamento

            if (departamento != null) {
                this.disabledProvinciaN = false;
                this.disabledDistritoN = true;
            } else {
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
            }
            this.fields.provincian = null;
            this.distriton = null;

            this.getProvinciasN();
        },
        changeProvinciaN: function(provincia) {
            if (provincia != null) {
                this.disabledDistritoN = false;
            } else {
                this.disabledDistritoN = true;
            }
            this.distriton = null;
            this.getDistritosN();
        },
        changeDistritoN: function(distrito) {
            this.fields.ubigeon = distrito.id;
        },
        getDepartamentosN: function() {
            axios.get("../get-departamentos").then(
                function(response) {
                    this.departamentosn = response.data;
                }.bind(this)
            );
        },
        getProvinciasN: function() {
            axios
                .get("../get-provincias", {
                    params: {
                        codigo: this.fields.departamenton
                    }
                })
                .then(
                    function(response) {
                        this.provinciasn = response.data;
                    }.bind(this)
                );
        },
        getDistritosN: function() {
            axios
                .get("../get-distritos", {
                    params: {
                        codigo: this.fields.provincian
                    }
                })
                .then(
                    function(response) {
                        this.distritosn = response.data;
                    }.bind(this)
                );
        },
        getTipoDocumentos: function() {
            axios.get("../get-tipo-documentos").then(
                function(response) {
                    this.tipoDocumentos = response.data;
                    // console.log(this.tipoDocumentos);
                }.bind(this)
            );
        },
        changeDocumento: function() {
            if (this.fields.nro_documento.length == 0) {
                this.statusDocumento = false;
            } else {
                this.statusDocumento = true;
            }
        },
        changePais: function(pais) {
            // console.log(data);
            if (pais == 1) {
                this.disabledDepartamento = false;
                this.disabledProvincia = true;
                this.disabledDistrito = true;

                this.disabledDepartamentoN = false;
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
            } else {
                this.disabledDepartamento = true;
                this.disabledProvincia = true;
                this.disabledDistrito = true;
                this.fields.departamento = null;
                this.fields.provincia = null;
                this.distrito = null;

                this.disabledDepartamentoN = true;
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
                this.fields.departamenton = null;
                this.fields.provincian = null;
                this.distriton = null;
            }
            this.fields.pais = pais;
        },
        changeDepartamento: function(departamento) {
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
        changeProvincia: function(provincia) {
            if (provincia != null) {
                this.disabledDistrito = false;
            } else {
                this.disabledDistrito = true;
            }
            this.distrito = null;
            this.getDistritos();
        },
        changeDistrito: function(distrito) {
            this.fields.ubigeo = distrito.id;
        },
        changeColegio: function(data) {
            if (data != null) {
                this.fields.colegio = data.id;

                this.statusColegio = true;
            } else {
                this.statusColegio = false;
                this.montoPagar = 0;
            }
        },
        changeFiltroArea: function() {
            if (this.area == "") {
                this.disabledFiltroCursos = true;
                this.curso = "";
            }
            this.getCursos(this.area);
            this.$refs.table.setCustomFilters({ semana: this.semana, area: this.area, curso: this.curso });
        },
        changeFiltroCurso: function() {
            this.$refs.table.setCustomFilters({ semana: this.semana, area: this.area, curso: this.curso });
        },
        changeFiltroSemana: function() {
            this.$refs.table.setCustomFilters({ semana: this.semana, area: this.area, curso: this.curso });
        },
        onSearch(search, loading) {
            if (search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        search: _.debounce((loading, search, vm) => {
            fetch(`../get-colegios?q=${escape(search)}`).then(res => {
                res.json().then(json => (vm.colegios = json));
                loading(false);
            });
        }, 350)
    },
    mounted() {
        this.getAreas();
        this.getTipoDocumentos();
        this.getPaises();
        this.getDepartamentos();
        this.getDepartamentosN();
        this.getProvincias();
        this.getDistritos();
        // this.getColegios();
    }
};
</script>

<style></style>
