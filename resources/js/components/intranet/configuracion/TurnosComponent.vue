<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <header class="card-header">
            Turnos
            <button class="btn btn-primary float-right" @click="nuevo">
              <i class="fa fa-plus"></i> Nuevo
            </button>
          </header>
          <div class="card-body">
            <v-server-table
              ref="table"
              :columns="columns"
              :options="options"
              url="/intranet/configuracion/turnos/lista/data"
            >
              <div slot="estado" slot-scope="props">
                {{ props.row.estado == "1" ? "Activo" : "Inactivo" }}
              </div>
              <div slot="actions" slot-scope="props">
                <button
                  class="btn btn-sm btn-info"
                  @click="editar(props.row.id)"
                >
                  Editar
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
            <div class="modal-header">
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
              <div class="container-fluid">
                <div class="row">
                  <div class="form-group col-12">
                    <label for="denominacion">Denominación</label>
                    <input
                      type="text"
                      class="form-control"
                      id="denominacion"
                      v-model="fields.denominacion"
                    />
                    <div
                      v-if="errors && errors.denominacion"
                      class="text-danger"
                    >
                      {{ errors.denominacion[0] }}
                    </div>
                  </div>
                </div>
                <div class="row form-group">
                  <label class="col-12">Estado</label>
                  <div class="col-md-6">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.estado"
                          :value="1"
                          checked
                        />
                        Activo
                      </label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="radio">
                      <label>
                        <input
                          type="radio"
                          v-model="fields.estado"
                          :value="0"
                        />
                        Inactivo
                      </label>
                    </div>
                  </div>
                  <div v-if="errors && errors.estado" class="text-danger">
                    {{ errors.estado[0] }}
                  </div>
                </div>
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
import "vue-select/dist/vue-select.css";

export default {
  data() {
    return {
      edit: 0,
      id: 0,
      titulo: "",
      fields: {
        estado: "1",
        denominacion: "",
      },
      errors: {},
      turnos: [],
      columns: ["id", "denominacion", "estado", "actions"],
      options: {
        headings: {
          id: "id",
          denominacion: "Denominacion",
          estado: "Estado",
          actions: "Acciones",
        },
        sortable: ["id", "denominacion", "estado"],
      },
    };
  },

  methods: {
    nuevo: function () {
      this.edit = 0;
      this.errors = {};
      this.titulo = "Nuevo Turno";
      this.fields.estado = "1";
      this.fields.denominacion = "";
      $("#ModalFormulario").modal("show");
    },
    editar: function (id) {
      this.edit = 1;
      this.id = id;
      this.errors = {};
      this.titulo = "Editar Turno";
      axios.get("turnos/" + id + "/edit").then((response) => {
        this.fields.estado = response.data.estado;
        this.fields.denominacion = response.data.denominacion;
      });
      $("#ModalFormulario").modal("show");
    },
    submit: function () {
      this.errors = {};
      if (this.edit == 0)
        axios
          .post("turnos", this.fields)
          .then((response) => {
            if (response.data.status) {
              this.$refs.table.refresh();
              toastr.success(response.data.message);
              $("#ModalFormulario").modal("hide");
            } else {
              toastr.warning(response.data.message, "Aviso");
            }
          })
          .catch((error) => {
            if (error.response.status === 422) {
              this.errors = error.response.data.errors || {};
            }
          });
      else {
        axios
          .put("turnos/" + this.id, this.fields)
          .then((response) => {
            if (response.data.status) {
              this.$refs.table.refresh();
              toastr.success(response.data.message);
              $("#ModalFormulario").modal("hide");
            } else {
              toastr.warning(response.data.message, "Aviso");
            }
          })
          .catch((error) => {
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