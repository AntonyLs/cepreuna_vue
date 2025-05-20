<template>
    <div class="container">
        <!-- Button trigger modal -->
        <button type="button"  class="btn btn-primary" data-toggle="modal" @click="openModal">
        <i class="fa fa-plus"></i> Añadir Voucher
        </button>

        <!-- Modal -->
        <div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Validar pago Banco de la Nación</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <form class="" action="" @submit.prevent="submit">
                                        <div class="form-row">
                                            <div class="alert alert-info text-justify" role="alert">
                                                <small class=""><i class="fa fa-info-circle"></i> Para validar el pago debe transcurrir un día para iniciar su inscripción ó <b>realizar el pago con un día de anticipación</b></small>
                                            </div>
                                        </div>
                                        <div v-if="errorPago != null" class="text-danger">* {{ errorPago }}</div>
                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> N° Documento: </label>
                                            <div class="col-md-7 col-xs-12">
                                            {{estudiante.nro_documento}}
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
                                                    <input type="file" id="adjunto" ref="voucher" name="adjunto" @change="filesChange" class="custom-file-input ">
                                                    <label class="custom-file-label" for="exampleInputFile" >{{ selectAdjunto.substr(0,20) }}...</label>
                                                    </div>
                                                </div>
                                                <div v-if="errors && errors.file" class="text-danger">{{ errors.file[0] }}</div>
                                            </div>

                                        </div>
                                        <br>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" id="validar-banco" class="btn btn-primary">Validar pago</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <img src="/images/voucher.jpg" class="img-fluid image-responsive" alt="">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import $ from 'jquery'
    export default {
        data() {
            return{
                fields: {

                },
                errors: {},
                result:{
                    pago:[]
                },
                errorPago:null,
                selectAdjunto:'Selecione',
                estudiante:''
            }
        },
        // props: ['tarifa','documento'],
        methods: {
            submit(){
                $(".loader").show();
                this.fields.tarifa = this.tarifa;
                let formData = new FormData();

                formData.append('secuencia',typeof this.fields.secuencia !== 'undefined' ? this.fields.secuencia:'');
                formData.append('monto',typeof this.fields.monto !== 'undefined' ? this.fields.monto:'');
                formData.append('fecha',typeof this.fields.fecha !== 'undefined' ? this.fields.fecha:'');
                // formData.append('tarifa',this.fields.tarifa);
                formData.append('documento',this.fields.documento);
                formData.append('file',typeof this.fields.file !== 'undefined' ? this.fields.file:'');

                this.errors = {};
                axios.post('pago/validar-pago-cuota', formData).then(response =>{
                    this.errorPago = null;
                    if (response.data.status) {
                        this.result.pago.push(response.data);
                        this.result.pago = this.result.pago.filter((pago, index, self) =>
                            index === self.findIndex((t) => (
                                t.secuencia === pago.secuencia
                            ))
                        )
                        $('#modalPago').modal('hide');
                        this.getEstudiante();
                        this.$emit('result',this.result);
                        this.fields = {};
                        this.selectAdjunto = 'Seleccione';
                        this.$refs.voucher.value=null;
                        this.errorPago = null;
                    }
                    else{
                        this.errorPago = response.data.message;
                    }
                    $(".loader").hide();
                }).catch(error => {
                    $(".loader").hide();
                    if(error.response.status ===422){
                        this.errors = error.response.data.errors || {};
                    }
                });
            },
            filesChange(e) {
                let file = e.target.files[0];
                this.selectAdjunto = file.name;
                this.fields.file = file;
            },
            getEstudiante: function(){
                axios.get('get-estudiante')
                .then(function (response) {
                    // console.log(response.data);
                    this.estudiante = response.data.estudiante;
                    this.fields.documento = response.data.estudiante.nro_documento
                }.bind(this));
            },
            openModal: function(){
                $('#modalPago').modal('show');
            }
        },
        mounted() {
            this.getEstudiante();
            // console.log(this.tarifa);
            // this.fields.documento = this.documento;
        }
    }
</script>
