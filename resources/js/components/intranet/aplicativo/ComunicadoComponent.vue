<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        Comunicados
                        <button class="btn btn-primary float-right" @click="nuevo">
                            <i class="fa fa-plus"></i> Nuevo
                        </button>
                    </header>
                    <div class="card-body">
                        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/aplicativo/comunicado/lista/data">
                            <div slot="actions" slot-scope="props">
                            <!-- <a class="btn btn-sm btn-primary" href="#" >Detalles</a> -->
                                <button class="btn btn-sm btn-info" @click="editar(props.row.id)">
                                    Editar
                                </button>
                            <!-- <a href="#" @click="editar(props.row.id)"><i class="fa  fa-trash big-icon text-danger" aria-hidden="true"></i></a> -->
                            </div>
                            <div slot="mostrar" slot-scope="props">
                                <template v-if="props.row.mostrar == '1'">
                                    Estudiantes
                                </template>
                                <template v-else-if="props.row.mostrar == '2'">
                                    Docentes
                                </template>
                                <template v-else-if="props.row.mostrar == '3'">
                                    Estudiantes y Docentes
                                </template>
                                <template v-else>
                                    Ninguno
                                </template>
                            </div>
                            <div slot="estado" slot-scope="props">
                                <template v-if="props.row.estado == '0'">
                                    <span class="badge badge-danger">Inactivo</span>
                                </template>
                                <template v-else-if="props.row.estado == '1'">
                                    <span class="badge badge-success">Activo</span>
                                </template>
                                <template v-else>
                                </template>
                            </div>
                        </v-server-table>
                    </div>
                </div>
            </div>
        </div>
        <form @submit.prevent="submit">
            <div class="modal fade" id="ModalFormulario" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="descripcion">Descripcion</label>
                                            <textarea type="text" class="form-control" name="descripcion" id="descripcion" rows="2" v-model="fields.descripcion"></textarea>
                                            <div v-if="errors && errors.descripcion" class="text-danger">{{ errors.descripcion[0] }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="titulo">Título</label>
                                            <input type="text" class="form-control" name="titulo" id="titulo" v-model="fields.titulo" />
                                            <div v-if="errors && errors.titulo" class="text-danger">{{ errors.titulo[0] }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="mostrar">Mostrar en paneles</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="mostrar" id="mostrar" v-model="fields.mostrar" value="1" class="form-check-input">
                                                            Estudiantes
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="mostrar" id="mostrar" v-model="fields.mostrar" value="2" class="form-check-input">
                                                            Docentes
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="mostrar" id="mostrar" v-model="fields.mostrar" value="3" class="form-check-input">
                                                            Ambos
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="errors && errors.mostrar" class="text-danger">{{ errors.mostrar[0] }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="estado">Estado</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="estado" id="estado" v-model="fields.estado" value="0" class="form-check-input">
                                                            Inactivo
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" name="estado" id="estado" v-model="fields.estado" value="1" class="form-check-input">
                                                            Activo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="errors && errors.estado" class="text-danger">{{ errors.estado[0] }}</div>
                                        </div>
                                        <div class="container-fluit">

                                        </div>

                                    </div>
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                    <input type="file" id="image_data" name="image" @change="GetImage" class="custom-file-input ">
                                                    <label class="custom-file-label" for="exampleInputFile" >1. Selecionar Imagen</label>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-4">
                                                <label>2. Recorta</label>

                                                <cropper
                                                    class="cropper"
                                                    :src="avatar"
                                                    ref="cropper"
                                                    :auto-zoom="true"
                                                    :transitions="true"
                                                    :resize-image="{
                                                        adjustStencil: true
                                                    }"
                                                    image-restriction="fit-area"
                                                    default-boundaries="fill"
                                                    :stencil-props="{
                                                        aspectRatio: 10/12
                                                    }"
                                                    :min-height="limitations.minHeight"
                                                    :min-width="limitations.minWidth"
                                                    :size-restrictions-algorithm="pixelsRestriction"
                                                    @change="change"
                                                />
                                            </div>
                                            <div class="col-md-4">
                                                <label>3. Previsualiza el resultado</label>
                                                <div v-if="image">
                                                    <img :src="image" class="img-fluid" alt="Responsive image">
                                                </div>

                                            </div>
                                            <div class="col-md-4">
                                                <label for="">Ancho:{{coordinates.width}} <b id="ancho"></b>px</label>
                                                <br>
                                                <label for="">Alto:{{coordinates.height}} <b id="alto"></b>px</label>
                                                <div class="alert alert-info" role="alert">
                                                    <small><li>El recorte de la imagen debe ser superior a 413 px de ancho y 531 px de alto</li></small>
                                                </div>
                                                <div v-if="modal" class="alert alert-danger mt-0" role="alert">
                                                <!-- <div class="alert alert-danger mt-0" role="alert"> -->
                                                    <small><li>El recorte de la imagen es muy pequeña</li></small>
                                                </div>
                                                <span class="badge badge-danger" id="error_imagen"></span>
                                                <br>
                                                <button v-if="foto != ''" type="button" v-on:click="guardar()" class="btn btn-primary">Actualizar Foto</button>
                                            </div>

                                            <div class="col-md-3 offset-md-4 col-xs-12 form-group">
                                                <div id="show" v-if="img" style="display:none">
                                                        <canvas class="img-fluid img-thumbnail" id="canvas" ></canvas>
                                                </div>
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
    import { Cropper } from 'vue-advanced-cropper';
    import $ from "jquery";
    import toastr from "toastr";
    // import 'quill/dist/quill.core.css'
    // import 'quill/dist/quill.snow.css'
    // import 'quill/dist/quill.bubble.css'
    // import { quillEditor } from 'vue-quill-editor'

    export default {
        name: 'TextEditor',
        data(){
            return {
                image:null,
                img:false,
                avatar:null,
                final:null,
                fin:null,
                modal: false,
                foto:'',
                coordinates: {
                    width: 0,
                    height: 0,
                    left: 0,
                    top: 0,
                },
                limitations: {
                    minWidth: 412,
                    minHeight: 430,
                    maxWidth: 413 +50 ,
                    maxHeight: 531 +50,
                },
                edit:0,
                id:0,
                titulo: "",
                fields: {
                    titulo:'',
                    mostrar:'3',
                    estado: "1" ,
                    contenido:''
                    },

                errors:{},
                columns: ["id", "titulo", "mostrar", "estado", "user.name", "actions"],
                options: {
                    headings: {
                        id: "id",
                        titulo: "Título",
                        mostrar: "Mostrar",
                        estado: "Estado",
                        "user.name": "Usuario",
                        actions: "Acciones"
                    },
                    sortable: [],
                    filterable: ["titulo"],
                    filterByColumn: true
                },
            }
        },
        components:{
            Cropper
        },
        methods: {
            pixelsRestriction({ minWidth, minHeight, maxWidth, maxHeight, imageWidth, imageHeight }) {
                return {
                    minWidth: minWidth,
                    minHeight: minHeight,
                    maxWidth: maxWidth,
                    maxHeight: maxHeight,
                };
            },
            change({ coordinates, canvas }) {
                this.coordinates = coordinates;

                this.image = canvas.toDataURL();

            },
            nuevo: function () {
                this.edit = 0;
                this.errors = {};
                this.titulo = "Nuevo Comunicado";
                this.fields.titulo = '';
                this.fields.mostrar = '3';
                this.fields.estado =  "1" ;
                this.fields.contenido = '';
                $("#ModalFormulario").modal("show");
            },
            editar: function (id) {
                this.edit = 1;
                this.id = id;
                this.errors = {};
                this.titulo = "Editar Comunicado";
                axios.get("comunicado/"+id+"/edit").then(response => {
                    this.fields.titulo = response.data.titulo;
                    this.fields.mostrar = response.data.mostrar;
                    this.fields.estado = response.data.estado;
                    this.fields.contenido = response.data.contenido;
                });
                // console.log("hgols");
                $("#ModalFormulario").modal("show");
            },
            submit: function () {
                this.errors = {};
                if(this.edit==0)
                    axios
                    .post("comunicado", this.fields)
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
                    .put("comunicado/"+this.id, this.fields)
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
        },
    }
</script>
