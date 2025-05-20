<template>
  <div>
    <div class="row">
      <div class="form-group mb-0 col-12">
        <label for="area">Evaluacion de Documentos Docente</label>
      </div>
    </div>
    <div class="row mb-2">
      <div class="col-4">
        <div class="row">
          <div class="form-group mb-0 col-12">
            <label for="">Estado</label>
            <select
              class="form-control"
              v-model="estado"
              @change="changeEstado"
            >
              <option value="">--Seleccionar--</option>
              <option value="0">Enviado</option>
              <option value="1">Pendiente</option>
              <option value="2">Observado</option>
              <option value="3">Aprobado</option>
              <!-- <option value="4">Derivado</option> -->
            </select>
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="row">
          <div class="form-group mb-0 col-12">
            <label for="">Periodo</label>
            <select
              class="form-control"
              v-model="periodo"
              @change="changePeriodo"
            >
              <option value="">--Seleccionar--</option>
              <option v-for="p in periodos" :value="p.periodo">
                {{ p.periodo }}
              </option>
              <!-- <option value="4">Derivado</option> -->
            </select>
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="row">
          <div class="form-group mb-0 col-12">
            <label for="">Responsable</label>
            <select
              class="form-control"
              v-model="responsable"
              @change="changeResponsable"
            >
              <option value="">--Seleccionar--</option>
              <option v-for="r in responsables" :value="r.user_id">
                {{ r.user_names }}
              </option>
              <!-- <option value="4">Derivado</option> -->
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-2">
      <excel-export
        class="btn btn-success"
        :data="json_data"
        :columns="json_fields"
        :filename="'ReporteExpedientes'"
        :sheetname="'Reporte'"
      >
        Descargar excel
      </excel-export>
    </div>
    <v-server-table
      ref="table"
      :columns="columns"
      :options="options"
      url="/intranet/recursos-humanos/pagos/docentes/expedientes/lista/data"
    >
      <div slot="actions" slot-scope="props">
        <button
          v-if="props.row.estado == '1'"
          type="button"
          class="p-0 m-0 h5 btn btn-link text-info"
          @click="calificarDocente(props.row.id)"
          data-toggle="modal"
          data-target="#modalcalificarDocente"
        >
          <i class="fas fa-file-signature"></i>
        </button>
      </div>
      <div slot="estado" slot-scope="props">
        <template v-if="props.row.estado == '0'">
          <span class="badge badge-info">Enviado</span>
        </template>
        <template v-else-if="props.row.estado == '1'">
          <span class="badge badge-secondary">Pendiente</span>
        </template>
        <template v-else-if="props.row.estado == '2'">
          <span class="badge badge-danger">Observado</span>
        </template>
        <template v-else-if="props.row.estado == '3'">
          <span class="badge badge-success">Aprobado</span>
        </template>
        <template v-else>
          <span class="badge badge-warning">Derivado</span>
        </template>
        <!-- <a href="#" @click="detalles(props.row.id)"><i class="fa fa-folder big-icon text-success" aria-hidden="true"></i></a> -->
      </div>
      <div slot="user_id" slot-scope="props">
        <template>
          {{
            users.find((u) => {
              return u.id == props.row.user_id;
            }).name
          }}
        </template>
      </div>
    </v-server-table>

    <!-- Modal  Editar de Inscripcion-->
    <form @submit.prevent="submit">
      <div
        class="modal fade"
        id="modalcalificarDocente"
        data-backdrop="static"
        data-keyboard="false"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header bg-info text-white">
              <h5 class="modal-title" id="exampleModalLabel">
                Evaluación Pago Docente
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

            <div class="modal-body bg-gray-100">
              <div class="container p-0">
                <div class="row">
                  <div class="col-md-6">
                    <div class="card mb-2 shadow-sm rounded">
                      <div class="card-body py-2">
                        <template>
                          <div class="row" v-for="data in documentosTramite">
                            <div class="form-group col-12">
                              <label for="puntaje"
                                >{{ data.tipo_documento }}
                                <a
                                  class="btn btn-info btn-sm text-white"
                                  @click="verDocumento(data.id)"
                                  >Ver Documento</a
                                ></label
                              >
                              <div
                                v-if="data.estado_documento == '1'"
                                class="alert alert-success"
                                role="alert"
                              >
                                APROBADO
                              </div>
                              <div
                                v-else-if="data.estado_documento == '2'"
                                class="alert alert-danger"
                                role="alert"
                              >
                                {{ data.estado_observacion }}
                              </div>
                              <div class="input-group">
                                <div class="form-check form-check-inline">
                                  <input
                                    class="form-check-input"
                                    type="radio"
                                    id="inlineCheckbox1"
                                    v-model="
                                      fields.estado[data.tipo_documento_id]
                                    "
                                    value="1"
                                  />
                                  <label
                                    class="form-check-label"
                                    for="inlineCheckbox1"
                                    >Validar</label
                                  >
                                </div>
                                <div class="form-check form-check-inline">
                                  <input
                                    class="form-check-input"
                                    type="radio"
                                    id="inlineCheckbox2"
                                    v-model="
                                      fields.estado[data.tipo_documento_id]
                                    "
                                    value="2"
                                  />
                                  <label
                                    class="form-check-label"
                                    for="inlineCheckbox2"
                                    >Observar</label
                                  >
                                </div>
                              </div>

                              <div class="input-group">
                                <textarea
                                  rows="2"
                                  class="form-control"
                                  id="observacion"
                                  v-model="
                                    fields.observacion[data.tipo_documento_id]
                                  "
                                />
                                <!-- <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3"> -->
                              </div>
                              <div
                                v-if="errors && errors.puntaje"
                                class="text-danger"
                              >
                                {{ errors.puntaje[0] }}
                              </div>
                            </div>
                          </div>
                        </template>
                        <!-- <template v-else>
                                                    <div class="row" v-for="criterio in criterios">
                                                        <div class="form-group col-12">
                                                            <label for="puntaje">{{ criterio.denominacion }}</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" id="basic-addon3"> 0 de a {{ criterio.puntaje }}</span>
                                                                </div>
                                                                <input
                                                                    type="number"
                                                                    class="form-control"
                                                                    id="puntaje"
                                                                    v-model="fields.puntaje[criterio.id]"
                                                                    :max="criterio.puntaje"
                                                                    min="0"
                                                                    value="0"
                                                                    required
                                                                />
                                                            </div>
                                                            <div v-if="errors && errors.puntaje" class="text-danger">
                                                                {{ errors.puntaje[0] }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template> -->
                      </div>
                      <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                          Guardar
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card mb-1 shadow-sm rounded">
                      <div class="card-header text-center py-1">
                        <b class="card-title mb-0">Visualizar Documento</b>
                      </div>
                      <div class="card-body py-2">
                        <object
                          class="embed-responsive-item"
                          type="application/pdf"
                          :data="url"
                          allowfullscreen
                          width="100%"
                          height="600"
                          style="height: 85vh"
                        ></object>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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
  props: ["permissions", "external_url", "users"],
  data() {
    return {
      url: "",
      errors: {},
      calificar: false,
      fields: {
        observacion: [],
        estado: [],
      },
      criterios: [],
      calificado: false,
      areas: [],
      area: "",
      ///table//
      columns: [
        "id",
        "periodo",
        "dni",
        "paterno",
        "materno",
        "nombres",
        "celular",
        "email",
        "horas",
        "fecha_inicio",
        "fecha_fin",
        "oficina",
        "estado",
        "user_id",
        "actions",
      ],
      options: {
        headings: {
          id: "ID",
          periodo: "Periodo",
          dni: "DNI",
          paterno: "Paterno",
          materno: "Materno",
          nombres: "Nombres",
          celular: "Celular",
          email: "Email",
          horas: "Horas",
          fecha_inicio: "Inicio",
          fecha_fin: "Fin",
          oficina: "Oficina",
          estado: "Estado",
          user_id: "Responsable",
        },
        sortable: ["id"],
        filterable: ["dni", "paterno", "materno", "nombres"],
        filterByColumn: true,
      },
      grado: [],
      experiencia: [],
      capacitacion: [],
      produccion: [],
      json_fields: [
        {
          label: "id",
          field: "expediente_id",
        },
        {
          label: "Fecha Inicio",
          field: "fecha_inicio",
        },
        {
          label: "Fecha Fin",
          field: "fecha_fin",
        },
        {
          label: "Total Horas",
          field: "cantidad_horas",
        },
        {
          label: "Estado",
          field: "tramite_estado",
        },
        {
          label: "Oficina",
          field: "oficina",
        },
        {
          label: "DNI",
          field: "docente_dni",
        },
        {
          label: "Apellido Paterno",
          field: "apellido_paterno",
        },
        {
          label: "Apellido Materno",
          field: "apellido_materno",
        },
        {
          label: "Nombres",
          field: "nombres",
        },
        {
          label: "Periodo",
          field: "periodo",
        },
        {
          label: "Celular",
          field: "celular",
        },
        {
          label: "Correo",
          field: "email",
        },
        {
          label: "Responsable",
          field: "user_names",
        },
      ],
      json_data: [],
      documentosTramite: [],
      url: "",
      estado: "",
      periodos: [],
      periodo: "",
      responsables: [],
      responsable: "",
    };
  },

  methods: {
    ficha: function (id) {
      axios.get("/intranet/encrypt/" + id).then((response) => {
        this.url = "docentes/pdf/" + response.data;
      });
      // console.log("hgols");
      $("#ModalFicha").modal("show");
    },
    submit() {
      $(".loader").show();
      axios
        .post(
          "/intranet/recursos-humanos/pagos/docentes/evaluar-docente",
          this.fields
        )
        .then((response) => {
          if (response.data.status) {
            // this.editCursos = false;
            this.$refs.table.refresh();
            toastr.success(response.data.message);
            // this.infoInscripcion.apto = this.fieldsCalificar.apto;
            // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
            $("#modalcalificarDocente").modal("hide");
            $("#modalcalificarDocente").data("bs.modal", null);
            $(".modal-backdrop").remove();
          } else {
            toastr.warning(response.data.message, "Aviso");
          }
          $(".loader").hide();
        })
        .catch((error) => {
          $(".loader").hide();
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {};
          }
        });
    },
    calificarDocente: function (id) {
      this.fields.id = id;
      axios
        .get(
          "/intranet/recursos-humanos/pagos/get-documentos-expediente-docente/" +
            id
        )
        .then((response) => {
          this.documentosTramite = response.data;
        });
      $("#modalcalificarDocente").modal("show");
    },
    formatApto: function (value) {
      if (value == "1") {
        return "Si";
      }
      return "No";
    },
    changeArea: function () {
      this.getDataExcel();
      this.$refs.table.setCustomFilters({
        // sede: this.sede,
        area: this.area,
        // turno: this.turno,
        // tipo_descuento: this.tipo_descuento,
        // estado: this.estado
      });
    },
    getAreas: function () {
      axios.get("/intranet/get-areas").then((response) => {
        this.areas = response.data;
      });
    },
    getDataExcel: function () {
      axios
        .get("/intranet/recursos-humanos/pagos/expedientes-excel", {
          params: {
            all: true,
            periodo: this.periodo,
            estado: this.estado,
            responsable: this.responsable,
          },
        })
        .then((response) => {
          // this.url = "docentes/pdf/"+response.data;
          this.json_data = response.data;
          // console.log(response.data);
        });
    },
    verDocumento: function (id) {
      // console.log(path);
      // this.url = this.external_url + "/storage/docentes/" + path;
      this.url = this.external_url + "/tramite-pago/show-document/" + id;
    },
    // getCriterios:function(){
    //     axios.get('/intranet/get-criterio-docente-inscripcion')
    //     .then(function (response) {
    //         this.criterios = response.data;
    //     }.bind(this));
    // }
    changeEstado: function () {
      this.getDataExcel();
      this.$refs.table.setCustomFilters({
        estado: this.estado,
      });
    },
    getPeriodos: function () {
      axios
        .get("/intranet/recursos-humanos/pagos/get-periodos-filter")
        .then((response) => {
          // console.log(response.data);
          this.periodos = response.data;
        });
    },
    changePeriodo: function () {
      this.getDataExcel();
      this.$refs.table.setCustomFilters({
        periodo: this.periodo,
      });
    },
    getResponsables: function () {
      axios
        .get("/intranet/recursos-humanos/pagos/get-responsables")
        .then((response) => {
          // console.log(response.data);
          this.responsables = response.data;
        });
    },
    changeResponsable: function () {
      this.getDataExcel();
      this.$refs.table.setCustomFilters({
        responsable: this.responsable,
      });
    },
  },
  mounted() {
    this.getAreas();
    this.getDataExcel();
    this.getPeriodos();
    this.getResponsables();
    // axios.get("/intranet/inscripcion/calificacion-docente/lista/data?all=true").then(response => {
    //     // this.url = "docentes/pdf/"+response.data;
    //     this.json_data = response.data;
    //     // console.log(response.data);
    // });
  },
};
</script>

<style></style>
