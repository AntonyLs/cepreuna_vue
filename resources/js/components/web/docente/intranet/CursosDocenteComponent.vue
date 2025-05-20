<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Lista General de Cursos</h5></div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <table class="table">
                                <thead>
                                    <tr style="background-color: #004d40 !important">
                                        <th scope="col">#</th>
                                        <th scope="col">Curso</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Dirección</th>
                                        <th scope="col">Estudiantes</th>
                                        <th scope="col">Meet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in carga" :key="index" :class="item.estado == '0' ? 'bg-light text-dark' : ''">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>
                                            {{ item.curso }}
                                        </td>
                                        <td>
                                            {{ item.tipo == "1" ? "Titular" : "Remplazo" }}
                                        </td>
                                        <td>
                                            {{ item.grupo }}
                                        </td>
                                        <td v-if="item.estado == '1'">
                                            <h6><span class="badge badge-success">Activo</span></h6>
                                        </td>
                                        <td v-else>
                                            <h6><span class="badge badge-danger">Inactivo</span></h6>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modelLugar" @click="verLugar(item)">Ver Lugar</button>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-info btn-sm"
                                                data-toggle="modal"
                                                data-target="#modalEstudiantes"
                                                @click="getEstudiante(item.grupo_aula_id)"
                                            >
                                                Ver Estudiantes
                                            </button>
                                        </td>
                                        <td>
                                            <div v-if="item.link != null">
                                                <a v-if="item.estado == '1'" :href="item.link" target="_blank">
                                                    {{ item.link }}
                                                </a>
                                                <a
                                                    v-if="item.estado == '1'"
                                                    name=""
                                                    id=""
                                                    class="btn btn-primary text-white m-2"
                                                    @click="idCarga(item.id)"
                                                    data-toggle="modal"
                                                    data-target="#modelIdEditar"
                                                    role="button"
                                                    >Editar Enlace</a
                                                >
                                            </div>
                                            <div v-else>
                                                <a
                                                    v-if="item.estado == '1'"
                                                    class="btn btn-success text-white btn-sm"
                                                    @click="idCarga(item.id)"
                                                    data-toggle="modal"
                                                    data-target="#modelId"
                                                >
                                                    Agregar Link
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <form @submit.prevent="submit">
            <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Link de Google Meet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="meet">Link de Meet</label>
                                <input
                                    type="url"
                                    class="form-control"
                                    v-model="fields.meet"
                                    name="meet"
                                    id="meet"
                                    aria-describedby="helpId"
                                    placeholder="Ingrese aqui su link de meet"
                                />
                            </div>
                            <div v-if="errors && errors.meet" class="text-danger">{{ errors.meet[0] }}</div>
                            <div class="text-info">Ejemplo: <b>https://meet.google.com/tov-uxq1ao-uhv</b></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form @submit.prevent="submit">
            <div class="modal fade" id="modelIdEditar" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Link de Meet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="meet">Link de Meet</label>
                                <input
                                    type="url"
                                    class="form-control"
                                    v-model="fields.meet"
                                    name="meet"
                                    id="meet"
                                    aria-describedby="helpId"
                                    placeholder="Ingrese aqui su link de meet"
                                />
                            </div>
                            <div v-if="errors && errors.meet" class="text-danger">{{ errors.meet[0] }}</div>
                            <div class="text-info">Ejemplo: <b>https://meet.google.com/tov-uxq1ao-uhv</b></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="modelLugar" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Dirección del grupo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table style="width: 100%">
                                    <tbody>
                                        <tr>
                                            <td class="text-left"><b>Sede </b></td>
                                            <td class="text-left" id="oficina">
                                                <b class="text-success">{{ sede }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><b>Local: </b></td>
                                            <td class="text-left" id="tarea">
                                                <b class="text-success"> {{ local }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><b>Dirección: </b></td>
                                            <td class="text-left" id="estado">
                                                <b class="text-success">{{ direccion }}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-left"><b>Aula: </b></td>
                                            <td class="text-left" id="estado">
                                                <b class="text-success"> {{ aula }}</b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br />
                                <div class="col-md-12 col-xs-12" v-if="foto">
                                    <img :src="foto" class="img-fluid" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalEstudiantes" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Lista de Estudiantes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped col table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Apellido Paterno</th>
                                                <th>Apellido Materno</th>
                                                <th>Nombres</th>
                                                <th>DNI</th>
                                                <th>Correo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in estudiantes" :key="i">
                                                <td>{{ i + 1 }}</td>
                                                <td>{{ item.paterno }}</td>
                                                <td>{{ item.materno }}</td>
                                                <td>{{ item.nombres }}</td>
                                                <td>{{ item.nro_documento }}</td>
                                                <td>{{ item.usuario }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <excel-export class="btn btn-success" :data="json_data" :columns="json_fields" :filename="'ListaEstudiantes'" :sheetname="'Reporte'">
                                        Descargar excel
                                    </excel-export>
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
import "vue-select/dist/vue-select.css";
import toastr from "toastr";
import $ from "jquery";

export default {
    data: () => ({
        sede: "",
        local: "",
        direccion: "",
        aula: "",
        foto: "",
        carga: [],
        estudiantes: [],
        fields: {
            id: ""
        },
        errors: {},
        disabled: false,
        json_data: [],
        json_fields: [
            {
                label: "Apellido Paterno",
                field: "paterno"
            },
            {
                label: "Apellido Materno",
                field: "materno"
            },
            {
                label: "Nombres",
                field: "nombres"
            },
            {
                label: "DNI",
                field: "nro_documento"
            },
            {
                label: "Correo",
                field: "usuario"
            }
        ]
    }),
    methods: {
        submit: function() {
            // $(".loader").show();
            this.errors = {};
            axios
                .post("cursos/carga-update", this.fields)
                .then(response => {
                    console.log(response.data.status);
                    if (response.data.status == true) {
                        // this.$refs.table.refresh();
                        toastr.success(response.data.message);
                        // $("#modelId").modal("hide");
                        this.getCarga();
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                    $(".loader").hide();
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        getEstudiante: function(id) {
            axios.get("cursos/get-estudiantes/" + id).then(
                function(response) {
                    this.estudiantes = response.data.estudiantes;
                    this.json_data = response.data.estudiantes;
                }.bind(this)
            );
        },
        getCarga: function() {
            axios.get("cursos/get-carga").then(
                function(response) {
                    // console.log(response.data);
                    this.carga = response.data;
                }.bind(this)
            );
        },
        idCarga: function(id) {
            this.fields.id = id;
        },
        verLugar: function(item) {
            this.sede = item.Sede;
            this.local = item.Local;
            this.direccion = item.DireccionLocal;
            this.aula = item.Aula;
            this.foto = "/images/locales/" + item.Foto;
        }
    },
    mounted() {
        this.getCarga();
    }
};
</script>

<style></style>
