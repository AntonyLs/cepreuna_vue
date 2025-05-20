<template>
    <div>
        <div class="row">
            <div class="form-group mb-0 col-2">
                <label for="area">Periodos</label>
                <select class="form-control" v-model="periodo" @change="changePeriodo">
                    <option value="">--Seleccionar--</option>
                    <option v-for="periodo in periodos" :value="periodo.id" :key="periodo.id"> {{ periodo.inicio_ciclo }} - {{ periodo.fin_ciclo }} </option>
                </select>
            </div>
        </div>
        <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/recursos-humanos/pagos/docentes/horas-mes/lista/data">
            <div slot="actions" slot-scope="props">
                <button type="button" class="p-0 m-0 h5 btn btn-link text-info" @click="validarHoras(props.row.id)">
                    <i class="fas fa-file-signature"></i>
                </button>
            </div>
            <div slot="apto" slot-scope="props">
                <template v-if="props.row.apto == '1'">
                    <span class="badge badge-success">SI</span>
                </template>
                <template v-else>
                    <span class="badge badge-danger">NO</span>
                </template>
            </div>
            <div slot="periodo" slot-scope="props">{{ props.row.periodo.inicio_ciclo }} - {{ props.row.periodo.fin_ciclo }}</div>
        </v-server-table>
        <!-- Modal  Editar de Inscripcion-->
        <form @submit.prevent="submit">
            <div class="modal fade" id="modalcalificarDocente" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="exampleModalLabel">HORAS DOCENTE</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body bg-gray-100">
                            <div class="container p-0">
                                <div class="row">
                                    <div class="col-md-12">
                                        {{ this.fields }}
                                        <div class="card shadow-sm rounded">
                                            <div class="card-header">
                                                <b>¿Tiene Contrato?</b>
                                            </div>
                                            <div class="card-body py-2 flex flex-row">
                                                <input type="radio" id="c1" name="contrato" value="No" v-model="fields.contrato" />
                                                <label for="c1">No</label>
                                                <input type="radio" id="c2" name="contrato" value="Si" v-model="fields.contrato" />
                                                <label for="c2">Si</label>
                                            </div>
                                        </div>
                                        <div class="card shadow-sm rounded" v-for="(mes, m) in meses" :key="mes.id">
                                            <div class="card-header">
                                                <b>{{ mes.denominacion }}</b>
                                                <button type="button" class="btn btn-link float-right" @click="agregar(m, mes.id)"><i class="fas fa-plus"></i> Añadir</button>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row border-bottom" v-for="(tipo, t) in mes.tipos" :key="t">
                                                    <div class="form-group col-md-4">
                                                        <label for="puntaje">Tipo</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend"></div>
                                                            <!-- <input type="number" class="form-control" id="puntaje"  min="0" required /> -->
                                                            <select id="" class="form-control" v-model="fields.tipo[m][t]">
                                                                <option :value="tipo.id" v-for="tipo in tipos" :key="tipo.id">
                                                                    {{ tipo.denominacion }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="puntaje">Cantidad</label>
                                                        <div class="input-group input-group-sm">
                                                            <div class="input-group-prepend"></div>
                                                            <input type="number" class="form-control" id="puntaje" v-model="fields.cantidad[m][t]" min="0" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger btn-sm mt-4" @click="eliminar(m, t)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-success">
                                                Guardar
                                            </button>
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
    props: ["tipos"],
    data() {
        return {
            id: 0,
            errors: {},
            fields: {
                tipo: [],
                cantidad: [],
                mes: [],
                contrato: null
            },
            meses: [[]],
            calificado: false,
            periodos: [],
            periodo: "",
            ///table//
            columns: ["id", "dni", "paterno", "materno", "nombres", "condicion", "direccion", "celular", "email", "periodo", "actions"],
            options: {
                headings: {
                    id: "id",
                    dni: "Documento",
                    paterno: "Apellido Paterno",
                    materno: "Apellido Materno",
                    nombres: "Nombres",
                    condicion: "Condicion",
                    direccion: "Dirección",
                    celular: "Celular",
                    email: "Email",
                    periodo: "Periodo",
                    actions: "Acciones"
                },
                sortable: ["id", "dni", "paterno", "materno", "nombres", "condicion"],
                filterable: ["dni", "paterno", "materno", "nombres", "condicion"],
                listColumns: {
                    condicion: [
                        {
                            id: "1",
                            text: "Particular"
                        },
                        {
                            id: "2",
                            text: "Unap"
                        }
                    ]
                },
                filterByColumn: true
            }
        };
    },

    methods: {
        submit() {
            console.log("Datos enviados: ", this.fields);
            $(".loader").show();
            if (this.calificado) {
                axios
                    .put("/intranet/recursos-humanos/pagos/docentes/horas-mes/" + this.fields.id, this.fields)
                    .then(response => {
                        if (response.data.status) {
                            // this.editCursos = false;
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            // this.infoInscripcion.apto = this.fieldsCalificar.apto;
                            // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                            $("#modalcalificarDocente").modal("hide");
                            $("#modalcalificarDocente").data("bs.modal", null);
                            // $(".modal-backdrop").remove();
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
            } else {
                axios
                    .post("/intranet/recursos-humanos/pagos/docentes/horas-mes", this.fields)
                    .then(response => {
                        if (response.data.status) {
                            // this.editCursos = false;
                            this.$refs.table.refresh();
                            toastr.success(response.data.message);
                            // this.infoInscripcion.apto = this.fieldsCalificar.apto;
                            // this.infoInscripcion.puntaje = this.fieldsCalificar.puntaje;
                            $("#modalcalificarDocente").modal("hide");
                            $("#modalcalificarDocente").data("bs.modal", null);
                            // $(".modal-backdrop").remove();
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
            }
        },
        validarHoras: function(id) {
            this.fields.id = id;
            axios.get("/intranet/recursos-humanos/pagos/get-mes-hora-docente/" + id).then(response => {
                // console.log(response.data);
                this.meses = response.data.data;
                this.calificado = response.data.status;
                //   this.fields.cantidad = response.data.cantidad;
                for (let index = 0; index < this.meses.length; index++) {
                    this.fields.tipo[index] = new Array();
                    this.fields.cantidad[index] = new Array();
                    this.fields.mes[index] = new Array();

                    for (let i = 0; i < this.meses[index].tipos.length; i++) {
                        this.fields.tipo[index][i] = this.meses[index].tipos[i].tipo_pago_id;
                        this.fields.cantidad[index][i] = this.meses[index].tipos[i].cantidad;
                        this.fields.mes[index][i] = this.meses[index].tipos[i].mes_id;
                    }
                }
                // this.allCursos = cursos;
            });

            $("#modalcalificarDocente").modal("show");
        },
        changePeriodo: function() {
            this.$refs.table.setCustomFilters({
                // sede: this.sede,
                periodo: this.periodo
                // turno: this.turno,
                // tipo_descuento: this.tipo_descuento,
                // estado: this.estado
            });
        },
        getAreas: function() {
            axios.get("/intranet/recursos-humanos/pagos/get-periodos").then(response => {
                this.periodos = response.data;
            });
        },
        agregar: function(id, mes) {
            this.meses[id].tipos.push({ tipo: 1, cantidad: 0 });

            // Asegúrate de que los arrays correspondientes se actualicen
            this.fields.mes[id].push(mes); // mes agregado
            this.fields.tipo[id].push(1); // tipo agregado
            this.fields.cantidad[id].push(0); // cantidad inicial agregada
        },
        eliminar: function(m, t) {
            console.log(m, t);
            this.meses[m].tipos.splice(t, 1);
            this.fields.tipo[m].splice(t, 1);
            this.fields.cantidad[m].splice(t, 1);
            this.fields.mes[m].splice(t, 1);
        }
    },
    mounted() {
        this.getAreas();
    }
};
</script>

<style></style>
