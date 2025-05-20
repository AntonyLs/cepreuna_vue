<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Cuadernillos
                        <button v-if="permissions.includes('agregar cuadernillos')" class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Semana</label>
                                        <select class="form-control" v-model="semana" @change="changeFiltroSemana">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="semana in filtroSemanas" :value="semana" :key="semana">{{ semana }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="area">Area</label>
                                        <select class="form-control" v-model="area" @change="changeFiltroArea">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="area in filtroAreas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0 col-4">
                                        <label for="turno">Curso</label>
                                        <select class="form-control" v-model="curso" @change="changeFiltroCurso" :disabled="disabledFiltroCursos">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="curso in filtroCursos" :value="curso.id" :key="curso.id">{{ curso.denominacion }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/coordinador-docente/cuadernillo/lista/data">
                            <div slot="periodo" slot-scope="props">{{ props.row.periodo.inicio_ciclo }} - {{ props.row.periodo.fin_ciclo }}</div>
                            <div slot="tipo" slot-scope="props">
                                {{ props.row.tipo == 1 ? "Docente" : "Estudiante" }}
                            </div>

                            <div slot="actions" slot-scope="props">
                                <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button v-if="permissions.includes('editar cuadernillos')" class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                                <button v-if="permissions.includes('pdf cuadernillos')" class="btn btn-sm btn-warning" @click="ver(props.row.id)">
                                    Ver
                                </button>
                                <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
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
                                        <label for="area">Área de estudios</label>
                                        <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" @input="changeAreas" label="denominacion"></v-select>
                                        <div v-if="errors && errors.area" class="text-danger">{{ errors.area[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="cursos">Curso</label>
                                        <v-select
                                            v-model="fields.curricula_detalle"
                                            :options="cursos"
                                            :reduce="curso => curso.id"
                                            :disabled="disabledCursos"
                                            label="denominacion"
                                        ></v-select>
                                        <div v-if="errors && errors.curricula_detalle" class="text-danger">{{ errors.curricula_detalle[0] }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="semana">Semana</label>
                                        <v-select v-model="fields.semana" :options="semanas"></v-select>
                                        <div v-if="errors && errors.semana" class="text-danger">{{ errors.semana[0] }}</div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Tipo</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo" :value="1" checked />
                                                Docente
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.tipo" :value="2" />
                                                Estudiante
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.tipo" class="text-danger">
                                        {{ errors.tipo[0] }}
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-md-12 col-xs-12 control-label">Subir Archivo (solo formato .pdf): </label>
                                    <div class="col-md-12 col-xs-12 ">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" id="adjunto" name="adjunto" accept="application/pdf" @change="filesChange" class="custom-file-input " />
                                                <label class="custom-file-label" for="exampleInputFile">{{ selectAdjunto.substr(0, 20) }}...</label>
                                            </div>
                                        </div>
                                        <div v-if="errors && errors.file" class="text-danger">{{ errors.file[0] }}</div>
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
        <!-- Modal ver cuadernillo -->
        <div class="modal fade" id="ModalFicha" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cuadernillo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <object class="embed-responsive-item" type="application/pdf" :data="url" allowfullscreen width="100%" height="500" style="height: 85vh;"></object>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
    props: ["permissions"],
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                semana: "",
                tipo: "",
                file: "",
                curricula_detalle: null
            },
            errors: {},
            areas: [],
            cursos: [],
            semanas: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16"],
            semana: "",
            area: "",
            curso: "",
            filtroAreas: [],
            filtroCursos: [],
            filtroSemanas: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16"],
            url: "",
            disabledCursos: true,
            disabledFiltroCursos: true,
            selectAdjunto: "Selecione",
            columns: ["id", "periodo", "semana", "area", "curso", "tipo", "actions"],
            options: {
                headings: {
                    id: "id",
                    periodo: "Periodo",
                    semana: "Semana",
                    area: "Area",
                    curso: "Curso",
                    tipo: "Tipo",
                    actions: "Acciones"
                },
                sortable: ["id", "semana", "tipo", "area"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
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
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Cuadernillo";
            axios.get("cuadernillo/" + id + "/edit").then(response => {
                this.cursos = response.data.cursos;
                this.fields.area = response.data.area.id;
                this.fields.semana = response.data.cuadernillo.semana;
                this.fields.tipo = response.data.cuadernillo.tipo;
                this.fields.curricula_detalle = response.data.cuadernillo.curricula_detalles_id;
                this.disabledCursos = false;
            });
            this.fields.file = "";
            this.selectAdjunto = "Selecione";
            $("#ModalFormulario").modal("show");
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
            let formData = new FormData();
            if (this.edit == 0) {
                formData.append("area", typeof this.fields.area !== "undefined" ? this.fields.area : "");
                formData.append("semana", typeof this.fields.semana !== "undefined" ? this.fields.semana : "");
                formData.append("tipo", typeof this.fields.tipo !== "undefined" ? this.fields.tipo : "");
                formData.append("fecha", typeof this.fields.fecha !== "undefined" ? this.fields.fecha : "");
                formData.append("curricula_detalle", typeof this.fields.curricula_detalle !== "undefined" ? this.fields.curricula_detalle : "");
                formData.append("file", typeof this.fields.file !== "undefined" ? this.fields.file : "");
                axios
                    .post("cuadernillo", formData)
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
            } else {
                formData.append("file", typeof this.fields.file !== "undefined" ? this.fields.file : "");
                axios
                    .post("update-cuadernillo/" + this.id, formData)
                    .then(response => {
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
                    .catch(error => {
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }
            // console.log("hols");
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
        }
    },
    mounted() {
        this.getAreas();
    }
};
</script>

<style></style>
