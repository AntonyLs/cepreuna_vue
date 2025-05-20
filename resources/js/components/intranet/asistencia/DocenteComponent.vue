<template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div>
            <div class="row">
                <div class="form-group mb-0 col-4">
                    <label for="grupo">Fecha</label>
                    <input type="date" class="form-control" v-model="fecha" @change="changeFecha()" />
                    <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                </div>
                <div class="form-group mb-0 col-md-4">
                    <label for="sede">Sede</label>
                    <select class="form-control" v-model="sede" @change="changeSede">
                        <option value="">--Seleccionar--</option>
                        <option v-for="sede in sedes" :value="sede.id" :key="sede.id">{{sede.denominacion}}</option>
                    </select>
                </div>
                <div class="form-group mb-0 col-md-4">
                    <label for="turno">Grupo</label>
                    <select class="form-control" v-model="grupo" @change="changeGrupo">
                        <option value="">--Seleccionar--</option>
                        <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo.denominacion }}</option>
                    </select>
                </div>
            </div>

            <v-server-table ref="table" :columns="columns" :options="options" url="/intranet/asistencia/docente/lista/data">
                <div slot="actions" slot-scope="props">
                    <button type="button" class="p-0 m-0 h5 btn btn-link text-danger" @click="eliminarAsistencia(props.row.id)" data-toggle="modal" data-target="#modalEditarInscripcion">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div slot="fecha" slot-scope="props">
                    <fecha-format :fecha="props.row.fecha" :format="'DD/MM/YYYY'"></fecha-format>
                </div>
                <div slot="hora" slot-scope="props">
                    <hora-format :hora="props.row.hora_inicio" :format="'hh:mm A'"></hora-format> -
                    <hora-format :hora="props.row.hora_fin" :format="'hh:mm A'"></hora-format>
                </div>
                <div slot="estado" slot-scope="props">
                    <template v-if="props.row.estado == '1'">
                        <span class="badge badge-success">Presente</span>
                    </template>
                    <template v-else-if="props.row.estado == '2'">
                        <span class="badge badge-warning">Tarde</span>
                    </template>
                    <template v-else-if="props.row.estado == '3'">
                        <span class="badge badge-danger">Falta</span>
                    </template>
                </div>
            </v-server-table>
        </div>

    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import toastr from "toastr";
import "vue-select/dist/vue-select.css";

export default {
    props: ["permissions"],
    data() {
        return {
            fecha: "",
            sedes: [],
            sede: '',
            grupo: "",
            grupos: [],
            ///table//
            columns: ["id", "fecha", "hora","cantidad_horas",'horas_pago','estado', "docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres","carga.grupo_aula.aula.local.sede.denominacion","carga.grupo.denominacion","carga.curso.denominacion", "actions"],
            options: {
                headings: {
                    id: "id",
                    fecha: "fecha",
                    hora: "hora",
                    cantidad_horas: "Hrs Carga",
                    horas_pagos: "Hrs Pagar",
                    estado: "estado",
                    "docente.nro_documento": "Documento",
                    "docente.paterno": "Apellido Paterno",
                    "docente.materno": "Apellido Materno",
                    "docente.nombres": "Nombres",
                    "carga.grupo_aula.aula.local.sede.denominacion": "Sede",
                    "carga.grupo.denominacion": "Grupo",
                    "carga.curso.denominacion": "Curso",
                    actions: "Acciones"

                },
                sortable: ["id", "apto", "puntaje"],
                filterable: ["docente.nro_documento", "docente.paterno", "docente.materno", "docente.nombres"],
                filterByColumn: true
            }
        };
    },

    methods: {
        eliminarAsistencia: function(value){
            this.$confirm({
                title: "Alerta",
                message: "¿Esta seguro de eliminar la asistencia?",
                button: {
                    no: "NO",
                    yes: "SI"
                },
                callback: confirm => {
                    if (confirm) {
                        $(".loader").show();
                        axios
                            .delete("docente/" + value)
                            .then(response => {
                                $(".loader").hide();
                                console.log(response.data);
                                if (response.data.status) {
                                    this.$refs.table.refresh();
                                    toastr.success(response.data.message);
                                } else {
                                    toastr.warning(response.data.message, "Aviso");
                                }
                            })
                            .catch(error => {
                                $(".loader").hide();
                                toastr.warning("error al eliminar, intentelo de nuevo", "Aviso");
                            });
                    }
                }
            });
        },
        changeFecha: function() {
            this.$refs.table.setCustomFilters({ grupo: this.grupo, sede:this.sede , fecha: this.fecha });
        },
        changeGrupo: function() {
            this.$refs.table.setCustomFilters({ grupo: this.grupo, sede:this.sede , fecha: this.fecha });
        },
        getSedes:function(){
            axios.get("/intranet/get-sedes",{
                params: {
                    all: true
                },
            }).then(response => {
                this.sedes = response.data;
            });
        },
        changeSede:function(){
            this.checks = [];
            this.todo = [];
            this.$refs.table.setCustomFilters({ grupo: this.grupo, sede:this.sede, fecha: this.fecha });
            this.getGrupos();
        },
        getGrupos: function() {
            axios
                .get("/intranet/get-grupo-aulas", {
                    params: {
                        area: this.area,
                        turno: this.turno,
                        sede: this.sede,
                    }
                })
                .then(response => {
                    // this.grupo = response.data[0].id;
                    this.grupos = response.data;
                });
        },
    },
    mounted() {
        this.getGrupos();
        this.getSedes();

    }
};
</script>

<style></style>
