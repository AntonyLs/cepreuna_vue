<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <header class="card-header">
            Historia
            <button class="btn btn-primary float-right" @click="nuevo">
              <i class="fa fa-plus"></i> Nuevo
            </button>
          </header>
          <div class="card-body">
            <v-server-table
              ref="table"
              :columns="columns"
              :options="options"
              url="/intranet/administrar-nosotros/historia/lista/data"
            >
              <div slot="nosotros_tipo_dato_id" slot-scope="props">
                <span v-if="props.row.nosotros_tipo_dato_id == '5'"
                  >Historia</span
                >
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
                <div class="form-group">
                  <div class="row">
                    <label class="col-md-12" for="descripcion"
                      >Descripción</label
                    >
                    <div class="col-md-12">
                      <textarea
                        class="form-control"
                        name="descripcion"
                        v-model="fields.descripcion"
                        id="descripcion"
                        placeholder=""
                      />
                      <div
                        v-if="errors && errors.descripcion"
                        class="text-danger"
                      >
                        {{ errors.descripcion[0] }}
                      </div>
                    </div>
                  </div>
                </div>
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
export default {
  props: ["permissions"],
  data() {
    return {
      edit: 0,
      id: 0,
      titulo: "",
      fields: {
        descripcion: "",
      },
      errors: {},
      columns: [
        // "id",
        "descripcion",
        "activo",
        "actions",
      ],
      options: {
        headings: {
          descripcion: "Descripción",
          activo: "Estado",
          actions: "Acciones",
        },
        sortable: ["descripcion", "activo"],
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
      this.titulo = "Agregar Nueva Historia";
      this.fields.descripcion = "";
      $("#ModalFormulario").modal("show");
    },
    editar: function (id) {
      this.edit = 1;
      this.id = id;
      this.errors = {};
      this.titulo = "Editar Historia";
      axios.get("historia/" + id + "/edit").then((response) => {
        this.fields.descripcion = response.data.descripcion;
      });
      $("#ModalFormulario").modal("show");
    },
    desactivar: function (id) {
      this.id = id;
      axios
        .put("historia/desactivar/" + this.id)
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
    submit: function () {
      $(".loader").show();
      this.errors = {};
      if (this.edit == 0) {
        axios
          .post("historia", this.fields)
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
        axios
          .put("historia/" + this.id, this.fields)
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
    },
  },
};
</script>

<style>
</style>