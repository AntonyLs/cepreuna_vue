<template>
    <div class="card mb-2 shadow-sm rounded">
        <div class="card-header text-white text-center bg-primary py-1"><b class="card-title mb-0">Agregar Mora</b></div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <form class="" action="" @submit.prevent="submit">
                            <div class="form-row">
                                <div class="alert alert-info text-justify" role="alert">
                                    <small class=""><i class="fa fa-info-circle"></i> Para agregar un pago debe transcurrir un día despues de haber pagado ó <b>realizar el pago con un día de anticipación</b></small>
                                </div>

                                <span class="badge badge-secondary" style="margin: 0 auto;">{{ tipoColegio == 1 ? 'Colegio Particular': 'Colegio Público'}}</span>


                            </div>
                            <div v-if="errorPago != null" class="text-danger">* {{ errorPago }}</div>
                            <div class="form-row" style="margin: 5px 0;">
                                <label class="col-md-5 col-xs-12 control-label"> Número de Documento: </label>
                                <div class="col-md-7 col-xs-12">
                                    <input type="text" v-model="fields.nro_documento" name="nro_documento" id="nro_documento" class="form-control" placeholder="">
                                    <div v-if="errors && errors.nro_documento" class="text-danger">{{ errors.nro_documento[0] }}</div>
                                </div>
                            </div>
                            <div class="form-row" style="margin: 5px 0;">
                                <label class="col-md-5 col-xs-12 control-label"> Secuencia: </label>
                                <div class="col-md-7 col-xs-12">
                                    <input type="text" v-model="fields.secuencia" name="secuencia" id="secuencia" class="form-control" placeholder="">
                                    <div v-if="errors && errors.secuencia" class="text-danger">{{ errors.secuencia[0] }}</div>
                                </div>
                            </div>
                            <div class="form-row" style="margin: 5px 0;">
                                <label class="col-md-5 col-xs-12 control-label"> Monto: </label>
                                <div class="col-md-7 col-xs-12">
                                    <input type="text" v-model="fields.monto" name="monto" id="monto" class="form-control">
                                    <div v-if="errors && errors.monto" class="text-danger">{{ errors.monto[0] }}</div>
                                </div>
                            </div>
                            <div class="form-row" style="margin: 5px 0;">
                                <label class="col-md-5 col-xs-12 control-label"> Fecha: </label>
                                <div class="col-md-7 col-xs-12">
                                    <input type="date" v-model="fields.fecha" name="fecha" id="fecha" min="2020-08-03" class="form-control">
                                    <div v-if="errors && errors.fecha" class="text-danger">{{ errors.fecha[0] }}</div>
                                </div>
                            </div>
                            <div class="form-row" style="margin: 5px 0;">
                                <label class="col-md-5 col-xs-12 control-label"> Voucher Adjunto: </label>
                                <div class="col-md-7 col-xs-12 ">
                                    <div class="input-group">
                                        <div class="custom-file">
                                        <input type="file" id="adjunto" name="adjunto" @change="filesChange" class="custom-file-input ">
                                        <label class="custom-file-label" for="exampleInputFile" >{{ selectAdjunto.substr(0,20) }}...</label>
                                        </div>
                                    </div>
                                    <div v-if="errors && errors.file" class="text-danger">{{ errors.file[0] }}</div>
                                </div>
                            </div>
                            <br>
                            <div class="text-center">
                                <button type="submit" id="validar-banco" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar pago</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </div>

</template>

<script>
    import $ from 'jquery'
import ActualizarComponent from './ActualizarComponent.vue';
import toastr from "toastr";

    export default {
  components: { ActualizarComponent },
        data() {
            return{
                nombres:'',
                fields: {

                },
                errors: {},
                result:{
                    pago:[]
                },
                errorPago:null,
                selectAdjunto:'Selecione',
                selectDocumento:'Selecione',
            }
        },
        props: ['idInscripcion','tipoColegio'],
        methods: {
            submit(){
                $(".loader").show();
                this.errorPago = null;
                this.fields.tarifa = this.tarifa;
                this.fields.documento = this.documento;
                let formData = new FormData();

                formData.append('nro_documento',typeof this.fields.nro_documento !== 'undefined' ? this.fields.nro_documento:'');
                formData.append('secuencia',typeof this.fields.secuencia !== 'undefined' ? this.fields.secuencia:'');
                formData.append('monto',typeof this.fields.monto !== 'undefined' ? this.fields.monto:'');
                formData.append('fecha',typeof this.fields.fecha !== 'undefined' ? this.fields.fecha:'');
                formData.append('idInscripcion',this.idInscripcion);
                formData.append('tipoColegio',this.tipoColegio);

                formData.append('file',typeof this.fields.file !== 'undefined' ? this.fields.file:'');
                // formData.append('file_documento',typeof this.fields.file_documento !== 'undefined' ? this.fields.file_documento:'');

                this.errors = {};
                axios.post('agregar-pago-mora', formData).then(response =>{

                    if (response.data.status) {
                        // console.log(response.data)
                        this.$emit('result',this.fields.secuencia);
                        this.fields = {};
                        this.selectAdjunto = 'Seleccione';
                        this.selectDocumento = 'Seleccione';
                        this.errorPago = null;
                        toastr.success(response.data.message);
                        $(".loader").hide();
                    }
                    else{
                        this.errorPago = response.data.message;
                        // console.log(this.errorPago);
                        toastr.warning(response.data.message,'Error');
                        $(".loader").hide();
                    }

                }).catch(error => {
                    $(".loader").hide();
                    if(error.response.status ===422){
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            filesChange(e) {
                let file = e.target.files[0];
                if (file === undefined) {
                    this.selectAdjunto = 'Selecione';
                }
                else{
                    this.selectAdjunto = file.name;
                    this.fields.file = file;
                }

            },
        },
        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>
