<template>
    <div class="card mb-2 shadow-sm rounded">
        <div class="card-header text-white text-center bg-warning py-1"><b class="card-title mb-0">Editar Datos de Inscripción</b></div>
        <div class="card-body py-2">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <form class="" action="" @submit.prevent="submit">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="area">Área de estudios</label>
                                <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion" @input="changeArea"></v-select>
                                <div v-if="errors && errors.area" class="text-danger">{{ errors.area[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="turno">Turno de estudios</label>
                                <v-select v-model="fields.turno" :options="turnos" :reduce="turno => turno.id" label="denominacion" @input="changeTurno"></v-select>
                                <div v-if="errors && errors.turno" class="text-danger">{{ errors.turno[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="turno">Tipo de Descuento</label>
                                <v-select
                                    v-model="fields.tipo_estudiante"
                                    :options="tipo_estudiantes"
                                    :reduce="tipo_estudiante => tipo_estudiante.id"
                                    @input="changeDescuento"
                                    label="denominacion"
                                ></v-select>
                                <div v-if="errors && errors.tipo_estudiante" class="text-danger">{{ errors.tipo_estudiante[0] }}</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="cantidad inscrito">Número de Inscripciones Anteriores</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    min="0"
                                    name="cantidad_inscrito"
                                    id="cantidad_inscrito"
                                    v-model="fields.cantidad_inscrito"
                                    @change="changeCantidad"
                                />
                                <div v-if="errors && errors.cantidad_inscrito" class="text-danger">{{ errors.cantidad_inscrito[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sede">Sede</label>
                                <v-select v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" label="denominacion" @input="changeSede"></v-select>
                                <div v-if="errors && errors.sede" class="text-danger">{{ errors.sede[0] }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="sexo">Estado</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" v-model="fields.estado" @change="changeEstado" name="estado" id="estado" value="1" />
                                                INSCRITO
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" v-model="fields.estado" @change="changeEstado" name="estado" id="estado" value="0" />
                                                PREINSCRITO
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br />
                        <div class="text-center">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import toastr from "toastr";
import $ from "jquery";
export default {
    data() {
        return {
            fields: {
                area: "",
                cantidad_inscrito: "",
                estado: "",
                turno: "",
                tipo_estudiante: "",
                sede: ""
            },
            fieldsEdit: {
                cantidad_inscrito: "",
                estado: ""
            },
            errors: {},
            result: {
                pago: []
            },
            errorPago: null,
            selectAdjunto: "Selecione",
            sedes: [],
            areas: [],
            turnos: [],
            tipo_estudiantes: [
                {
                    denominacion: "Normal (sin descuento)",
                    id: 1
                },
                {
                    denominacion: "Descuento por Hijo de trabajador",
                    id: 2
                },
                {
                    denominacion: "Descuento por planilla",
                    id: 3
                },
                {
                    denominacion: "Descuento por hermanos",
                    id: 4
                },
                {
                    denominacion: "Descuento por Resolucion Rectoral",
                    id: 5
                },
                {
                    denominacion: "Descuento Servicio Militar",
                    id: 6
                }
            ]
            // inscripcionFields:[],
        };
    },
    props: ["inscripcion"],
    methods: {
        submit() {
            $(".loader").show();
            axios
                .put("estudiante/" + this.inscripcion.id, this.fieldsEdit)
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.$emit("result", this.fieldsEdit);
                        toastr.success(response.data.message);
                    } else {
                        toastr.warning(response.data.message, "Aviso");
                    }
                })
                .catch(error => {
                    $(".loader").hide();
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        filesChange(e) {
            let file = e.target.files[0];
            this.selectAdjunto = file.name;
            this.fields.file = file;
        },
        getSedes: function() {
            axios.get("../get-sedes").then(
                function(response) {
                    this.sedes = response.data;
                }.bind(this)
            );
        },
        getAreas: function() {
            axios.get("../get-areas").then(
                function(response) {
                    this.areas = response.data;
                }.bind(this)
            );
        },
        getTurnos: function() {
            axios.get("../get-turnos").then(
                function(response) {
                    this.turnos = response.data;
                }.bind(this)
            );
        },
        changeArea(area) {
            this.fieldsEdit.areas_id = area;
        },
        changeTurno(turno) {
            this.fieldsEdit.turnos_id = turno;
        },
        changeDescuento(descuento) {
            this.fieldsEdit.tipo_estudiante = descuento;
        },
        changeCantidad() {
            this.fieldsEdit.cantidad_inscrito = this.fields.cantidad_inscrito;
        },
        changeSede() {
            this.fieldsEdit.sedes_id = this.fields.sede;

            if (this.fields.sede != 1) {
                this.fieldsEdit.modalidad = "2";
            } else {
                this.fieldsEdit.modalidad = "1";
            }
        },
        changeEstado(descuento) {
            this.fieldsEdit.estado = this.fields.estado;
        }
    },
    mounted() {
        this.getAreas();
        this.getTurnos();
        this.getSedes();
        this.fields.area = this.inscripcion.area;
        this.fields.cantidad_inscrito = this.inscripcion.cantidad_inscrito;
        this.fields.estado = this.inscripcion.estado;
        this.fields.turno = this.inscripcion.turno;
        this.fields.tipo_estudiante = this.inscripcion.tipo_estudiante;

        this.fieldsEdit.cantidad_inscrito = this.inscripcion.cantidad_inscrito;
        this.fieldsEdit.estado = this.inscripcion.estado;
        // this.inscripcionFields = this.inscripcion;

        console.log("Component mounted.");

        // this.fields.documento = this.documento;
    }
};
</script>
