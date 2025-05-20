<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <header class="card-header">
            Parentescos
            <button class="btn btn-primary float-right" @click="nuevo">
              <i class="fa fa-plus"></i> Nuevo
            </button>
          </header>
          <div class="card-body">
            <v-server-table
              ref="table"
              :columns="columns"
              :options="options"
              url="/intranet/configuracion/parentescos/lista/data"
            >
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
        denominacion: "",
      },
      errors: {},
      parentescos: [],
      columns: ["id", "denominacion", "actions"],
      options: {
        headings: {
          id: "id",
          denominacion: "Denominacion",
          actions: "Acciones",
        },
        sortable: ["id", "denominacion"],
      },
    };
  },

  methods: {
    nuevo: function () {
      this.edit = 0;
      this.errors = {};
      this.titulo = "Nuevo Parentesco";
      this.fields.denominacion = "";
      $("#ModalFormulario").modal("show");
    },
    editar: function (id) {
      this.edit = 1;
      this.id = id;
      this.errors = {};
      this.titulo = "Editar Parentesco";
      axios.get("parentescos/" + id + "/edit").then((response) => {
        this.fields.denominacion = response.data.denominacion;
      });
      $("#ModalFormulario").modal("show");
    },
    submit: function () {
      this.errors = {};
      if (this.edit == 0)
        axios
          .post("parentescos", this.fields)
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
          .put("parentescos/" + this.id, this.fields)
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