<template>
  <div>
    <div class="row">
      <div class="col-sm-12">
        <h4>Docentes Registrados.</h4>
        <div class="card">
          <header class="card-header">
            <div class="row">
              <div class="col-5">
                <div class="row">
                  <div class="form-group mb-0 col-10">
                    <label for="sede">Condicion</label>
                    <select
                      class="form-control"
                      v-model="condicion"
                      @change="changeCondicion"
                    >
                      <option value="">--Seleccionar--</option>
                      <option value="1">Particular</option>
                      <option value="2">Unap</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-5">
                <div class="row">
                  <div class="form-group mb-0 col-10">
                    <label for="sede">Contrato</label>
                    <select
                      class="form-control"
                      v-model="contrato"
                      @change="changeContrato"
                    >
                      <option value="">--Seleccionar--</option>
                      <option value="0">No</option>
                      <option value="1">Si</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-2">
                <div class="row justify-content-end">
                  <!-- <button v-if="permissions.includes('habilitar docente')" class="btn btn-success col-md-6" @click="habilitar">Habilitar</button> -->
                  <button class="btn btn-success col-md-8" @click="habilitar">
                    Habilitar
                  </button>
                  <button
                    class="btn btn-danger col-md-8 mt-2"
                    @click="deshabilitar"
                  >
                    Deshabilitar
                  </button>
                </div>
              </div>
            </div>
          </header>
          <div class="card-body">
            <div class="form-check">
              <input
                type="checkbox"
                id="todo"
                class="form-check-input"
                v-model="todo"
                @change="seleccionarTodo($event)"
                value="true"
              />
              <label for="todo" class="mb-0">Seleccionar Todo</label>
            </div>
            <v-server-table
              ref="table"
              :columns="columns"
              :options="options"
              url="/intranet/recursos-humanos/pagos/docentes/habilitacion/lista/data"
            >
              <div
                v-if="
                  !props.row.expediente.find((e) => {
                    return e.estado == '0';
                  })
                "
                slot="select"
                slot-scope="props"
              >
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    v-model="checks"
                    :value="props.row.id"
                  />
                </div>
              </div>
              <div slot="condicion" slot-scope="props">
                <template v-if="props.row.condicion == '1'">
                  Particular
                </template>
                <template v-else> Unap </template>
              </div>
              <div slot="periodo" slot-scope="props">
                <template>
                  {{ props.row.periodo.inicio_ciclo }} -
                  {{ props.row.periodo.fin_ciclo }}
                </template>
              </div>
              <div slot="estado" slot-scope="props">
                <template
                  v-if="
                    props.row.expediente.find((e) => {
                      return e.estado == '0';
                    })
                  "
                >
                  <span class="badge badge-info">En curso</span>
                </template>
                <template v-else>
                  <span class="badge badge-secondary">Vacio</span>
                </template>
                <!-- <template v-if="props.row.estado == '0'">
                                    <span class="badge badge-info">En curso</span>
                                </template>
                                <template v-else-if="props.row.estado == null">
                                    <span class="badge badge-secondary">Vacio</span>
                                </template> -->
                <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
              </div>
              <!-- <div slot="actions" slot-scope="props">
                                <button class="p-0 m-0 h5 btn btn-link" @click="ficha(props.row.id)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div> -->
              <div slot="contrato" slot-scope="props">
                <template v-if="props.row.contrato == '1'"> Si </template>
                <template v-else> No </template>
              </div>
            </v-server-table>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal fade"
      id="ModalFicha"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ficha</h5>
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
              <object
                class="embed-responsive-item"
                type="application/pdf"
                :data="url"
                allowfullscreen
                width="100%"
                height="500"
                style="height: 85vh"
              ></object>
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
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="ModalDeshabilitar"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              Deshabilitar Docentes
            </h5>
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
            <div class="container-fluid text-center">
              <b>¿Está seguro que desea deshabilitar TODOS los expedientes?</b>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-danger"
              data-dismiss="modal"
              @click="deshabilitarBtn"
            >
              Deshabilitar
            </button>
          </div>
        </div>
      </div>
    </div>
    <form @submit.prevent="submit">
      <div
        class="modal fade"
        id="ModalHabilitar"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">
                Habilitar Docentes
              </h5>
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
                  <div class="col-md-6">
                    <h6>Seleccione los documentos requeridos</h6>
                    <div v-for="(d, i) in documentos" class="row">
                      <div class="form-group col-12">
                        <div class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            v-model="fields.tipo_documentos"
                            :value="d.id"
                            :id="'customCheck' + d.id"
                          />
                          <label
                            class="custom-control-label"
                            :for="'customCheck' + d.id"
                            >{{ d.denominacion }}</label
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6>Seleccione los meses por tramite</h6>
                    <div v-for="(m, i) in meses" class="row">
                      <div class="form-group col-12">
                        <div class="custom-control custom-checkbox">
                          <input
                            type="checkbox"
                            class="custom-control-input"
                            v-model="fields.meses"
                            :value="m.id"
                            :id="'customMes' + m.id"
                          />
                          <label
                            class="custom-control-label"
                            :for="'customMes' + m.id"
                            >{{ m.denominacion }}</label
                          >
                        </div>
                      </div>
                    </div>
                    <h6>Escriba alguna indicación</h6>
                    <textarea rows="4" cols="50" v-model="fields.mensaje" />
                  </div>
                </div>

                <div class="row">
                  <div v-if="errors && errors.area" class="text-danger">
                    {{ errors.area[0] }}
                  </div>
                  <div v-if="errors && errors.turno" class="text-danger">
                    {{ errors.turno[0] }}
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-12">
                    <div v-if="errors && errors.checks" class="text-danger">
                      {{ errors.checks[0] }}
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
var area = 0;
var turno = 0;
var sede = 0;
export default {
  props: ["permissions"],
  data() {
    return {
      url: "",
      errors: {},
      todo: false,
      fields: { checks: [], tipo_documentos: [], meses: [], mensaje: "" },
      condicion: "",
      contrato: "",
      checks: [],
      //*********** */
      local: 0,
      locales: [],
      grupo: "",
      grupos: [],
      ///table//
      columns: [
        "select",
        "id",
        "dni",
        "paterno",
        "materno",
        "nombres",
        // "celular",
        // "email",
        "condicion",
        "periodo",
        "estado",
        "contrato",
      ],
      options: {
        perPage: 50,
        perPageValues: [50, 100],
        headings: {
          select: "",
          id: "ID",
          dni: "DNI",
          paterno: "Paterno",
          materno: "Materno",
          nombres: "Nombres",
          //   celular: "Celular",
          //   email: "Email",
          condicion: "Condicion",
          periodo: "Periodo",
          estado: "Estado",
          contrato: "Contrato",
          // actions: "Acciones"
        },
        sortable: ["id", "correlativo", "cantidad_inscrito"],
        filterable: ["dni", "paterno", "materno", "nombres"],
        // listColumns: {
        //     estado: [{
        //             id: "1",
        //             text: 'Inscrito'
        //         },
        //         {
        //             id: "0",
        //             text: 'Pre Inscrito'
        //         }

        //     ]
        // },
        // customFilters: ['correlativo','num_mat']
        filterByColumn: true,
      },
      documentos: [],
      meses: [],
      dni: "",
    };
  },

  methods: {
    buscarDocenteDni: function () {
      this.$refs.table.setCustomFilters({ dni: this.dni });
    },
    submit: function () {
      // console.log("hols");
      $(".loader").show();
      this.fields.checks = this.checks;
      // console.log(this.fields);
      axios
        .post(
          "/intranet/recursos-humanos/pagos/docentes/habilitacion/guardar",
          this.fields
        )
        .then((response) => {
          $(".loader").hide();
          if (response.data.status) {
            this.$refs.table.refresh();
            toastr.success(response.data.message);

            $("#ModalHabilitar").modal("hide");
            this.checks = [];
            this.todo = false;
            // window.location.replace(response.data.url);
          } else {
            toastr.warning(response.data.message, "Aviso");
          }
          // $(".loader").hide();
        })
        .catch((error) => {
          $(".loader").hide();
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
          }
        });
    },
    habilitar: function () {
      if (this.checks.length) {
        $("#ModalHabilitar").modal("show");
      } else {
        var aviso = "<ul>";
        aviso += "<li>Seleccionar como mínimo un docente.</li>";
        toastr.warning(aviso, "Aviso");
      }
    },
    deshabilitar: function () {
      $("#ModalDeshabilitar").modal("show");
    },
    deshabilitarBtn: function () {
      $(".loader").show();
      axios
        .post(
          "/intranet/recursos-humanos/pagos/docentes/habilitacion/deshabilitar"
        )
        .then((response) => {
          $(".loader").hide();
          if (response.data.status) {
            this.$refs.table.refresh();
            toastr.success(response.data.message);

            // $("#ModalHabilitar").modal("hide");
            // this.checks = [];
            // this.todo = false;
            // window.location.replace(response.data.url);
          } else {
            toastr.warning(response.data.message, "Aviso");
          }
          // $(".loader").hide();
        })
        .catch((error) => {
          $(".loader").hide();
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
          }
        });
    },
    ficha: function (id) {
      axios.get("/intranet/encrypt/" + id).then((response) => {
        this.url = "estudiantes/pdf/" + response.data;
      });
      // console.log("hgols");
      $("#ModalFicha").modal("show");
    },
    seleccionarTodo: function (event) {
      if (event.target.checked) {
        // console.log("SEleccionar todo");
        this.checks = [];
        var datos = this.$refs.table.data;
        // datos.forEach(function(i,val){
        //     this.checks.push(val.id);
        // });
        // console.log(datos)
        for (let value of datos) {
          console.log(value);
          if (
            !value.expediente.find((e) => {
              return e.estado == "0";
            })
          ) {
            this.checks.push(value.id);
          }

          // console.log(value);
        }
        // $('.tipo_descuento').prop('checked',false);
        // event.path[0].checked = true;
        // this.fields.tipo_descuento = event.target.value;
        // this.statusDescuento = true;
      } else {
        // console.log("deselccionar");
        this.checks = [];
        // this.fields.tipo_descuento = "1";
        // this.statusDescuento = false;
      }
      // console.log(this.$refs.table.data);
    },
    changeCondicion: function () {
      this.$refs.table.setCustomFilters({ condicion: this.condicion });
    },
    changeContrato: function () {
      this.$refs.table.setCustomFilters({ contrato: this.contrato });
    },
    getDocumentos: function () {
      axios.get("/intranet/get-documentos").then((response) => {
        this.documentos = response.data;
      });
    },
    getMeses: function () {
      axios.get("/intranet/get-meses").then((response) => {
        this.meses = response.data;
      });
    },
  },

  mounted: function () {
    this.getDocumentos();
    this.getMeses();
  },
};
</script>

<style></style>
