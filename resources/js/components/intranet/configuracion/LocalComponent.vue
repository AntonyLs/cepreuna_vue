<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Locales
                        <button class="btn btn-primary float-right" @click="nuevo"><i class="fa fa-plus"></i> Nuevo</button>
                    </header>
                    <div class="card-body">
                        <!-- <button type="button" id="agregar-area-users" class="btn btn-secondary btn-sm">
                        <i class="fa fa-plus"></i> Agregar
                        </button>  -->
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/configuracion/local/lista/data">
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
                                        <input type="text" class="form-control" id="denominacion" v-model="fields.denominacion" />
                                        <div v-if="errors && errors.denominacion" class="text-danger">
                                            {{ errors.denominacion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="direccion">Direccion</label>
                                        <input type="text" class="form-control" id="direccion" v-model="fields.direccion" />
                                        <div v-if="errors && errors.direccion" class="text-danger">
                                            {{ errors.direccion[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="nro_aulas">N° Aulas</label>
                                        <input type="text" class="form-control" id="nro_aulas" v-model="fields.nro_aulas" />
                                        <div v-if="errors && errors.nro_aulas" class="text-danger">
                                            {{ errors.nro_aulas[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="sede">Sede</label>
                                        <select type="text" class="form-control" id="sede" v-model="fields.sede">
                                            <option value="">--Seleccionar--</option>
                                            <option v-for="sede in sedes" :key="sede.id" :value="sede.id">{{ sede.denominacion }}</option>
                                        </select>
                                        <div v-if="errors && errors.sede" class="text-danger">
                                            {{ errors.sede[0] }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <input type="file" @change="handleImageChange" ref="imageInput" accept="image/*" />
                                        <div v-if="errors && errors.image" class="text-danger">
                                            {{ errors.image[0] }}
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
    // props:[],
    data() {
        return {
            ///table//
            edit: 0,
            id: 0,
            titulo: "",
            fields: {
                estado: "1",
                denominacion: "",
                direccion: "",
                nro_aulas: "",
                sede: "",
                image: null
            },
            errors: {},
            sedes: [],
            columns: ["id", "denominacion", "direccion", "nro_aulas", "sede.denominacion", "actions"],
            options: {
                headings: {
                    id: "id",
                    denominacion: "Denominacion",
                    direccion: "Direccion",
                    nro_aulas: "N° Aulas",
                    "sede.denominacion": "Sede",
                    actions: "Acciones"
                },
                sortable: ["id", "denominacion"]
                // filterable: ['correlativo','num_mat','paterno'],
                // customFilters: ['correlativo','num_mat']
                // filterByColumn:true
            }
        };
    },

    methods: {
        nuevo: function() {
            this.edit = 0;
            this.errors = {};
            this.titulo = "Nuevo Local";
            this.fields.denominacion = "";
            this.fields.direccion = "";
            this.fields.nro_aulas = "";
            this.fields.sede = "";
            this.fields.image = null; // Restablece el campo de entrada de imagens
            const inputElement = this.$refs.imageInput;
            if (inputElement) {
                inputElement.value = null;
            }
            $("#ModalFormulario").modal("show");
        },
        editar: function(id) {
            this.edit = 1;
            this.id = id;
            this.errors = {};
            this.titulo = "Editar Local";
            axios.get("local/" + id + "/edit").then(response => {
                this.fields.denominacion = response.data.denominacion;
                this.fields.direccion = response.data.direccion;
                this.fields.nro_aulas = response.data.nro_aulas;
                this.fields.sede = response.data.sedes_id;
            });
            // console.log("hgols");
            $("#ModalFormulario").modal("show");
        },
        async submit() {
            this.errors = {};

            if (this.edit === 0) {
                await this.subirImagen();
                axios
                    .post("local", this.fields)
                    .then(response => {
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            this.fields.image = null; // Restablece el campo de entrada de imagens
                            const inputElement = this.$refs.imageInput;
                            if (inputElement) {
                                inputElement.value = null;
                            }
                            $("#ModalFormulario").modal("hide");
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            } else {
                await this.subirImagen();
                axios
                    .put("local/" + this.id, this.fields)
                    .then(response => {
                        // $(".loader").hide();
                        if (response.data.status) {
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            this.fields.image = null; // Restablece el campo de entrada de imagens
                            const inputElement = this.$refs.imageInput;
                            if (inputElement) {
                                inputElement.value = null;
                            }
                            $("#ModalFormulario").modal("hide");
                            // window.location.replace(response.data.url);
                        } else {
                            toastr.warning(response.data.message, "Aviso");
                        }
                    })
                    .catch(error => {
                        // $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                        }
                    });
            }
        },
        handleImageChange(event) {
            // Accede al archivo seleccionado por el usuario
            const file = event.target.files[0];
            // Asigna el archivo a la propiedad adecuada en `fields`
            this.fields.image = file;
            // Puedes realizar otras acciones aquí si es necesario, como previsualizar la imagen.
        },
        async subirImagen() {
            this.errors = {};
            let formData = new FormData();
            //formData.append("avatar", this.$refs.elFileFoto.files[0]);
            formData.append("image", this.fields.image);
            await axios
                .post("local/upload", formData)
                .then(response => {
                    console.log("subi img ", response.data);
                    this.fields.image = response.data;
                })
                .catch(error => {
                    // this.$toastr.e(e.response.data.message, "ERROR");
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                        console.log(error.response.data)
                    }
                    console.log(e);
                    return;
                });
        },
        getSede: function() {
            axios.get("/intranet/get-sedes").then(
                function(response) {
                    this.sedes = response.data;
                }.bind(this)
            );
        }
    },
    mounted() {
        this.getSede();
    }
};
</script>

<style></style>