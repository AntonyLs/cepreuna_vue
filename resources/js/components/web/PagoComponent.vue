<template>
    <div class="container">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPago"><i class="fa fa-plus"></i> Añadir Pago</button>

        <!-- Modal -->
        <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Validar pago Banco de la Nación</h5>
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
                                                <small class=""
                                                    ><i class="fa fa-info-circle"></i> Para validar el pago debe transcurrir un día para iniciar su inscripción ó
                                                    <b>realizar el pago con un día de anticipación</b></small
                                                >
                                            </div>
                                            <div class="alert alert-warning text-justify" role="alert">
                                                <small class=""
                                                    ><i class="fa fa-info-circle"></i><b> Usted realizo el pago mediante <a href="https://pagalo.pe/">pagalo.pe</a></b>
                                                    <input type="checkbox" v-model="fields.pagadoConPagalo" name="pagadoConPagalo" id="pagadoConPagalo" />
                                                </small>
                                            </div>
                                        </div>
                                        <div v-if="errorPago != null" class="text-danger">* {{ errorPago }}</div>
                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> N° Documento: </label>
                                            <div class="col-md-7 col-xs-12">
                                                {{ documento }}
                                            </div>
                                        </div>
                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> Secuencia: </label>
                                            <div class="col-md-7 col-xs-12">
                                                <input type="text" v-model="fields.secuencia" name="secuencia" id="secuencia" class="form-control" placeholder="" />
                                                <div v-if="errors && errors.secuencia" class="text-danger">{{ errors.secuencia[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> Monto: </label>
                                            <div class="col-md-7 col-xs-12">
                                                <input type="text" v-model="fields.monto" name="monto" id="monto" class="form-control" />
                                                <div v-if="errors && errors.monto" class="text-danger">{{ errors.monto[0] }}</div>
                                            </div>
                                        </div>
                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> Fecha: </label>
                                            <div class="col-md-7 col-xs-12">
                                                <input type="date" v-model="fields.fecha" name="fecha" id="fecha" min="2020-08-03" class="form-control" />
                                                <div v-if="errors && errors.fecha" class="text-danger">{{ errors.fecha[0] }}</div>
                                            </div>
                                        </div>

                                        <div class="form-row" style="margin: 5px 0;">
                                            <label class="col-md-5 col-xs-12 control-label"> Voucher Adjunto: </label>
                                            <div class="col-md-7 col-xs-12 ">
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" id="adjunto" name="adjunto" @change="filesChange" class="custom-file-input " />
                                                        <label class="custom-file-label" for="exampleInputFile">{{ selectAdjunto.substr(0, 20) }}...</label>
                                                    </div>
                                                </div>
                                                <div v-if="errors && errors.file" class="text-danger">{{ errors.file[0] }}</div>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <button type="submit" id="validar-banco" class="btn btn-primary">Validar pago</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 col-xs-12" v-if="this.fields.pagadoConPagalo">
                                    <img src="/images/pagalo.jpg" class="img-fluid image-responsive image-with-margin" alt="" />
                                </div>
                                <div class="col-md-6 col-xs-12" v-else>
                                    <img src="/images/voucher.jpg" class="img-fluid image-responsive image-with-margin" alt="" />
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
import $ from "jquery";
export default {
    data() {
        return {
            fields: {},
            errors: {},
            result: {
                pago: []
            },
            errorPago: null,
            selectAdjunto: "Selecione"
        };
    },
    props: ["tarifa", "documento"],
    methods: {
        submit() {
            this.fields.tarifa = this.tarifa;
            this.fields.documento = this.documento;
            let formData = new FormData();

            // Pagalo.pe (checkbox)
            formData.append("pagarEnPagalo", typeof this.fields.pagadoConPagalo !== "undefined" ? this.fields.pagadoConPagalo : "");
            formData.append("secuencia", typeof this.fields.secuencia !== "undefined" ? this.fields.secuencia : "");
            formData.append("monto", typeof this.fields.monto !== "undefined" ? this.fields.monto : "");
            formData.append("fecha", typeof this.fields.fecha !== "undefined" ? this.fields.fecha : "");
            formData.append("tarifa", this.fields.tarifa);
            formData.append("documento", this.fields.documento);
            formData.append("file", typeof this.fields.file !== "undefined" ? this.fields.file : "");

            this.errors = {};
            axios
                .post("/validar-pago", formData)
                .then(response => {
                    this.errorPago = null;
                    if (response.data.status) {
                        this.result.pago.push(response.data);
                        this.result.pago = this.result.pago.filter((pago, index, self) => index === self.findIndex(t => t.secuencia === pago.secuencia));
                        $("#modalPago").modal("hide");
                        this.$emit("result", this.result);
                        this.fields = {};
                        this.selectAdjunto = "Seleccione";
                        this.errorPago = null;
                    } else {
                        this.errorPago = response.data.message;
                        console.log(this.errorPago);
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    }
                });
        },
        filesChange(e) {
            let file = e.target.files[0];
            this.selectAdjunto = file.name;
            this.fields.file = file;
        }
    },
    mounted() {
        console.log("Component mounted.");
        // console.log(this.tarifa);
        // this.fields.documento = this.documento;
    }
};
</script>
<style>
.image-with-margin {
    margin-top: 20px;
}
</style>
