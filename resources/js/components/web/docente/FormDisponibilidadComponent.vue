<template>
    <div>
        <form @submit.prevent="submit">
            <h5 class="text-success">1. Datos Personales</h5>
            <br />

            <div class="row">
                <div class="col-md-5 col-xs-12">
                    <div class="form-group">
                        <label for="tipo documento">Tipo Documento</label>
                        <v-select v-model="fields.tipo_documento" :options="tipoDocumentos" :reduce="tipo_documento => tipo_documento.id" label="denominacion" disabled></v-select>
                        <div v-if="errors && errors.tipo_documento" class="text-danger">{{ errors.tipo_documento[0] }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12">
                    <div class="form-group">
                        <label for="nro_documento">Número de Documento</label>
                        <input type="text" class="form-control" maxlength="8" minlength="8" name="nro_documento" id="nro_documento" v-model="fields.nro_documento" disabled />
                        <div v-if="errors && errors.nro_documento" class="text-danger">{{ errors.nro_documento[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="ruc">Ruc</label>
                        <input type="number" class="form-control"  name="ruc" id="ruc" v-model="fields.ruc" @input="maxLengthRuc" />
                        <div class="invalid-tooltip d-inline position-relative" v-if="fields.ruc.length!==11" >
                            Por favor ingrese 11 digitos
                        </div>
                        <div class="valid-tooltip d-inline position-relative border border-success" v-else >
                            Correcto!
                        </div>
                        <div v-if="errors && errors.ruc" class="text-danger">{{ errors.ruc[0] }}</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="nombres">Nombres</label>
                        <input type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" disabled/>
                        <div v-if="errors && errors.nombres" class="text-danger">{{ errors.nombres[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="paterno">Apellido Paterno</label>
                        <input type="text" class="form-control" name="paterno" id="paterno" v-model="fields.paterno" disabled/>
                        <div v-if="errors && errors.paterno" class="text-danger">{{ errors.paterno[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="materno">Apellido Materno</label>
                        <input type="text" class="form-control" name="materno" id="materno" v-model="fields.materno" disabled/>
                        <div v-if="errors && errors.materno" class="text-danger">{{ errors.materno[0] }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-xs-12">
                    <div class="form-group">
                        <label for="condicion">Condición</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" v-model="fields.condicion" name="condicion" id="condicion" value="1" @change="codigoVaciar" />
                                        Particular
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" v-model="fields.condicion" name="condicion" id="condicion" value="2" @change="codigoVaciar" />
                                        Unap (Administrativo o Docente Unap)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="tipo_trabajador">Tipo Trabajador</label>
                        <select v-model="fields.tipo_trabajador" id="tipo_trabajador" class="form-control" :disabled="fields.condicion == 1">
                            <option disabled value=""></option>
                            <option value="1">Administrativo</option>
                            <option value="2">Docente</option>
                        </select>
                        <div v-if="errors && errors.tipo_trabajador" class="text-danger">{{ errors.tipo_trabajador[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="codigo">Código docente Unap</label>
                        <input type="codigo" class="form-control" name="codigo" id="codigo" v-model="fields.codigo" :disabled="fields.condicion == 1" />
                        <div v-if="errors && errors.codigo" class="text-danger">{{ errors.codigo[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="contrato">Condición</label>
                        <select v-model="fields.contrato" id="contrato" class="form-control" :disabled="fields.condicion == 1">
                            <option disabled value=""></option>
                            <option value="1">Contratado</option>
                            <option value="2">Nombrado</option>
                        </select>
                        <div v-if="errors && errors.contrato" class="text-danger">{{ errors.contrato[0] }}</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="direccion">Dirección de residencia habitual</label>
                        <input type="text" class="form-control" name="direccion" id="direccion" v-model="fields.direccion" disabled/>
                        <div v-if="errors && errors.direccion" class="text-danger">{{ errors.direccion[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" v-model="fields.email" disabled/>
                        <div v-if="errors && errors.email" class="text-danger">{{ errors.email[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="celular">Celular</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">+51</span>
                            </div>
                            <input type="number" class="form-control" maxlength="9" minlength="9" name="celular" id="celular" v-model="fields.celular" @input="maxLengthCel" required/>  
                        </div>
                        <div class="invalid-tooltip d-inline position-relative"  v-if="fields.celular.length!==9" >
                            Por favor ingrese 9 digitos
                        </div>
                        <div class="valid-tooltip d-inline position-relative" v-else >
                            Correcto!
                        </div>

                        <div v-if="errors && errors.celular" class="text-danger">{{ errors.celular[0] }}</div>
                    </div>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                        <label for="tipo_trabajador">Nombre de su Banco</label>
                        <select v-model="fields.nombre_banco" id="tipo_trabajador" class="form-control" :disabled="fields.condicion == 2">
                            <option disabled value=""></option>
                            <option value="Banco de Crédito del Perú - BCP">Banco de Crédito del Perú - BCP</option>
                            <option value="Banco BBVA">Banco BBVA</option>
                            <option value="Banco Interbank">Banco Interbank</option>
                            <option value="Banco Scotiabank">Banco Scotiabank </option>
                            <option value="Banco Pichincha">Banco Pichincha</option>
                        </select>
                        
                        <div class="invalid-tooltip d-inline position-relative" v-if="!fields.nombre_banco" >
                            Por favor seleccione un banco que este vinculado a su RUC
                        </div>
                        <div class="valid-tooltip d-inline position-relative border border-success" v-else >
                            Correcto!
                        </div>
                        
                        <div v-if="errors && errors.nombre_banco" class="text-danger">{{ errors.nombre_banco[0] }}</div>
                    </div>

                </div>
                <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                        <label for="cci">Código de Cuenta Interbancaria (CCI)</label >
                        <input type="number" class="form-control" name="cci" id="cci" v-model="fields.cci" @input="maxLengthCci" :disabled="fields.condicion == 2" />
                        <div class="invalid-tooltip d-inline position-relative" v-if="fields.cci.length!==20" >
                            Por favor ingrese 20 digitos
                        </div>
                        <div class="valid-tooltip d-inline position-relative border border-success" v-else >
                            Correcto!
                        </div>
                        <div v-if="errors && errors.cci" class="text-danger">{{ errors.cci[0] }}</div>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <h5 class="text-success">2. Disponibilidad</h5>
            <br />
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="modalidad">Modalidad</label>
                        <select v-model="fields.modalidad" id="modalidad" class="form-control" @change="changeModalidad">
                            <option disabled value=""></option>
                            <option value="1">Virtual</option>
                            <option value="2">Presencial</option>
                            <option value="3">Presencial y Virtual</option>
                        </select>
                        <div v-if="errors && errors.modalidad" class="text-danger">{{ errors.modalidad[0] }}</div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="area">Área de estudios</label>
                        <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" @input="changeAreas" label="denominacion"></v-select>
                        <div v-if="errors && errors.area" class="text-danger">
                            {{ errors.area[0] }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-xs-12">
                    <div class="form-group">
                        <label for="modalidad">¿Enseño ciclos pasados?</label>
                        <select v-model="fields.dicto" id="modalidad" class="form-control">
                            <option disabled value=""></option>
                            <option value="1">Si</option>
                            <option value="0">No</option>
                        </select>
                        <div v-if="errors && errors.dicto" class="text-danger">{{ errors.dicto[0] }}</div>
                    </div>
                </div>
            </div>
            <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Seleccione el curso al cual postula (en caso de no pertenecer a la UNAP puede seleccionar mas de dos cursos).</p>
            <div v-if="errors && errors.cursos" class="text-danger">{{ errors.cursos[0] }}</div>
            <div class="row justify-content-md-center">
                <div class="col-md-4" v-for="item in areasCursos" :key="item.id">
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">{{ item.area }}</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="curso in item.cursos" :key="curso.id">
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" :value="curso.id" v-bind:id="'CheckCurso-' + curso.id" v-model="fields.cursos" @change="clickCurso" />
                                        <label class="form-check-label" v-bind:for="'CheckCurso-' + curso.id">
                                            {{ curso.denominacion }}
                                        </label>
                                    </div>
                                </td>
                                <td :style="'background:' + curso.color"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr />
            <h6 class="text-center">Sedes</h6>
            <div class="row justify-content-md-center">
                <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Seleccione la sede a la cual postula (Tiene la posibilidad de postular a dos sedes).</p>
                <div class="col-8">
                    <div v-if="errors && errors.sede" class="text-danger">{{ errors.sede[0] }}</div>
                    <div v-if="errors && errors.prioridad" class="text-danger">{{ errors.prioridad[0] }}</div>
                </div>
                <table class="table table-bordered col-8">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">Sedes</th>
                            <th scope="col">Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sede in sedes" :key="sede.id">
                            <td>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="sede.id"
                                        v-bind:id="'Check-' + sede.id"
                                        v-model="fields.sede"
                                        @change="checkSedes(sede.id, sede.denominacion, $event)"
                                    />
                                    <!-- :disabled="fields.sede.length >= 2  && fields.sede.indexOf(sede.id) === -1" -->
                                    <label class="form-check-label" v-bind:for="'Check-' + sede.id">
                                        {{ sede.denominacion }}
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="form-check">
                                    <input
                                        type="radio"
                                        v-bind:id="'Radio-' + sede.id"
                                        name="customRadio"
                                        class="form-check-input"
                                        :value="sede.id"
                                        v-model="fields.prioridad"
                                        checked="false"
                                        disabled
                                    />
                                    <label class="form-check-label" v-bind:for="'Radio-' + sede.id">Principal</label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr />
            <h6 class="text-secondary">Horario</h6>
            <!-- <br> -->
            <div class="row">
                <div class="col-md-12">
                    <small>(*) Seleccione de acuerdo al horario establecido</small>
                    <table class="table table-bordered table-sm text-center">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Biomédicas</th>
                                <th>Ingenierías</th>
                                <th>Sociales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>Mañana</th>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('biomedicas', 'ma')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('ingenierias', 'ma')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('sociales', 'ma')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                            </tr>
                            <tr>
                                <th>Tarde</th>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('biomedicas', 'ta')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('ingenierias', 'ta')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('sociales', 'ta')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                            </tr>
                            <tr>
                                <th>Noche</th>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('biomedicas', 'no')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('ingenierias', 'no')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" @click="verHorario('sociales', 'no')"><i class="fa fa-eye"></i> Ver</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="alert alert-warning" role="alert">
                <strong>Aviso importante</strong> Los horarios resaltados representan el horario del curso seleccionado para el periodo actual, dentro de los cuales deberá seleccionar una sola sede
                como se ve en la imagen correcta.
                <button type="button" class="btn btn-warning" @click="aviso">Ver Ejemplo</button>
            </div>
            <br />
            <div v-if="errors && errors.disponibilidad" class="text-danger">{{ errors.disponibilidad[0] }}</div>
            <div class="row justify-content-md-center" v-for="turno in turnoHorario" :key="turno.id">
                <h6 class="text-secondary col-12 text-center">Horario {{ turno.turno }}</h6>
                <br />
                <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Seleccione su disponibilidad de tiempo segun la prioridad que eligio en la tabla anterior.</p>
                <table class="table table-bordered horario_inscripcion">
                    <thead>
                        <tr class="table-primary">
                            <th scope="col">Hora</th>
                            <th scope="col">Lunes</th>
                            <th scope="col">Martes</th>
                            <th scope="col">Miercoles</th>
                            <th scope="col">Jueves</th>
                            <th scope="col">Viernes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="hora in turno.plantilla" :key="hora.id">
                            <th scope="col">{{ hora.hora_inicio }} - {{ hora.hora_fin }}</th>
                            <td scope="col" v-bind:style="{ background: pintar(hora.id, hora.horario, '1') }">
                                <select v-model="lu[hora.id]">
                                    <option value=""></option>
                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'1-' + hora.id + '-' + disponibidad">{{ disponiblesSede[disponibidad] }}</option>
                                </select>
                            </td>
                            <td scope="col" v-bind:style="{ background: pintar(hora.id, hora.horario, '2') }">
                                <select v-model="ma[hora.id]">
                                    <option value=""></option>
                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'2-' + hora.id + '-' + disponibidad">{{ disponiblesSede[disponibidad] }}</option>
                                </select>
                            </td>
                            <td scope="col" v-bind:style="{ background: pintar(hora.id, hora.horario, '3') }">
                                <select v-model="mi[hora.id]">
                                    <option value=""></option>
                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'3-' + hora.id + '-' + disponibidad">{{ disponiblesSede[disponibidad] }}</option>
                                </select>
                            </td>
                            <td scope="col" v-bind:style="{ background: pintar(hora.id, hora.horario, '4') }">
                                <select v-model="ju[hora.id]">
                                    <option value=""></option>
                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'4-' + hora.id + '-' + disponibidad">{{ disponiblesSede[disponibidad] }}</option>
                                </select>
                            </td>
                            <td scope="col" v-bind:style="{ background: pintar(hora.id, hora.horario, '5') }">
                                <select v-model="vi[hora.id]">
                                    <option value=""></option>
                                    <option v-for="disponibidad in fields.sede" :key="disponibidad" :value="'5-' + hora.id + '-' + disponibidad">{{ disponiblesSede[disponibidad] }}</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr />
            <vue-recaptcha sitekey="6LdtAfoSAAAAANbNT_vL65Gsi3WY4eBx3vX2-HUV" @verify="VerificarRecaptcha" @expired="ExpiradoRecaptcha"></vue-recaptcha>
            <div>
                <strong class="text-danger">{{ loginForm.pleaseTickRecaptchaMessage }}</strong>
            </div>
            <hr />
            <div class="row">
                <div class="alert alert-info" role="alert">
                    <div class="col text-justify">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" v-model="terminos" class="form-check-input" name="condiciones" id="condiciones0" value="checkedValue" />
                                ACEPTO que los datos consignados en el presente formulario son VERDADEROS y tienen caracter de
                                DECLARACION JURADA, los mismos que estan sujetos a Fiscalizacion Posterior. En
                                caso se acreditase falsedad o fraude, me someto a las sanciones de Ley.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div class="text-center">
                <button type="submit" :disabled="!terminos" class="btn btn-primary">Registrar Preinscripción</button>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="ModalAviso" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Ejemplo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col">
                                        <img src="/images/guia-horarios.png" alt="" class="img-fluid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ModalHoraio" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Horario</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col">
                                        <img :src="'/images/horario/' + horarioArea + '-' + horarioTurno + '.png'" alt="" class="img-fluid" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a :href="'/images/horario/' + horarioArea + '-' + horarioTurno + '.png'" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i> Ver Solo Imagen</a>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import $ from "jquery";
import "vue-select/dist/vue-select.css";
import toastr from "toastr";
import VueRecaptcha from "vue-recaptcha";

export default {
    components: { VueRecaptcha },
    props:['docente'],
    data() {
        return {
            fields: { 
                condicion: 1,
                tipo_documento:this.docente.tipo_documentos_id,
                nro_documento:this.docente.nro_documento,
                ruc:this.docente.ruc,
                nombres:this.docente.nombres,
                paterno:this.docente.paterno,
                materno:this.docente.materno,
                direccion:this.docente.direccion,
                email:this.docente.email,
                celular:this.docente.celular,
                nombre_banco:this.docente.nombre_banco,
                cci:this.docente.cci,
                sede: [], 
                prioridad: "", 
                cursos: [], 
                disponibilidad: [], 
                capacitacion_tipo: [], 
                capacitacion_titulo: [], 
                capacitacion_file: [],
                ruc:this.docente.ruc,
                celular:this.docente.celular,
                cci:this.docente.cci 
            },
            errors: {},
            // codigoDisable:"false",
            tipoDocumentos: [],
            departamentos: [],
            provincias: [],
            distritos: [],
            disabledDepartamento: false,
            disabledProvincia: true,
            disabledDistrito: true,
            distrito: "",
            // paises: [],
            sedes: [],
            areas: [],
            turnos: [],
            gradosAcademicos: [],
            programas: [],
            experiencias: [],
            capacitaciones: [],
            producciones: [],
            counterGrado: 0,
            inputsGrado: [],
            counterCapacitacion: 0,
            inputsCapacitacion: [],
            counterProduccion: 0,
            inputsProduccion: [],

            cursos: [],
            areasCursos: [],
            lu: [],
            ma: [],
            mi: [],
            ju: [],
            vi: [],
            disponiblesSede: [],
            turnoHorario: [],
            horarioMa: [],
            horarioTa: [],
            horarioNo: [],
            terminos: false,
            loginForm: {
                recaptchaVerified: false,
                pleaseTickRecaptchaMessage: ""
            },

            selectAdjuntoGrado: "Selecione",
            selectAdjuntoExperiencia: "Selecione",
            selectAdjuntoTitulo: "Selecione",
            // selectAdjuntoCapacitacion:[],
            selectAdjunto: "Selecione",
            horarioArea: "",
            horarioTurno: ""
        };
    },
    methods: {
        maxLengthRuc(){
            if (this.fields.ruc.length > 11) {
                this.fields.ruc = this.fields.ruc.slice(0, 11);
            }
        },
        maxLengthCel(){
            if (this.fields.celular.length > 9) {
                this.fields.celular = this.fields.celular.slice(0, 9);
            }
        },
        maxLengthCci(){
            if (this.fields.cci.length > 20) {
                this.fields.cci = this.fields.cci.slice(0, 20);
            }
        },
        codigoVaciar: function() {
            this.fields.codigo = "";
            this.fields.tipo_trabajador = "";
            this.fields.contrato = "";
        },
        checkSedes: function(value, text, $event) {
            // console.log($event.target.parentElement.parentElement);
            var elemento = $event.target.parentElement.parentElement.parentElement;
            var radio = elemento.children[1].children[0].children[0];
            if (event.target.checked) {
                radio.disabled = false;
                this.disponiblesSede.push({ id: value, text: text });
            } else {
                radio.disabled = true;
                radio.checked = false;
                var i;
                this.disponiblesSede.forEach(function(val, key) {
                    if (val.id === value) {
                        // console.log(val.id,value);
                        // console.log(this.disponiblesSede[0]);
                        i = key;
                    }
                });

                delete this.disponiblesSede[i];
                // console.log(this.disponiblesSede);
            }
        },
        VerificarRecaptcha: function() {
            this.loginForm.pleaseTickRecaptchaMessage = "";
            this.loginForm.recaptchaVerified = true;
        },
        ExpiradoRecaptcha: function() {
            this.loginForm.pleaseTickRecaptchaMessage = "Captcha expirado";
            this.loginForm.recaptchaVerified = false;
        },
        submit() {
            // console.log(this.VerificarRecaptcha());
            if (!this.loginForm.recaptchaVerified) {
                this.loginForm.pleaseTickRecaptchaMessage = "Captcha requerido.";
                return true;
            } else {
                $(".loader").show();
                this.errors = {};
                var disponible = [];
                var dias = [];

                dias = dias.concat(this.lu);
                dias = dias.concat(this.ma);
                dias = dias.concat(this.mi);
                dias = dias.concat(this.ju);
                dias = dias.concat(this.vi);

                console.log(dias);
                dias.forEach(function(val, key) {
                    // console.log(val);
                    if (val != "") {
                        disponible.push(val);
                    }
                });
                this.fields.disponibilidad = disponible;
                let formData = new FormData();
                // formData.append('tipo_documento', this.tipo_documento);
                formData.append("tipo_documento", typeof this.fields.tipo_documento !== "undefined" ? this.fields.tipo_documento : "");
                formData.append("nro_documento", typeof this.fields.nro_documento !== "undefined" ? this.fields.nro_documento : "");
                formData.append("ruc", typeof this.fields.ruc !== "undefined" ? this.fields.ruc : "");
                formData.append("nombres", typeof this.fields.nombres !== "undefined" ? this.fields.nombres : "");
                formData.append("paterno", typeof this.fields.paterno !== "undefined" ? this.fields.paterno : "");
                formData.append("materno", typeof this.fields.materno !== "undefined" ? this.fields.materno : "");
                formData.append("condicion", typeof this.fields.condicion !== "undefined" ? this.fields.condicion : "");
                formData.append("tipo_trabajador", typeof this.fields.tipo_trabajador !== "undefined" ? this.fields.tipo_trabajador : "");
                formData.append("contrato", typeof this.fields.contrato !== "undefined" ? this.fields.contrato : "");
                formData.append("codigo", typeof this.fields.codigo !== "undefined" ? this.fields.codigo : "");
                formData.append("departamento", typeof this.fields.departamento !== "undefined" ? this.fields.departamento : "");
                formData.append("provincia", typeof this.fields.provincia !== "undefined" ? this.fields.provincia : "");
                formData.append("ubigeo", typeof this.fields.ubigeo !== "undefined" ? this.fields.ubigeo : "");
                formData.append("email", typeof this.fields.email !== "undefined" ? this.fields.email : "");
                formData.append("celular", typeof this.fields.celular !== "undefined" ? this.fields.celular : "");
                formData.append("nombre_banco", typeof this.fields.nombre_banco !== "undefined" ? this.fields.nombre_banco : "");
                formData.append("cci", typeof this.fields.cci !== "undefined" ? this.fields.cci : "");
                formData.append("dicto", typeof this.fields.dicto !== "undefined" ? this.fields.dicto : "");
                formData.append("direccion", typeof this.fields.direccion !== "undefined" ? this.fields.direccion : "");
                formData.append("foto", typeof this.fields.foto !== "undefined" ? this.fields.foto : "");
                // formData.append("grado", typeof this.fields.grado !== "undefined" ? this.fields.grado : "");
                formData.append("programa_titulo", typeof this.fields.programa_titulo !== "undefined" ? this.fields.programa_titulo : "");
                formData.append("file_titulo", typeof this.fields.file_titulo !== "undefined" ? this.fields.file_titulo : "");

                this.inputsGrado.forEach(function(val, key) {
                    formData.append("grado_tipo[]", val.grado);
                    formData.append("grado_programa[]", val.programa);
                    formData.append("grado_archivo[]", val.archivo);
                });

                formData.append("experiencia", typeof this.fields.experiencia !== "undefined" ? this.fields.experiencia : "");
                formData.append("file_experiencia", typeof this.fields.file_experiencia !== "undefined" ? this.fields.file_experiencia : "");
                this.inputsCapacitacion.forEach(function(val, key) {
                    formData.append("capacitacion_tipo[]", val.capacitacion);
                    formData.append("capacitacion_titulo[]", val.tituloCapacitacion);
                    formData.append("capacitacion_archivo[]", val.archivo);
                });
                this.inputsProduccion.forEach(function(val, key) {
                    formData.append("produccion_tipo[]", val.produccion);
                    formData.append("produccion_titulo[]", val.tituloProduccion);
                    formData.append("produccion_archivo[]", val.archivo);
                });
                formData.append("modalidad", typeof this.fields.modalidad !== "undefined" ? this.fields.modalidad : "");
                formData.append("area", typeof this.fields.area !== "undefined" ? this.fields.area : "");
                this.fields.cursos.forEach(function(val, key) {
                    formData.append("cursos[]", val);
                });
                this.fields.sede.forEach(function(val, key) {
                    formData.append("sede[]", val);
                });
                // formData.append("sede[]",this.fields.sede);
                formData.append("prioridad", typeof this.fields.prioridad !== "undefined" ? this.fields.prioridad : "");
                // formData.append("disponibilidad[]",this.fields.disponibilidad);
                this.fields.disponibilidad.forEach(function(val, key) {
                    formData.append("disponibilidad[]", val);
                });
                // console.log(this.fields);
                axios
                    .post("disponibilidades", formData)
                    .then(response => {
                        $(".loader").hide();
                        console.log(response);
                        if (response.data.status == true) {
                            toastr.success(response.data.message);

                            window.location.replace(response.data.url);
                        } else {
                            console.log(response.data.message);
                            toastr.warning(response.data.message, "Aviso");
                        }
                        // console.log(response.data);
                    })
                    .catch(error => {
                        $(".loader").hide();
                        if (error.response.status === 422) {
                            this.errors = error.response.data.errors || {};
                            // toastr.error();
                        }
                    });
            }
        },
        getSedes: function() {
            axios
                .get("get-sedes-modalidad", {
                    params: {
                        modalidad: this.fields.modalidad
                    }
                })
                .then(
                    function(response) {
                        this.sedes = response.data;
                        var sedes = [];
                        this.sedes.forEach(function(val, key) {
                            sedes[val.id] = val.denominacion;
                        });
                        this.disponiblesSede = sedes;
                        // console.log(this.disponiblesSede);
                    }.bind(this)
                );
        },
        getCursos: function() {
            axios
                .get("get-cursos", {
                    params: {
                        area: this.fields.area
                    }
                })
                .then(
                    function(response) {
                        this.areasCursos = response.data;
                        // this.cursos = response.data.;
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
        getTurnos: function() {
            axios.get("get-turnos").then(
                function(response) {
                    this.turnos = response.data;
                }.bind(this)
            );
        },

        getGradosAcademicos: function() {
            axios.get("get-grados-academicos").then(
                function(response) {
                    this.gradosAcademicos = response.data;
                }.bind(this)
            );
        },
        getProgramas: function() {
            axios.get("get-programas").then(
                function(response) {
                    this.programas = response.data;
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
        getColegios: function() {
            axios.get("get-colegios").then(
                function(response) {
                    this.colegios = response.data;
                    // console.log(this.tipoColegios);
                }.bind(this)
            );
        },
        getPlantillaHorario: function() {
            axios
                .get("get-plantilla-horario", {
                    params: {
                        area: this.fields.area,
                        cursos: this.fields.cursos
                    }
                })
                .then(
                    function(response) {
                        this.turnoHorario = response.data;
                    }.bind(this)
                );
            // axios.get('get-plantilla-horario/2')
            // .then(function (response) {
            //     this.horarioTa = response.data;
            // }.bind(this));
            // axios.get('get-plantilla-horario/3')
            // .then(function (response) {
            //     this.horarioNo = response.data;
            // }.bind(this));
        },
        getExperiencias: function() {
            axios.get("get-experiencias").then(
                function(response) {
                    this.experiencias = response.data;
                }.bind(this)
            );
        },
        getCapacitaciones: function() {
            axios.get("get-capacitaciones").then(
                function(response) {
                    this.capacitaciones = response.data;
                }.bind(this)
            );
        },
        getProducciones: function() {
            axios.get("get-producciones").then(
                function(response) {
                    this.producciones = response.data;
                }.bind(this)
            );
        },
        changeAreas: function(area) {
            this.fields.cursos = [];
            this.getCursos();
            this.getPlantillaHorario();
        },
        clickCurso: function() {
            console.log(this.fields.cursos.length);
            // if(this.fields.cursos.length>0){
            this.getPlantillaHorario();
            // }
        },
        changeModalidad: function() {
            this.getSedes();
            this.fields.sede = [];
            this.fields.prioridad = "";
        },
        addGrado() {
            this.inputsGrado.push({
                id: ++this.counterGrado,
                grado: "",
                programa: "",
                value: "",
                texto: "Seleccione...",
                archivo: ""
            });
        },
        removeGrado(index) {
            this.$delete(this.inputsGrado, index);
        },
        addCapacitacion() {
            this.inputsCapacitacion.push({
                id: ++this.counterCapacitacion,
                capacitacion: "",
                tituloCapacitacion: "",
                value: "",
                texto: "Seleccione...",
                archivo: ""
            });
        },
        removeCapacitacion(index) {
            this.$delete(this.inputsCapacitacion, index);
        },

        addProduccion() {
            this.inputsProduccion.push({
                id: ++this.counterProduccion,
                produccion: "",
                tituloProduccion: "",
                value: "",
                texto: "Seleccione...",
                archivo: ""
            });
        },
        removeProduccion(index) {
            this.$delete(this.inputsProduccion, index);
        },

        filesChangeGrado(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjuntoGrado = "Selecione";
            } else {
                this.selectAdjuntoGrado = file.name;
                this.fields.file_grado = file;
            }
        },
        filesChangeTitulo(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjuntoTitulo = "Selecione";
            } else {
                this.selectAdjuntoTitulo = file.name;
                this.fields.file_titulo = file;
            }
        },
        filesChangeExperiencia(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjuntoExperiencia = "Selecione";
            } else {
                this.selectAdjuntoExperiencia = file.name;
                this.fields.file_experiencia = file;
            }
        },
        filesChangeGrado(e, index) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.inputsGrado[index].texto = "Selecione";
            } else {
                this.inputsGrado[index].texto = file.name;
                this.inputsGrado[index].archivo = file;
            }
        },
        filesChangeCapacitacion(e, index) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.inputsCapacitacion[index].texto = "Selecione";
            } else {
                this.inputsCapacitacion[index].texto = file.name;
                this.inputsCapacitacion[index].archivo = file;
            }
        },
        filesChangeProduccion(e, index) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.inputsProduccion[index].texto = "Selecione";
            } else {
                this.inputsProduccion[index].texto = file.name;
                this.inputsProduccion[index].archivo = file;
            }
        },
        filesChange(e) {
            let file = e.target.files[0];
            if (file === undefined) {
                this.selectAdjunto = "Selecione";
            } else {
                this.selectAdjunto = file.name;
                this.fields.file_experiencia = file;
            }
        },
        pintar(horaId, horario, dia) {
            let color = "";
            horario.forEach(function(val, key) {
                if (val.plantilla_horarios_id == horaId && dia == val.dia) {
                    color = val.curso.color;
                }
            });
            return color;
        },
        pintado(horaId, horario, dia) {
            let color = false;
            horario.forEach(function(val, key) {
                if (val.plantilla_horarios_id == horaId && dia == val.dia) {
                    color = val.plantilla_horarios_id;
                }
            });
            return color;
        },
        changeHorario(event, horaId, horario, dia) {
            // console.log(this.$refs[refe]);
            let idsede = event.target.value.split("-")[2];
            for (var h in horario) {
                if (horario[h].plantilla_horarios_id == horaId && dia == horario[h].dia) {
                    // color = val.plantilla_horarios_id;
                    this.lu[horaId] = "1-" + horaId + "-" + idsede;
                    this.ma[horaId] = "2-" + horaId + "-" + idsede;
                    this.mi[horaId] = "3-" + horaId + "-" + idsede;
                    this.ju[horaId] = "4-" + horaId + "-" + idsede;
                    this.vi[horaId] = "5-" + horaId + "-" + idsede;
                }
            }
        },
        aviso() {
            $("#ModalAviso").modal("show");
        },
        verHorario(Harea, Hturno) {
            this.horarioArea = Harea;
            this.horarioTurno = Hturno;
            $("#ModalHoraio").modal("show");
            // alert(area,turno)
        },
        changeDepartamento: function(departamento) {
            // this.fields.departamento = departamento

            if (departamento != null) {
                this.disabledProvincia = false;
                this.disabledDistrito = true;
            } else {
                this.disabledProvincia = true;
                this.disabledDistrito = true;
            }
            this.fields.provincia = null;
            this.distrito = null;

            this.getProvincias();
        },
        changeProvincia: function(provincia) {
            if (provincia != null) {
                this.disabledDistrito = false;
            } else {
                this.disabledDistrito = true;
            }
            this.distrito = null;
            this.getDistritos();
        },
        changeDistrito: function(distrito) {
            this.fields.ubigeo = distrito.id;
        },
        getDepartamentos: function() {
            axios.get("get-departamentos").then(
                function(response) {
                    this.departamentos = response.data;
                }.bind(this)
            );
        },
        getProvincias: function() {
            axios
                .get("get-provincias", {
                    params: {
                        codigo: this.fields.departamento
                    }
                })
                .then(
                    function(response) {
                        this.provincias = response.data;
                    }.bind(this)
                );
        },
        getDistritos: function() {
            axios
                .get("get-distritos", {
                    params: {
                        codigo: this.fields.provincia
                    }
                })
                .then(
                    function(response) {
                        this.distritos = response.data;
                    }.bind(this)
                );
        }
    },
    mounted() {
        this.getDepartamentos();
    },

    created: function() {
        // this.getCursos();
        this.getTipoDocumentos();
        this.getGradosAcademicos();
        this.getProgramas();
        this.getExperiencias();
        this.getCapacitaciones();
        this.getProducciones();
        // this.getDepartamentos();
        // this.getColegios();
        // this.getSedes();
        this.getAreas();
        // this.getParentescos();
        this.getTurnos();
        // this.getPlantillaHorario();
    }
};
</script>
<style>
.horario_inscripcion td {
    position: relative;
    /* color:white; */
}
.horario_inscripcion td span {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    height: 100%;
    font-weight: 500;
    z-index: 1000;
}
.horario_inscripcion td select {
    z-index: 1001;
}
</style>
