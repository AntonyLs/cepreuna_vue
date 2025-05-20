<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <header class="card-header">
            Directivos
            <button class="btn btn-primary float-right" @click="nuevo">
              <i class="fa fa-plus"></i> Nuevo
            </button>
          </header>
          <div class="card-body">
            <v-server-table
              ref="table"
              :columns="columns"
              :options="options"
              url="/intranet/administrar-nosotros/directivos/lista/data"
            >
              <div slot="tipo" slot-scope="props">
                <span v-if="props.row.tipo == '1'">Presidente</span>
                <span v-if="props.row.tipo == '2'">Miembro CEPREUNA</span>
              </div>
              <div slot="activo" slot-scope="props">
                <span v-if="props.row.activo == 1" class="badge badge-success"
                  >Activo</span
                >
                <span v-else class="badge badge-danger">Inactivo</span>
              </div>
              <div slot="actions" slot-scope="props">
                <button
                  class="btn btn-sm btn-info"
                  @click="editar(props.row.id)"
                >
                  Editar
                </button>
                <button
                  class="btn btn-sm btn-danger"
                  @click="desactivar(props.row.id)"
                  v-if="props.row.activo == 1"
                >
                  Desactivar
                </button>
                <button
                  class="btn btn-sm btn-warning"
                  @click="desactivar(props.row.id)"
                  v-if="props.row.activo == 0"
                >
                  Activar
                </button>
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
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="exampleModalLabel">{{ titulo }}</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="container">
                <div class="row form-group mb-0">
                  <label class="col-12">Sigla Grado Académico</label>
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.sigla_grado_academico"
                          :value="'Dr.'"
                          checked
                        />
                        Dr.
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.sigla_grado_academico"
                          :value="'Dra.'"
                        />
                        Dra.
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.sigla_grado_academico"
                          :value="'M. Sc.'"
                        />
                        M. Sc.
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.sigla_grado_academico"
                          :value="'Mg.'"
                        />
                        Mg.
                      </label>
                    </div>
                  </div>
                  <div
                    v-if="errors && errors.sigla_grado_academico"
                    class="text-danger"
                  >
                    {{ errors.sigla_grado_academico[0] }}
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <label class="col-md-12" for="nombres">Nombres</label>
                    <div class="col-md-12">
                      <input
                        type="text"
                        class="form-control"
                        name="nombres"
                        v-model="fields.nombres"
                        id="nombres"
                        aria-describedby="helpId"
                        placeholder=""
                      />
                      <div v-if="errors && errors.nombres" class="text-danger">
                        {{ errors.nombres[0] }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <label class="col-md-12" for="paterno"
                      >Apellido Paterno</label
                    >
                    <div class="col-md-12">
                      <input
                        type="text"
                        class="form-control"
                        name="paterno"
                        v-model="fields.paterno"
                        id="paterno"
                        aria-describedby="helpId"
                        placeholder=""
                      />
                      <div v-if="errors && errors.paterno" class="text-danger">
                        {{ errors.paterno[0] }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="row">
                    <label class="col-md-12" for="materno"
                      >Apellido Materno</label
                    >
                    <div class="col-md-12">
                      <input
                        type="text"
                        class="form-control"
                        name="materno"
                        v-model="fields.materno"
                        id="materno"
                        aria-describedby="helpId"
                        placeholder=""
                      />
                      <div v-if="errors && errors.materno" class="text-danger">
                        {{ errors.materno[0] }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row form-group mb-0">
                  <label class="col-12">Tipo</label>
                  <div class="col-md-6">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.tipo"
                          :value="'1'"
                          checked
                        />
                        Presidente
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.tipo"
                          :value="'2'"
                        />
                        Miembro CEPREUNA
                      </label>
                    </div>
                  </div>
                  <div v-if="errors && errors.tipo" class="text-danger">
                    {{ errors.tipo[0] }}
                  </div>
                </div>
                <div class="row form-group mb-0">
                  <label class="col-md-12 col-xs-12 control-label"
                    >Subir Imagen:
                  </label>
                  <div class="col-md-12 col-xs-12 mb-0">
                    <upload-imagen-directivo
                      @imagen64="fields.foto = $event"
                    ></upload-imagen-directivo>
                    <!-- <div class="input-group">
                      <div class="custom-file">
                        <input
                          type="file"
                          id="adjunto"
                          name="adjunto"
                          accept="jpg"
                          @change="filesChange"
                          class="custom-file-input"
                        />
                        <label class="custom-file-label" for="exampleInputFile"
                          >{{ selectAdjunto.substr(0, 20) }}...</label
                        >
                      </div>
                    </div> -->
                    <div v-if="errors && errors.foto" class="text-danger">
                      {{ errors.foto[0] }}
                    </div>
                  </div>
                </div>

                <!-- <div class="form-group">
                  <div
                    class="col-xs-12 col-md-8"
                    style="border-left: 1px solid #ddd"
                  >
                    <div class="form-group">
                      <div class="input-group">
                        <div class="custom-file">
                          <input
                            type="file"
                            id="image_data"
                            name="image"
                            @change="GetImage"
                            class="custom-file-input"
                          />
                          <label
                            class="custom-file-label"
                            for="exampleInputFile"
                            >1. Selecionar Imagen</label
                          >
                        </div>
                      </div>
                    </div>
                    <div class="">
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
                              adjustStencil: true,
                            }"
                            image-restriction="fit-area"
                            default-boundaries="fill"
                            :stencil-props="{
                              aspectRatio: 10 / 12,
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
                            <img
                              :src="image"
                              class="img-fluid"
                              alt="Responsive image"
                            />
                          </div>
                        </div>
                        <div class="col-md-4">
                          <label for=""
                            >Ancho:{{ coordinates.width }}
                            <b id="ancho"></b>px</label
                          >
                          <br />
                          <label for=""
                            >Alto:{{ coordinates.height }}
                            <b id="alto"></b>px</label
                          >
                          <div class="alert alert-info" role="alert">
                            <small
                              ><li>
                                El recorte de la imagen debe ser superior a 413
                                px de ancho y 531 px de alto
                              </li></small
                            >
                          </div>
                          <div
                            v-if="modal"
                            class="alert alert-danger mt-0"
                            role="alert"
                          >
                            <small
                              ><li>
                                El recorte de la imagen es muy pequeña
                              </li></small
                            >
                          </div>
                          <span
                            class="badge badge-danger"
                            id="error_imagen"
                          ></span>
                          <br />
                          <button
                            v-if="foto != ''"
                            type="button"
                            v-on:click="guardar()"
                            class="btn btn-primary"
                          >
                            Actualizar Foto
                          </button>
                        </div>

                        <div class="col-md-3 offset-md-4 col-xs-12 form-group">
                          <div id="show" v-if="img" style="display: none">
                            <canvas
                              class="img-fluid img-thumbnail"
                              id="canvas"
                            ></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                    <br />
                  </div>
                </div> -->
                <hr />
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                data-dismiss="modal"
              >
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
import { Cropper } from "vue-advanced-cropper";

export default {
  components: {
    Cropper,
  },
  props: ["permissions"],
  data() {
    return {
      edit: 0,
      titulo: "",
      fields: {
        sigla_grado_academico: "",
        nombres: "",
        paterno: "",
        materno: "",
        tipo: "",
        foto: "",
      },
      errors: {},
      selectAdjunto: "Selecione",
      columns: [
        "id",
        "tipo",
        "sigla_grado_academico",
        "nombres",
        "paterno",
        "materno",
        // "foto_path",
        "activo",
        "actions",
      ],
      options: {
        headings: {
          id: "Id",
          tipo: "Cargo",
          sigla_grado_academico: "Sigla",
          nombres: "Nombres",
          paterno: "Paterno",
          materno: "Materno",
          activo: "Estado",
          actions: "Acciones",
        },
        sortable: ["id", "nombres", "paterno", "materno", "tipo", "activo"],
        filterable: [],
        customFilters: [],
        filterByColumn: true,
      },
    };
  },

  methods: {
    nuevo: function () {
      this.edit = 0;
      this.errors = {};
      this.titulo = "Agregar Nuevo Directivo";
      this.fields.nombres = "";
      this.fields.paterno = "";
      this.fields.materno = "";
      this.fields.sigla_grado_academico = "";
      this.fields.tipo = "";
      this.fields.foto = "";
      this.selectAdjunto = "Selecione";
      $("#ModalFormulario").modal("show");
    },
    editar: function (id) {
      this.edit = 1;
      this.id = id;
      this.errors = {};
      this.titulo = "Editar Directivo";
      axios.get("directivos/" + id + "/edit").then((response) => {
        this.fields.nombres = response.data.nombres;
        this.fields.paterno = response.data.paterno;
        this.fields.materno = response.data.materno;
        this.fields.sigla_grado_academico = response.data.sigla_grado_academico;
        this.fields.tipo = response.data.tipo;
      });
      this.fields.foto = "";
      this.selectAdjunto = "Selecione";
      $("#ModalFormulario").modal("show");
    },
    desactivar: function (id) {
      this.id = id;
      axios
        .put("directivos/desactivar/" + this.id)
        .then((response) => {
          $(".loader").hide();
          if (response.data.status) {
            this.$refs.table.refresh();
            toastr.success(response.data.message);
            $("#ModalFormulario").modal("hide");
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
    filesChange(e) {
      let file = e.target.files[0];
      if (file === undefined) {
        this.selectAdjunto = "Selecione";
      } else {
        this.selectAdjunto = file.name;
        this.fields.file = file;
      }
    },
    submit: function () {
      $(".loader").show();
      this.errors = {};
      let formData = new FormData();
      if (this.edit == 0) {
        formData.append(
          "sigla_grado_academico",
          typeof this.fields.sigla_grado_academico !== "undefined"
            ? this.fields.sigla_grado_academico
            : ""
        );
        formData.append(
          "nombres",
          typeof this.fields.nombres !== "undefined" ? this.fields.nombres : ""
        );
        formData.append(
          "paterno",
          typeof this.fields.paterno !== "undefined" ? this.fields.paterno : ""
        );
        formData.append(
          "materno",
          typeof this.fields.materno !== "undefined" ? this.fields.materno : ""
        );
        formData.append(
          "tipo",
          typeof this.fields.tipo !== "undefined" ? this.fields.tipo : ""
        );
        formData.append(
          "foto",
          typeof this.fields.foto !== "undefined" ? this.fields.foto : ""
        );

        axios
          .post("directivos", formData)
          .then((response) => {
            $(".loader").hide();
            if (response.data.status) {
              this.$refs.table.refresh();
              toastr.success(response.data.message);
              $("#ModalFormulario").modal("hide");
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
      } else {
        formData.append(
          "foto",
          typeof this.fields.foto !== "undefined" ? this.fields.foto : ""
        );
        axios
          .put("directivos/" + this.id, formData)
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
      }
      // console.log("hols");
    },
  },
};
</script>

<style>
</style>