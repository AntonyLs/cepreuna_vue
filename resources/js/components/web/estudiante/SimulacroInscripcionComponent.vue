<template>
    <form @submit.prevent="submit">
        <!-- {{ estudiante }} -->
        <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Complete los siguientes campos.</p>
        <h5 class="text-secondary">1. Datos Personales</h5>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="nombres">Nombres</label>
                    <input disabled type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" />
                    <div v-if="errors && errors.nombres" class="text-danger">
                        {{ errors.nombres[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="paterno">Apellido Paterno</label>
                    <input disabled type="text" class="form-control" name="paterno" id="paterno" v-model="fields.paterno" />
                    <div v-if="errors && errors.paterno" class="text-danger">
                        {{ errors.paterno[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="materno">Apellido Materno</label>
                    <input disabled type="text" class="form-control" name="materno" id="materno" v-model="fields.materno" />
                    <div v-if="errors && errors.materno" class="text-danger">
                        {{ errors.materno[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input disabled type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="1" />
                                    M
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input disabled type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="2" />
                                    F
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 col-xs-12">
                <div class="form-group">
                    <label for="tipo documento">Tipo de Documento</label>
                    <v-select disabled v-model="fields.tipo_documento" :options="tipoDocumentos" :reduce="tipo_documento => tipo_documento.id" label="denominacion"></v-select>
                    <div v-if="errors && errors.tipo_documento" class="text-danger">
                        {{ errors.tipo_documento[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="nro_documento">Número de Documento</label>
                    <input disabled type="text" class="form-control" name="nro_documento" id="nro_documento" @input="changeDocumento" v-model="fields.nro_documento" />
                    <div v-if="errors && errors.nro_documento" class="text-danger">
                        {{ errors.nro_documento[0] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input disabled type="email" class="form-control" name="email" id="email" v-model="fields.email" />
                    <div v-if="errors && errors.email" class="text-danger">
                        {{ errors.email[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input disabled type="text" class="form-control" name="celular" id="celular" v-model="fields.celular" />
                    <div v-if="errors && errors.celular" class="text-danger">
                        {{ errors.celular[0] }}
                    </div>
                </div>
            </div>
        </div>

        <hr />
        <h5 class="text-secondary">2. Datos Adicionales</h5>
        <br />
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="area">Área de estudio</label>
                    <v-select disabled v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion" @input="changeAreas"></v-select>
                    <div v-if="errors && errors.area" class="text-danger">
                        {{ errors.area[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="sede">Sede</label>
                    <v-select disabled v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" label="denominacion" ></v-select>
                    <div v-if="errors && errors.sede" class="text-danger">
                        {{ errors.sede[0] }}
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 col-xs-12">
                <div class="form-group">
                    <label for="escuelas">Escuela Profesional</label>
                    <v-select disabled v-model="fields.escuela" :options="escuelas" :reduce="escuela => escuela.id" label="denominacion"></v-select>
                    <div v-if="errors && errors.escuela" class="text-danger">
                        {{ errors.escuela[0] }}
                    </div>
                </div>
            </div>
        </div>
        
        <hr />
        <h5 class="text-secondary">4. Pago</h5>
        <div v-if="errors && errors.tokens" class="text-danger">
            {{ errors.tokens[0] }}
        </div>
        
        <p class="text-info">
            <i class="fa fa-info-circle"></i> <b> El monto total a pagar es de 11.00 Soles</b
            >.
        </p>
        <div class="row" >
            <w-pago :documento="fields.nro_documento" @result="resultPago = $event"></w-pago>
            <br />
            <div class="container" style="margin: 14px;">
                <div class="row">
                    <table class="table">
                        <tbody>
                            <tr v-for="result in resultPago.pago" :key="result.secuencia">
                                <td>
                                    <div class="alert alert-secondary" role="alert">
                                        <b>Secuencia</b>: {{ result.secuencia }} | <b>Monto</b>: {{ result.monto }} | <b> fecha</b>:
                                        {{ dateFormat(result.fecha) }}
                                    </div>
                                </td>

                                <td>
                                    <div class="alert alert-success" role="alert">
                                        {{ result.message }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <hr />
        <div class="row" v-if="fields.modalidad == '1' && fields.modalidad">
            <div class="alert alert-info" role="alert">
                <div class="col text-justify">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input disabled type="checkbox" v-model="terminos" class="form-check-input" name="condiciones" id="condiciones0" value="checkedValue" />
                            <b>Declaro bajo Juramento:</b>
                            <p>
                                Que, a la fecha de inscripción,
                                <ul>
                                <li>Cuento con acceso a servicio de internet y equipo de cómputo para seguir mis estudios de preparación pre universitaria en el
                                <b>ciclo virtual</b> actual de CEPREUNA, declaro que será mi responsabilidad el acceso.</li>
                                <li>Haber culminado el quinto de secundaria satisfactoriamente y contar con el certificado de estudios visado por la UGEL o DREP. o ser un estudiante que esté en proceso de finalizar la educación secundaria (Quinto de secundaria).</li>
                                <li>No haber logrado una vacante en los procesos de exámenes de admisión a la Universidad Nacional del Altiplano (Extraordinario 2023, CEPREUNA 2023-I, CEPREUNA 2023-II, GENERAL 2023-I y GENERAL 2023-II.) y otros que determine la Dirección de Admisión en su reglamento para procesos de admisión.</li>
                                <li>No tengo deuda pendiente en el CEPREUNA de ciclos anteriores.</li>
                                </ul> 
                            </p>
                            <p>
                                Autorizo la verificación de lo declarado. En caso de falsedad declaro asumir toda la responsabilidad administrativa.
                            </p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" >
            <div class="alert alert-info" role="alert">
                <div class="col text-justify">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input  type="checkbox" v-model="terminos" class="form-check-input" name="condiciones" id="condiciones0" value="checkedValue" />
                            <b>Declaro bajo Juramento:</b>
                            <p>
                                Que, a la fecha de inscripción.
                            </p>
                            <p>Que la información proporcionada durante el proceso de inscripción y registro para participar del "Simulacro de Examen de Admisión CEPREUNA ciclo SETIEMBRE - DICIEMBRE 2024" es precisa, completa y veraz. Asumo plena responsabilidad por los datos proporcionados y soy consciente de que cualquier falsedad, omisión o información incorrecta, excluye mi participación en el simulacro; en consecuencia, ratifico la veracidad de los datos en la presente declaración jurada. </p>
                            <p>
                                Autorizo la verificación de lo declarado. En caso de falsedad declaro asumir toda la responsabilidad administrativa.
                            </p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <br />
        <div class="text-center">
            <button type="submit" :disabled="!terminos" class="btn btn-primary">
                Registrar Inscripción
            </button>
        </div>
    </form>
</template>

<script>
import "vue-select/dist/vue-select.css";
import toastr from "toastr";

export default {
    props:['estudiante'],
    data() {
        return {
            fields: {
                id: this.estudiante.id,
                sexo: this.estudiante.sexo,
                tokens: [],
                sede: this.estudiante.sedes_id,
                modalidad: null,
                nombres: this.estudiante.nombres,
                paterno: this.estudiante.paterno,
                materno: this.estudiante.materno,
                tipo_documento: this.estudiante.tipo_documentos_id,
                nro_documento: this.estudiante.nro_documento,
                celular: this.estudiante.celular,
                email: this.estudiante.email,
                area: this.estudiante.areas_id,
                escuela: this.estudiante.escuela_id
            },
            errors: {
                file_dni: { 0: "" }
            },
            tipoDocumentos: [],
            sedes: [],
            areas: [],
            escuelas: [],
            statusDocumento: false,
            resultPago: {
                pago: []
            },
            montoPagar: 0,
            terminos: false,
            // sede: 1,
            selectFileDni: "Selecione",
        };
    },
    methods: {
        filesChangeDni(e) {
            this.selectFileDni = "Selecione";
            let file = e.target.files[0];

            if (file === undefined) {
                this.selectFileDni = "Selecione";
            } else {
                if (file.size > 1024 * (1024 * 1)) {
                    this.errors.file_dni[0] = "El tamaño del archivo excede el limite de 1 MB permitido.";
                    this.selectFileDni = "Selecione";
                    this.fields.file_dni = null;
                } else {
                    this.errors.file_dni[0] = "";
                    this.selectFileDni = file.name;
                    this.fields.file_dni = file;
                }
            }
        },
        submit() {
            $(".loader").show();
            // if (this.fields.file_dni) {
            this.errors = { file_dni: { 0: "" } };
            // }

            let tokens = [];

            let formData = new FormData();
            this.resultPago.pago.map(function(pago) {
                tokens.push(pago.token);
                formData.append("tokens[]", typeof pago.token !== "undefined" ? pago.token : "");
            });
            if (tokens.length == 0) {
                formData.append("tokens", "");
            }
            formData.append("id", typeof this.fields.id !== "undefined" ? this.fields.id : "");
            formData.append("nombres", typeof this.fields.nombres !== "undefined" ? this.fields.nombres : "");
            formData.append("paterno", typeof this.fields.paterno !== "undefined" ? this.fields.paterno : "");
            formData.append("materno", typeof this.fields.materno !== "undefined" ? this.fields.materno : "");
            formData.append("sexo", typeof this.fields.sexo !== "undefined" ? this.fields.sexo : "");
            formData.append("tipo_documento", typeof this.fields.tipo_documento !== "undefined" ? this.fields.tipo_documento : "");
            formData.append("nro_documento", typeof this.fields.nro_documento !== "undefined" ? this.fields.nro_documento : "");
            formData.append("email", typeof this.fields.email !== "undefined" ? this.fields.email : "");
            formData.append("celular", typeof this.fields.celular !== "undefined" ? this.fields.celular : "");
            formData.append("sede", this.fields.sede !== null ? this.fields.sede : "");
            formData.append("area", typeof this.fields.area !== "undefined" ? this.fields.area : "");
            formData.append("escuela", typeof this.fields.escuela !== "undefined" ? this.fields.escuela : "");
            axios
                .post("simulacro", formData)
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        //this.getEstadoVacantes();
                        toastr.success(response.data.message);
                        window.location.replace(response.data.url);
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
            // }
        },
        getSedes: function() {
            axios
                .get("get-sedes", {})
                .then(
                    function(response) {
                        this.sedes = response.data;
                    }.bind(this)
                );
        },
        getAreas: function() {
            axios.get("get-areas").then(
                function(response) {
                    this.areas = response.data;
                }.bind(this)
            );
        },
    
        getEscuelas: function() {
            axios.get("get-escuelas", {}).then(
                function(response) {
                    this.escuelas = response.data;
                }.bind(this)
            );
        },
      
        getTipoDocumentos: function() {
            axios.get("get-tipo-documentos").then(
                function(response) {
                    this.tipoDocumentos = response.data;
                    // console.log(this.tipoDocumentos);
                }.bind(this)
            );
        },
        changeAreas: function(area) {
            if (area != null) {
                this.disabledTurno = false;
            } else {
                this.disabledTurno = true;
                this.fields.turno = null;
            }
            this.disabledModalidad = true;
            this.disabledSede = true;
            this.fields.modalidad = null;
            this.fields.sede = null;
            this.getVacanteTurnos();
        },
                                          
        dateFormat: function(date) {
            return moment(date, "YYYY-MM-DD").format("DD-MM-YYYY");
        },

        changeDocumento: function() {
            if (this.fields.nro_documento.length == 0) {
                this.statusDocumento = false;
            } else {
                this.statusDocumento = true;
            }
        },
        // validacionCantidad: function(data)
        // {
        //     console.log(data);
        // }
        onSearch(search, loading) {
            if (search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        search: _.debounce((loading, search, vm) => {
            fetch(`get-colegios?q=${escape(search)}`).then(res => {
                res.json().then(json => (vm.colegios = json));
                loading(false);
            });
        }, 350),
    },
    mounted() {
        console.log("Component mounted.");
        this.getTipoDocumentos();
        this.getAreas();
        this.getSedes();
        this.getEscuelas();
    },
    created: function() {}
};
</script>
