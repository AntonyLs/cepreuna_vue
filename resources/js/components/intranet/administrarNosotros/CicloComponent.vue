<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Ciclos
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/administrar-nosotros/ciclos/lista/data">
                            <div slot="estado" slot-scope="props">
                                <span v-if="props.row.estado == 1" class="badge badge-success">Activo</span>
                                <span v-else class="badge badge-danger">Inactivo</span>
                            </div>
                            <div slot="actions" slot-scope="props">
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
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
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="inicio_ciclo">Inicio de ciclo</label>
                                        <input type="text" class="form-control" id="inicio_ciclo" v-model="fields.inicio_ciclo" />
                                        <div v-if="errors && errors.inicio_ciclo" class="text-danger">
                                            {{ errors.inicio_ciclo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fin_ciclo">Fin de ciclo</label>
                                        <input type="text" class="form-control" id="fin_ciclo" v-model="fields.fin_ciclo" />
                                        <div v-if="errors && errors.fin_ciclo" class="text-danger">
                                            {{ errors.fin_ciclo[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="inicio_inscripciones">Inicio de Inscripciones</label>
                                        <input type="date" class="form-control" name="inicio_inscripciones" id="inicio_inscripciones" v-model="fields.inicio_inscripciones" />
                                        <div v-if="errors && errors.inicio_inscripciones" class="text-danger">
                                            {{ errors.inicio_inscripciones[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="fin_inscripciones">Fin de Inscripciones</label>
                                        <input type="date" class="form-control" name="fin_inscripciones" id="fin_inscripciones" v-model="fields.fin_inscripciones" />
                                        <div v-if="errors && errors.fin_inscripciones" class="text-danger">
                                            {{ errors.fin_inscripciones[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="inicio_clases">Inicio de Clases</label>
                                        <input type="date" class="form-control" name="inicio_clases" id="inicio_clases" v-model="fields.inicio_clases" />
                                        <div v-if="errors && errors.inicio_clases" class="text-danger">
                                            {{ errors.inicio_clases[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="file">Imagen</label><br />
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" id="file" name="file" accept="image/png, image/jpeg, image/jpg" @change="upload" class="custom-file-input " />
                                                <label class="custom-file-label" for="exampleInputFile">{{ selectAdjunto.substr(0, 20) }}...</label>
                                            </div>
                                        </div>
                                        <div v-if="errors && errors.file" class="text-danger">
                                            {{ errors.file[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-12">Estado</label>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.estado" :value="1" checked />
                                                Activo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="radio">
                                            <label>
                                                <input type="radio" v-model="fields.estado" :value="0" />
                                                Inactivo
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.estado" class="text-danger">
                                        {{ errors.estado[0] }}
                                    </div>
                                </div>
                                <hr />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Cerrar
                            </button>
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
export default {
    props: ["permissions"],
    data() {
        return {
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                inicio_ciclo: "",
                fin_ciclo: "",
                inicio_inscripciones: "",
                fin_inscripciones: "",
                inicio_clases: "",
                estado: "",
                file: ""
            },
            errors: {},
            columns: [
                // "id",
                "inicio_ciclo",
                "fin_ciclo",
                "inicio_inscripciones",
                "fin_inscripciones",
                "inicio_clases",
                "estado",
                "actions"
            ],
            options: {
                headings: {
                    inicio_ciclo: "Inicio Ciclo",
                    fin_ciclo: "Fin Ciclo",
                    inicio_inscripciones: "Inicio Inscripcion",
                    fin_inscripciones: "Fin Inscripción",
                    inicio_clases: "Inicio de Clases",
                    estado: "Estado",
                    actions: "Acciones"
                },
                sortable: ["descripcion", "nosotros_tipo_dato_id", "activo"],
                filterable: [],
                customFilters: [],
                filterByColumn: true
            },
            selectAdjunto: "Selecione"
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Agregar Nuevo Item";
            this.fields.inicio_ciclo = "";
            this.fields.fin_ciclo = "";
            this.fields.inicio_inscripciones = "";
            this.fields.fin_inscripciones = "";
            this.fields.inicio_clases = "";
            this.fields.estado = "";
            this.fields.file = null;
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Item";
            axios.get("ciclos/" + id + "/edit").then(response => {
                console.log(response.data.ciclos.inicio_ciclo);
                this.fields.inicio_ciclo = response.data.ciclos.inicio_ciclo;
                this.fields.fin_ciclo = response.data.ciclos.fin_ciclo;
                this.fields.inicio_inscripciones = response.data.ciclos.inicio_inscripciones;
                this.fields.fin_inscripciones = response.data.ciclos.fin_inscripciones;
                this.fields.inicio_clases = response.data.ciclos.inicio_clases;
                this.fields.estado = response.data.ciclos.estado;
            });
            this.fields.file = "";
            this.selectAdjunto = "Selecione";
            $("#ModalFormulario").modal("show");
        },
        upload(e) {
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
                formData.append("inicio_ciclo", typeof this.fields.inicio_ciclo !== "undefined" ? this.fields.inicio_ciclo : "");
                formData.append("fin_ciclo", typeof this.fields.fin_ciclo !== "undefined" ? this.fields.fin_ciclo : "");
                formData.append("inicio_inscripciones", typeof this.fields.inicio_inscripciones !== "undefined" ? this.fields.inicio_inscripciones : "");
                formData.append("fin_inscripciones", typeof this.fields.fin_inscripciones !== "undefined" ? this.fields.fin_inscripciones : "");
                formData.append("inicio_clases", typeof this.fields.inicio_clases !== "undefined" ? this.fields.inicio_clases : "");
                formData.append("estado", typeof this.fields.estado !== "undefined" ? this.fields.estado : "");
                formData.append("file", typeof this.fields.file !== "undefined" ? this.fields.file : "");

                axios
                    .post("ciclos", formData)
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
                formData.append("inicio_ciclo", typeof this.fields.inicio_ciclo !== "undefined" ? this.fields.inicio_ciclo : "");
                formData.append("fin_ciclo", typeof this.fields.fin_ciclo !== "undefined" ? this.fields.fin_ciclo : "");
                formData.append("inicio_inscripciones", typeof this.fields.inicio_inscripciones !== "undefined" ? this.fields.inicio_inscripciones : "");
                formData.append("fin_inscripciones", typeof this.fields.fin_inscripciones !== "undefined" ? this.fields.fin_inscripciones : "");
                formData.append("inicio_clases", typeof this.fields.inicio_clases !== "undefined" ? this.fields.inicio_clases : "");
                formData.append("estado", typeof this.fields.estado !== "undefined" ? this.fields.estado : "");
                formData.append("file", typeof this.fields.file !== "undefined" ? this.fields.file : "");
                axios
                    .post("update-ciclos/" + this.id, formData)
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
        }
    }
};
</script>

<style></style>
