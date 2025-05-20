<template>
    <form @submit.prevent="submit">
        <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Complete los siguientes campos.</p>
        <h5 class="text-secondary">1. Datos Personales</h5>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="nombres">Nombres</label>
                    <input type="text" class="form-control" name="nombres" id="nombres" v-model="fields.nombres" />
                    <div v-if="errors && errors.nombres" class="text-danger">
                        {{ errors.nombres[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="paterno">Apellido Paterno</label>
                    <input type="text" class="form-control" name="paterno" id="paterno" v-model="fields.paterno" />
                    <div v-if="errors && errors.paterno" class="text-danger">
                        {{ errors.paterno[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="materno">Apellido Materno</label>
                    <input type="text" class="form-control" name="materno" id="materno" v-model="fields.materno" />
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
                                    <input type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="1" />
                                    M
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" v-model="fields.sexo" name="sexo" id="sexo" value="2" />
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
                    <v-select v-model="fields.tipo_documento" :options="tipoDocumentos" :reduce="tipo_documento => tipo_documento.id" label="denominacion"></v-select>
                    <div v-if="errors && errors.tipo_documento" class="text-danger">
                        {{ errors.tipo_documento[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="nro_documento">Número del Documento</label>
                    <input type="text" class="form-control" name="nro_documento" id="nro_documento" @blur="callWebService" @input="changeDocumento" v-model="fields.nro_documento" />
                    <div v-if="errors && errors.nro_documento" class="text-danger">
                        {{ errors.nro_documento[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-xs-12">
                <div class="row form-group">
                    <label class="col-md-12 col-xs-12 control-label">Subir documento de identidad (ambas caras y en formato .pdf 1MB): </label>
                    <div class="col-md-12 col-xs-12 ">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" id="file_dni" name="file_dni" accept="application/pdf" @change="filesChangeDni" :disabled="observacion" class="custom-file-input " />
                                <label class="custom-file-label" for="exampleInputFile">{{ selectFileDni.substr(0, 30) }}...</label>
                            </div>
                        </div>
                        <div v-if="errors && errors.file_dni" class="text-danger">
                            {{ errors.file_dni[0] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="fecha nacimiento">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" name="fecha_nac" id="fecha_nac" v-model="fields.fecha_nac" :disabled="observacion" />
                    <div v-if="errors && errors.fecha_nac" class="text-danger">
                        {{ errors.fecha_nac[0] }}
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" class="form-control" name="email" id="email" v-model="fields.email" :disabled="observacion"/>
                    <div v-if="errors && errors.email" class="text-danger">
                        {{ errors.email[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" class="form-control" name="celular" id="celular" v-model="fields.celular" :disabled="observacion" />
                    <div v-if="errors && errors.celular" class="text-danger">
                        {{ errors.celular[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 col-xs-12">
                <div class="form-group">
                    <label for="colegio">Colegio</label>
                    <v-select :options="colegios" label="denominacion" @search="onSearch" @input="changeColegio" :disabled="observacion" />
                        <template slot="no-options">
                            escriba para buscar su colegio
                        </template>
                        <template slot="option" slot-scope="option">
                            <div class="d-center">
                                {{ option.denominacion }}
                            </div>
                        </template>
                        <template slot="selected-option" slot-scope="option">
                            <div class="selected d-center">
                                {{ option.denominacion }}
                            </div>
                        </template>
                    </v-select>
                    <div v-if="errors && errors.colegio" class="text-danger">
                        {{ errors.colegio[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    <label for="año de egreso">Año de egreso</label>
                    <input type="number" class="form-control" name="egreso" id="egreso" v-model="fields.egreso" />
                    <div v-if="errors && errors.egreso" class="text-danger">
                        {{ errors.egreso[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="pais">Pais de procedencia</label>
                    <!-- <input type="text" class="form-control" name="pais" id="pais" v-model="fields.pais" /> -->
                    <v-select :options="paises" :reduce="pais => pais.id" label="denominacion" @input="changePais"></v-select>
                    <div v-if="errors && errors.pais" class="text-danger">
                        {{ errors.pais[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-xs-12">
                <div class="row form-group">
                    <label class="col-md-12 col-xs-12 control-label">Subir certificado de estudios o constancia de logros(formato .pdf .jpg .png max 5MB): </label>
                    <div class="col-md-12 col-xs-12 ">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" id="certificado" name="certificado" accept=".jpg, .jpeg, .png, .pdf" @change="filesChangeCertificado"  class="custom-file-input " />
                                <label class="custom-file-label" for="exampleInputFile">{{ selectFileCertificado.substr(0, 30) }}...</label>
                            </div>
                        </div>
                        <div v-if="errors && errors.certificado" class="text-danger">
                            {{ errors.certificado[0] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="departamento">Departamento de nacimiento</label>

                    <v-select
                        :disabled="disabledDepartamentoN"
                        :options="departamentosn"
                        :reduce="departamento => departamento.codigo_departamento"
                        v-model="fields.departamenton"
                        label="departamento"
                        @input="changeDepartamentoN"
                    ></v-select>
                    <div v-if="errors && errors.departamenton" class="text-danger">
                        {{ errors.departamenton[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="provincia">Provincia de nacimiento</label>

                    <v-select
                        :disabled="disabledProvinciaN"
                        :options="provinciasn"
                        :reduce="provincia => provincia.codigo_provincia"
                        v-model="fields.provincian"
                        label="provincia"
                        @input="changeProvinciaN"
                    ></v-select>
                    <div v-if="errors && errors.provincian" class="text-danger">
                        {{ errors.provincian[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="distrito">Distrito de nacimiento</label>

                    <v-select :disabled="disabledDistritoN" :options="distritosn" label="distrito" v-model="distriton" @input="changeDistritoN"></v-select>
                    <div v-if="errors && errors.ubigeon" class="text-danger">
                        {{ errors.ubigeon[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="departamento">Departamento de residencia habitual</label>

                    <v-select
                        :disabled="disabledDepartamento"
                        :options="departamentos"
                        :reduce="departamento => departamento.codigo_departamento"
                        v-model="fields.departamento"
                        label="departamento"
                        @input="changeDepartamento"
                    ></v-select>
                    <div v-if="errors && errors.departamento" class="text-danger">
                        {{ errors.departamento[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="provincia">Provincia de residencia habitual</label>

                    <v-select
                        :disabled="disabledProvincia"
                        :options="provincias"
                        :reduce="provincia => provincia.codigo_provincia"
                        v-model="fields.provincia"
                        label="provincia"
                        @input="changeProvincia"
                    ></v-select>
                    <div v-if="errors && errors.provincia" class="text-danger">
                        {{ errors.provincia[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="distrito">Distrito de residencia habitual</label>

                    <v-select :disabled="disabledDistrito" :options="distritos" label="distrito" v-model="distrito" @input="changeDistrito"></v-select>
                    <div v-if="errors && errors.ubigeo" class="text-danger">
                        {{ errors.ubigeo[0] }}
                    </div>
                </div>
            </div>
        </div>

        <hr />
        <h5 class="text-secondary">2. Datos Adicionales</h5>
        <br />
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="alert alert-warning" role="alert">
                    <strong class="text-danger"
                        ><i class="fa fa-info-circle"></i> El número de vacantes para modalidad presencial de acuerdo a su área, turno y sede es limitado, una vez cumplida la meta la opción de
                        modalidad presencial desaparecerá.</strong
                    >
                    <h6 style="margin-top:5px">Lista de vacantes agotadas:</h6>
                    <ul>
                        <li v-for="(vacante, index) in vacantes" :key="index">{{ vacante.area.denominacion + " - " + vacante.sede.denominacion + " - " + vacante.turno.denominacion }}</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="area">Área de estudios</label>
                    <v-select v-model="fields.area" :options="areas" :reduce="area => area.id" label="denominacion" @input="changeAreas"></v-select>
                    <div v-if="errors && errors.area" class="text-danger">
                        {{ errors.area[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="turno">Turno de estudios</label>
                    <v-select v-model="fields.turno" :options="turnos" :reduce="turno => turno.id" label="denominacion" :disabled="disabledTurno" @input="changeTurnos"></v-select>
                    <div v-if="errors && errors.turno" class="text-danger">
                        {{ errors.turno[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="modalidad">Modalidad</label>
                    <v-select
                        v-model="fields.modalidad"
                        :options="modalidades"
                        :reduce="modalidad => modalidad.id"
                        :disabled="disabledModalidad"
                        label="denominacion"
                        @input="changeModalidad"
                    ></v-select>
                    <div v-if="errors && errors.modalidad" class="text-danger">
                        {{ errors.modalidad[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="sede">Sede</label>
                    <v-select v-model="fields.sede" :options="sedes" :reduce="sede => sede.id" label="denominacion" :disabled="disabledSede"></v-select>
                    <div v-if="errors && errors.sede" class="text-danger">
                        {{ errors.sede[0] }}
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="cantidad inscrito">Número de matriculas</label>

                    <input type="number" class="form-control" min="0" name="cantidad_inscrito" id="cantidad_inscrito" v-model="fields.cantidad_inscrito" />
                    <div v-if="errors && errors.cantidad_inscrito" class="text-danger">
                        {{ errors.cantidad_inscrito[0] }}
                    </div>
                    <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Número de veces que estudió en CEPREUNA anteriormente.</p>
                </div>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="form-group">
                    <label for="escuelas">Escuela Profesional</label>
                    <v-select v-model="fields.escuela" :options="escuelas" :reduce="escuela => escuela.id" label="denominacion"></v-select>
                    <div v-if="errors && errors.escuela" class="text-danger">
                        {{ errors.escuela[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="estado_colegio">¿Estudias en el colegio?</label>
                    <select class="form-control" v-model="fields.estado_colegio" @change="fields.estado_colegio == '2' ? (showEstadoUniversidad = true) : (showEstadoUniversidad = false)">
                        <option value="">--Seleccionar--</option>
                        <option value="1">Si, Actualmente estudio en el colegio</option>
                        <option value="2">No, ya termine mis estudios secundarios</option>
                    </select>
                    <div v-if="errors && errors.estado_colegio" class="text-danger">
                        {{ errors.estado_colegio[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12" v-if="showEstadoUniversidad">
                <div class="form-group">
                    <label for="estado_universidad">¿Estudias en la Universidad?</label>
                    <select class="form-control" v-model="fields.estado_universidad">
                        <option value="">--Seleccionar--</option>
                        <option value="1">Si</option>
                        <option value="2">No</option>
                    </select>
                    <div v-if="errors && errors.estado_universidad" class="text-danger">
                        {{ errors.estado_universidad[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="estado_discapacidad">¿Presenta alguna discapacidad?</label>
                    <select class="form-control" v-model="fields.estado_discapacidad" @change="fields.estado_discapacidad == '1' ? (showEstadoDiscapacidad = true) : (showEstadoDiscapacidad = false)">
                        <option value="">--Seleccionar--</option>
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                    <div v-if="errors && errors.estado_discapacidad" class="text-danger">
                        {{ errors.estado_discapacidad[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-xs-12" v-if="showEstadoDiscapacidad">
                <div class="form-group">
                    <label for="discapacidad">Mencione la discapacidad que presenta</label>
                    <input type="text" class="form-control" name="discapacidad" id="discapacidad" v-model="fields.discapacidad" />
                    <div v-if="errors && errors.discapacidad" class="text-danger">
                        {{ errors.discapacidad[0] }}
                    </div>
                </div>
            </div>

            <upload-imagen @imagen64="fields.foto = $event"></upload-imagen>
            <div v-if="errors && errors.foto" class="text-danger">
                {{ errors.foto[0] }}
            </div>
        </div>

        <hr />
        <h5 class="text-secondary">3. Datos del Apoderado</h5>
        <br />
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="apoderado nombres">Nombres</label>
                    <input type="text" class="form-control" name="apoderado_nombres" id="apoderado_nombres" v-model="fields.apoderado_nombres" />
                    <div v-if="errors && errors.apoderado_nombres" class="text-danger">
                        {{ errors.apoderado_nombres[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="apoderado paterno">Apellido Paterno</label>
                    <input type="text" class="form-control" name="apoderado_paterno" id="celula" v-model="fields.apoderado_paterno" />
                    <div v-if="errors && errors.apoderado_paterno" class="text-danger">
                        {{ errors.apoderado_paterno[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="apoderado materno">Apellido Materno</label>
                    <input type="text" class="form-control" name="apoderado_materno" id="apoderado_materno" v-model="fields.apoderado_materno" />
                    <div v-if="errors && errors.apoderado_materno" class="text-danger">
                        {{ errors.apoderado_materno[0] }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="nro_documento_apoderado">Número de Documento</label>
                    <input type="text" class="form-control" name="nro_documento_apoderado" id="nro_documento_apoderado" v-model="fields.nro_documento_apoderado" />
                    <div v-if="errors && errors.nro_documento_apoderado" class="text-danger">
                        {{ errors.nro_documento_apoderado[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="apoderado celular">Celular</label>
                    <input type="text" class="form-control" name="apoderado_celular" id="apoderado_celular" v-model="fields.apoderado_celular" />
                    <div v-if="errors && errors.apoderado_celular" class="text-danger">
                        {{ errors.apoderado_celular[0] }}
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <div class="form-group">
                    <label for="parentesco"> Parentesco</label>
                    <v-select v-model="fields.parentesco" :options="parentescos" :reduce="parentesco => parentesco.id" label="denominacion"></v-select>
                    <div v-if="errors && errors.parentesco" class="text-danger">
                        {{ errors.parentesco[0] }}
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <h5 class="text-secondary">4. Pago</h5>
        <div v-if="errors && errors.tokens" class="text-danger">
            {{ errors.tokens[0] }}
        </div>
        <!-- <div class="alert alert-warning" role="alert">
            <strong class="text-danger"><i class="fa fa-info-circle"></i> Marque solo si accede a algun tipo de descuento.</strong>
        </div> -->
        <!-- <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="tipo_descuento"></label>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input tipo_descuento" value="2" id="tipo_descuento3" @change="changeTipoDescuento($event)" />
                            Soy hijo de trabajador de la UNA
                        </label>
                    </div>
                    <p class="text-info w-text-info"><i class="fa fa-info-circle"></i> Al seleccionar esta opcion debe entregar la constancia de ser hijo de trabajador de la UNA en el CEPREUNA.</p>
                </div>
            </div>
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <label for="tipo_descuento"></label>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input tipo_descuento" value="4" id="tipo_descuento4" @change="changeTipoDescuento($event)" />
                            Descuento por hermanos
                        </label>
                    </div>
                    <p class="text-info w-text-info">
                        <i class="fa fa-info-circle"></i> Al seleccionar esta opcion debe confirmar su inscripción adjuntando la ficha de inscripcion del hermano en CEPREUNA.
                    </p>
                </div>
            </div>
            <div class="col-md-4 col-xs-12" v-if="fields.tipo_descuento == '4'">
                <div class="form-group">
                    <label for="nro_documento_hermano">Número de Documento del Hermano Inscrito</label>
                    <input type="text" class="form-control" name="nro_documento_hermano" id="nro_documento_hermano" v-model="fields.nro_documento_hermano" />
                    <div v-if="errors && errors.nro_documento_hermano" class="text-danger">
                        {{ errors.nro_documento_hermano[0] }}
                    </div>
                </div>
            </div>
        </div> -->
        
        <p class="text-info" v-if="montoPagar != 0 && statusDocumento && !statusDescuento && statusColegio && fields.modalidad">
            <i class="fa fa-info-circle"></i> El monto total a pagar es de <b style="font-size:16px"> S/ {{ (montoPagar + 1).toFixed(2) }}</b
            >.
        </p>
        <div class="row" v-if="statusColegio && statusDocumento && !statusDescuento && fields.modalidad">
            <w-pago :documento="fields.nro_documento" :tarifa="tarifa" @result="resultPago = $event"></w-pago>
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
                            <input type="checkbox" v-model="terminos" class="form-check-input" name="condiciones" id="condiciones0" value="checkedValue" />
                            <b>Declaro bajo Juramento:</b>
                            <p>
                                Que, a la fecha de inscripción,
                                <ul>
                                <li>Cuento con acceso a servicio de internet y equipo de cómputo para seguir mis estudios de preparación pre universitaria en el
                                <b>ciclo virtual</b> actual de CEPREUNA, declaro que será mi responsabilidad el acceso.</li>
                                <li>Haber culminado el quinto de secundaria satisfactoriamente y contar con el certificado de estudios visado por la UGEL o DREP.</li>
                                <!-- <li>Haber culminado el quinto de secundaria satisfactoriamente y contar con el certificado de estudios visado por la UGEL o DREP. o ser un estudiante que esté en proceso de finalizar la educación secundaria (Quinto de secundaria).</li> -->
                                <li>No haber logrado una vacante en los procesos de exámenes de admisión a la Universidad Nacional del Altiplano (Extraordinario 2023-2024, CEPREUNA 2023-I, CEPREUNA 2023-II, GENERAL 2023-I, GENERAL 2023-II, CEPREUNA 2024-I y GENERAL 2024-I) y otros que determine la Dirección de Admisión en su reglamento para procesos de admisión.</li>
                                <li>Si es que tengo una segunda carrera en la UNAP haber culminado satisfactoriamente un año académico.</li>
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
        <div class="row" v-if="fields.modalidad == '2' && fields.modalidad">
            <div class="alert alert-info" role="alert">
                <div class="col text-justify">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" v-model="terminos" class="form-check-input" name="condiciones" id="condiciones0" value="checkedValue" />
                            <b>Declaro bajo Juramento:</b>
                            <p>
                                Que, a la fecha de inscripción.
                            </p>
                            <ul>
                                <li>Haber culminado el quinto de secundaria satisfactoriamente y contar con el certificado de estudios visado por la UGEL o DREP.</li>
                                <!-- <li>Haber culminado el quinto de secundaria satisfactoriamente y contar con el certificado de estudios visado por la UGEL o DREP. o ser un estudiante que esté en proceso de finalizar la educación secundaria (Quinto de secundaria).</li> -->
                                <li>No haber logrado una vacante en los procesos de exámenes de admisión a la Universidad Nacional del Altiplano (Extraordinario 2023-2024, CEPREUNA 2023-I, CEPREUNA 2023-II, GENERAL 2023-I, GENERAL 2023-II, CEPREUNA 2024-I y GENERAL 2024-I) y otros que determine la Dirección de Admisión en su reglamento para procesos de admisión.</li>
                                <li>Si es que tengo una segunda carrera en la UNAP haber culminado satisfactoriamente un año académico.</li>
                                <li>No tengo deuda pendiente en el CEPREUNA de ciclos anteriores.</li>
                            </ul>
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
            <button type="submit" :disabled="!terminos || observacion" class="btn btn-primary">
                Registrar Inscripción
            </button>
        </div>
    </form>
</template>

<script>
import "vue-select/dist/vue-select.css";
import toastr from "toastr";

export default {
    data() {
        return {
            observacion:false,
            fields: {
                sexo: 1,
                tokens: [],
                hijo_trabajador: false,
                pago_descuento: false,
                sede: null,
                cantidad_inscrito: 0,
                tipo_descuento: "1",
                colegio: null,
                modalidad: null,
                escuela: "",
                estado_colegio: "",
                estado_colegio: "",
                estado_universidad: "",
                estado_discapacidad: "",
                discapacidad: "",
                nro_documento_hermano: ""
            },
            errors: {
                file_dni: { 0: "" },
                certificado: { 0: "" }
            },
            tipoDocumentos: [],
            paises: [],
            sedes: [],
            areas: [],
            turnos: [],
            escuelas: [],
            parentescos: [],
            departamentos: [],
            provincias: [],
            distritos: [],
            departamentosn: [],
            provinciasn: [],
            distritosn: [],
            colegios: [],
            disabledDepartamento: true,
            disabledProvincia: true,
            disabledDistrito: true,
            disabledDepartamentoN: true,
            disabledProvinciaN: true,
            disabledDistritoN: true,
            disabledSede: true,
            disabledTurno: true,
            disabledModalidad: true,
            distrito: "",
            distriton: "",
            tarifa: "",
            statusDocumento: false,
            statusColegio: false,
            statusDescuento: false,
            resultPago: {
                pago: []
            },
            montoPagar: 0,
            terminos: false,
            sede: 1,
            estadoVacantes: true,
            modalidades: [
                { id: 1, denominacion: "Virtual" },
                { id: 2, denominacion: "Presencial" }
            ],
            //modalidades: [],
            selectFileDni: "Selecione",
            selectFileCertificado: "Selecione",
            showEstadoUniversidad: false,
            showEstadoDiscapacidad: false,
            vacantes: []
        };
        1;
    },
    methods: {
        callWebService() {
        $(".loader").show();
        axios.get('https://inscripciones.admision.unap.edu.pe/api/v1/observados-cepre-libre/' + this.fields.nro_documento)
            .then(response => {
                if(response.data.estado){
                    toastr.error('Usted se encuentra observado por la oficina de admisión. Diríjase a esa oficina para más información. No podrá continuar con su inscripción.', 'Error', {timeOut: 8000});
                    this.observacion = true;
                }else{
                    $(".loader").hide();
                    this.observacion = false;
                }
            })
            .catch(error => {
            $(".loader").hide();
            console.error('Error al llamar al servicio web:', error);
            });
        },

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
        filesChangeCertificado(e) {
            this.selectFileCertificado = "Selecione";
            let file = e.target.files[0];

            if (file === undefined) {
                this.selectFileCertificado = "Selecione";
            } else {
                if (file.size > 1024 * (1024 * 5)) {
                    this.errors.certificado[0] = "El tamaño del archivo excede el limite de 5 MB permitido.";
                    this.selectFileCertificado = "Selecione";
                    this.fields.certificado = null;
                } else {
                    this.errors.certificado[0] = "";
                    this.selectFileCertificado = file.name;
                    this.fields.certificado = file;
                }
            }
        },
        submit() {
            $(".loader").show();
            // if (this.fields.file_dni) {
            this.errors = { file_dni: { 0: "" }, certificado: { 0: "" } };
            // }

            let tokens = [];

            var fields = this.fields;

            let formData = new FormData();
            this.resultPago.pago.map(function(pago) {
                tokens.push(pago.token);
                formData.append("tokens[]", typeof pago.token !== "undefined" ? pago.token : "");
            });
            if (tokens.length == 0) {
                formData.append("tokens", "");
            }
            formData.append("nombres", typeof this.fields.nombres !== "undefined" ? this.fields.nombres : "");
            formData.append("paterno", typeof this.fields.paterno !== "undefined" ? this.fields.paterno : "");
            formData.append("materno", typeof this.fields.materno !== "undefined" ? this.fields.materno : "");
            formData.append("sexo", typeof this.fields.sexo !== "undefined" ? this.fields.sexo : "");
            formData.append("tipo_documento", typeof this.fields.tipo_documento !== "undefined" ? this.fields.tipo_documento : "");
            formData.append("nro_documento", typeof this.fields.nro_documento !== "undefined" ? this.fields.nro_documento : "");
            formData.append("fecha_nac", typeof this.fields.fecha_nac !== "undefined" ? this.fields.fecha_nac : "");
            formData.append("email", typeof this.fields.email !== "undefined" ? this.fields.email : "");
            formData.append("celular", typeof this.fields.celular !== "undefined" ? this.fields.celular : "");
            formData.append("egreso", typeof this.fields.egreso !== "undefined" ? this.fields.egreso : "");
            formData.append("pais", typeof this.fields.pais !== "undefined" ? this.fields.pais : "");
            formData.append("departamenton", typeof this.fields.departamenton !== "undefined" ? this.fields.departamenton : "");
            formData.append("provincian", typeof this.fields.provincian !== "undefined" ? this.fields.provincian : "");
            formData.append("ubigeon", typeof this.fields.ubigeon !== "undefined" ? this.fields.ubigeon : "");
            formData.append("departamento", typeof this.fields.departamento !== "undefined" ? this.fields.departamento : "");
            formData.append("provincia", typeof this.fields.provincia !== "undefined" ? this.fields.provincia : "");
            formData.append("ubigeo", typeof this.fields.ubigeo !== "undefined" ? this.fields.ubigeo : "");
            formData.append("modalidad", typeof this.fields.modalidad !== "undefined" ? this.fields.modalidad : "");
            formData.append("sede", this.fields.sede !== null ? this.fields.sede : "");
            formData.append("area", typeof this.fields.area !== "undefined" ? this.fields.area : "");
            formData.append("turno", typeof this.fields.turno !== "undefined" ? this.fields.turno : "");

            formData.append("escuela", typeof this.fields.escuela !== "undefined" ? this.fields.escuela : "");
            formData.append("estado_colegio", typeof this.fields.estado_colegio !== "undefined" ? this.fields.estado_colegio : "");
            formData.append("estado_universidad", typeof this.fields.estado_universidad !== "undefined" ? this.fields.estado_universidad : "");
            formData.append("estado_discapacidad", typeof this.fields.estado_discapacidad !== "undefined" ? this.fields.estado_discapacidad : "");
            formData.append("discapacidad", typeof this.fields.discapacidad !== "undefined" ? this.fields.discapacidad : "");

            formData.append("colegio", typeof this.fields.colegio !== "undefined" ? this.fields.colegio : "");
            formData.append("cantidad_inscrito", typeof this.fields.cantidad_inscrito !== "undefined" ? this.fields.cantidad_inscrito : "");
            formData.append("foto", typeof this.fields.foto !== "undefined" ? this.fields.foto : "");
            formData.append("apoderado_nombres", typeof this.fields.apoderado_nombres !== "undefined" ? this.fields.apoderado_nombres : "");
            formData.append("apoderado_paterno", typeof this.fields.apoderado_paterno !== "undefined" ? this.fields.apoderado_paterno : "");
            formData.append("apoderado_materno", typeof this.fields.apoderado_materno !== "undefined" ? this.fields.apoderado_materno : "");
            formData.append("apoderado_celular", typeof this.fields.apoderado_celular !== "undefined" ? this.fields.apoderado_celular : "");
            formData.append("nro_documento_apoderado", typeof this.fields.nro_documento_apoderado !== "undefined" ? this.fields.nro_documento_apoderado : "");
            formData.append("parentesco", typeof this.fields.parentesco !== "undefined" ? this.fields.parentesco : "");
            formData.append("nro_documento", typeof this.fields.nro_documento !== "undefined" ? this.fields.nro_documento : "");
            formData.append("tipo_descuento", typeof this.fields.tipo_descuento !== "undefined" ? this.fields.tipo_descuento : "");
            formData.append("nro_documento_hermano", typeof this.fields.nro_documento_hermano !== "undefined" ? this.fields.nro_documento_hermano : "");
            formData.append("file_dni", typeof this.fields.file_dni !== "undefined" ? this.fields.file_dni : "");
            formData.append("certificado", typeof this.fields.certificado !== "undefined" ? this.fields.certificado : "");

            // if (this.fields.turno != null) {
            //     axios
            //         .get("get-estado-vacantes", {
            //             params: {
            //                 sede: this.sede,
            //                 area: this.fields.area,
            //                 turno: this.fields.turno
            //             }
            //         })
            //         .then(function(response) {
            //             if (response.data) {
            //                 axios
            //                     .post("estudiantes", fields)
            //                     .then(response => {
            //                         $(".loader").hide();
            //                         if (response.data.status) {
            //                             toastr.success(response.data.message);
            //                             window.location.replace(response.data.url);
            //                         } else {
            //                             toastr.warning(response.data.message, "Aviso");
            //                         }
            //                     })
            //                     .catch(error => {
            //                         $(".loader").hide();
            //                         if (error.response.status === 422) {
            //                             this.errors = error.response.data.errors || {};
            //                         }
            //                     });
            //             } else {
            //                 toastr.warning("Las vacantes para el turno seleccionado se acabaron, por favor elija otro turno", "Aviso");
            //                 $(".loader").hide();
            //             }
            //             // vm.estadoVacantes = response.data;
            //         });
            // } else {
            axios
                .post("estudiantes", formData)
                .then(response => {
                    $(".loader").hide();
                    if (response.data.status) {
                        this.getEstadoVacantes();
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
                .get("get-sedes-modalidad", {
                    params: {
                        modalidad: this.fields.modalidad
                    }
                })
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
        getTurnos: function() {
            axios.get("get-turnos").then(
                function(response) {
                    this.turnos = response.data;
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
        getParentescos: function() {
            axios.get("get-parentescos").then(
                function(response) {
                    this.parentescos = response.data;
                }.bind(this)
            );
        },

        getPaises: function() {
            axios.get("get-paises").then(
                function(response) {
                    this.paises = response.data;
                }.bind(this)
            );
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
        },
        getDepartamentosN: function() {
            axios.get("get-departamentos").then(
                function(response) {
                    this.departamentosn = response.data;
                }.bind(this)
            );
        },
        getProvinciasN: function() {
            axios
                .get("get-provincias", {
                    params: {
                        codigo: this.fields.departamenton
                    }
                })
                .then(
                    function(response) {
                        this.provinciasn = response.data;
                    }.bind(this)
                );
        },
        getDistritosN: function() {
            axios
                .get("get-distritos", {
                    params: {
                        codigo: this.fields.provincian
                    }
                })
                .then(
                    function(response) {
                        this.distritosn = response.data;
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
        getEstadoVacantes() {
            var vm = this;
            axios
                .get("get-estado-vacantes", {
                    params: {
                        sede: this.fields.sede,
                        area: this.fields.area,
                        turno: this.fields.turno
                    }
                })
                .then(function(response) {
                    console.log(response);
                    vm.estadoVacantes = response.data;
                });
        },
        // getVacanteAreas: function() {
        //     axios
        //         .get("get-vacante-areas", {
        //             params: {
        //                 sede: this.fields.sede
        //             }
        //         })
        //         .then(
        //             function(response) {
        //                 this.areas = response.data;
        //             }.bind(this)
        //         );
        // },
        getVacanteModalidad: function() {            
            // axios
            //     .get("get-vacante-modalidad", {
            //         params: {
            //             area: this.fields.area,
            //             turno: this.fields.turno
            //         }
            //     })
            //     .then(
            //         function(response) {
            //             // this.turnos = response.data;
            //             let virtual = response.data.filter(e => {
            //                 return e.denominacion == "Virtual";
            //             });

            //             if (virtual.length != 0) {
            //                 if (response.data.length > 1) {
            //                     this.modalidades = [
            //                         { id: 1, denominacion: "Virtual" },
            //                         { id: 2, denominacion: "Presencial" }
            //                     ];
            //                 } else {
            //                   this.modalidades = [{ id: 1, denominacion: "Virtual" }];
            //                 }
            //             } else {
            //                    this.modalidades = [{ id: 2, denominacion: "Presencial" }];
            //             }
            //         }.bind(this)
            //     );


        },
        getVacanteTurnos: function(area) {
            axios
                .get("get-vacante-turnos", {
                    params: {
                        area: this.fields.area
                    }
                })
                .then(
                    function(response) {
                        this.turnos = response.data;
                    }.bind(this)
                );
        },
        getVacanteSedes: function(area) {
            // axios
            //     .get("get-vacante-sedes", {
            //         params: {
            //             area: this.fields.area,
            //             turno: this.fields.turno,
            //             modalidad: this.fields.modalidad
            //         }
            //     })
            //     .then(
            //         function(response) {
            //             // this.turnos = response.data;

            //             this.sedes = response.data;
            //         }.bind(this)
            //     );
            if (this.fields.modalidad === 2) {
                if (
                    (this.fields.area === 1 || this.fields.area === 2 || this.fields.area === 3) &&
                    this.fields.turno === 1
                ) {
                    // Activar el tercer select con las opciones para "JULI"
                    this.sedes = [{ id: 4, denominacion: "Juli - Chucuito" }];
                } else if (
                    (this.fields.area === 1 || this.fields.area === 2 || this.fields.area === 3) &&
                    this.fields.turno === 3
                ) {
                    // Activar el tercer select con las opciones para "PUNO"
                    this.sedes = [{ id: 3, denominacion: "Puno" }];
                } else if (this.fields.area === 1 && this.fields.turno === 2) {
                    // Activar el tercer select con las opciones para "AYAVIRI", "AZANGARO" y "HUANCANE"
                    this.sedes = [
                        { id: 5, denominacion: "Ayaviri" },
                        { id: 6, denominacion: "Azangaro" },
                        { id: 7, denominacion: "Huancané" }
                    ];
                } else if (this.fields.area === 2 && this.fields.turno === 2) {
                    // Activar el tercer select con las opciones para "AYAVIRI", "AZANGARO" y "HUANCANE"
                    this.sedes = [
                        { id: 5, denominacion: "Ayaviri" },
                        { id: 6, denominacion: "Azangaro" },
                        { id: 7, denominacion: "Huancané" }
                    ];
                }
                
                else if (
                    (this.fields.area === 1 || this.fields.area === 2 || this.fields.area === 3) &&
                    this.fields.turno === 2
                ) {
                    // Activar el tercer select con las opciones para "AZANGARO"
                    this.sedes = [
                        { id: 6, denominacion: "Azangaro" },
                        { id: 7, denominacion: "Huancané" }
                    ];
                } 
            } else {
                // Lógica si la modalidad es 1
                this.sedes = [{ id: 1, denominacion: "Virtual" }];
            }
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
        changeTurnos: function(turno) {
            if (turno != null) {
                this.disabledModalidad = false;
            } else {
                this.disabledModalidad = true;
                this.fields.modalidad = null;
            }
            this.disabledSede = true;
            this.fields.sede = null;
            this.getVacanteModalidad();
        },
        changePais: function(pais) {
            // console.log(data);
            if (pais == 1) {
                this.disabledDepartamento = false;
                this.disabledProvincia = true;
                this.disabledDistrito = true;

                this.disabledDepartamentoN = false;
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
            } else {
                this.disabledDepartamento = true;
                this.disabledProvincia = true;
                this.disabledDistrito = true;
                this.fields.departamento = null;
                this.fields.provincia = null;
                this.distrito = null;

                this.disabledDepartamentoN = true;
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
                this.fields.departamenton = null;
                this.fields.provincian = null;
                this.distriton = null;
            }
            this.fields.pais = pais;
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
        changeDepartamentoN: function(departamento) {
            // this.fields.departamento = departamento

            if (departamento != null) {
                this.disabledProvinciaN = false;
                this.disabledDistritoN = true;
            } else {
                this.disabledProvinciaN = true;
                this.disabledDistritoN = true;
            }
            this.fields.provincian = null;
            this.distriton = null;

            this.getProvinciasN();
        },
        changeProvinciaN: function(provincia) {
            if (provincia != null) {
                this.disabledDistritoN = false;
            } else {
                this.disabledDistritoN = true;
            }
            this.distriton = null;
            this.getDistritosN();
        },
        changeDistritoN: function(distrito) {
            this.fields.ubigeon = distrito.id;
        },
        changeColegio: function(data) {
            this.fields.modalidad = null;

            if (data != null) {
                this.fields.colegio = data.id;
                this.statusColegio = true;
            } else {
                this.statusColegio = false;
            }
        },
        changeModalidad: function(data) {
            this.fields.sede = null;
            if (data != null && this.fields.colegio) {
                this.fields.modalidad = data;

                axios
                    .post("../get-tarifa-modalidad", {
                        modalidad: data,
                        colegio: this.fields.colegio,
                        concepto: 1
                    })
                    .then(
                        function(response) {
                            this.tarifa = response.data.id;
                            this.montoPagar = response.data.importe;
                        }.bind(this)
                    );

                // this.statusColegio = true;
                this.disabledSede = false;
                // this.getSedes();
                this.getVacanteSedes();
            } else {
                // this.statusColegio = false;
                this.montoPagar = 0;
                this.disabledSede = true;
                this.fields.sede = null;
            }
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

        // getColegios: function(){
        //     axios.get('get-colegios')
        //     .then(function (response) {
        //         this.colegios = response.data;
        //     }.bind(this));
        // },
        changeTipoDescuento(event) {
            if (event.target.checked) {
                $(".tipo_descuento").prop("checked", false);
                event.target.checked = true;
                this.fields.tipo_descuento = event.target.value;
                this.statusDescuento = true;
            } else {
                this.fields.tipo_descuento = "1";
                this.statusDescuento = false;
            }
        },
        getVacantesAgotadas() {
            axios.get("get-vacantes-agotadas").then(
                function(response) {
                    this.vacantes = response.data.vacantes;
                }.bind(this)
            );
        }
    },
    mounted() {
        console.log("Component mounted.");
        this.getTipoDocumentos();
        this.getPaises();
        this.getDepartamentos();
        this.getDepartamentosN();
        // this.getColegios();
        // this.getVacanteAreas();
        // this.getSedes();
        this.getAreas();
        this.getParentescos();
        this.getTurnos();
        this.getEscuelas();
        this.getVacantesAgotadas();
    },
    created: function() {}
};
</script>
