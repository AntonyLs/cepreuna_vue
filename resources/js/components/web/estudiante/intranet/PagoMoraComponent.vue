<template>
    <div class="row">
        <w-agregar-pago-cuota @result="resultPago = $event"></w-agregar-pago-cuota>
        <br>
        <div class="container" style="margin: 14px;">
            <div class="row">

                    <table class="table table-sm">
                        <tbody>
                            <tr v-for="result in resultPago.pago" :key="result.secuencia">
                                <td>
                                    <div class="alert alert-secondary" role="alert"> <b>Secuencia</b>:
                                        {{ result.secuencia }} | <b>Monto</b>: {{ result.monto }} | <b> fecha</b>: {{ dateFormat(result.fecha) }}
                                    </div>

                                <td>
                                    <div class="alert alert-success" role="alert">{{ result.message }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <div class="text-center">
                        <button type="button" v-if="resultPago.pago.length != 0" @click="submit" class="btn btn-success">Guardar Pago</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $ from 'jquery'
import toastr from "toastr";
export default {
    data:()=>({
        estudiante:'',
        resultPago:{
            pago:[]
        },
        fields:{
            tokens:[]
        },
        totalPago:0
    }),
    methods:{
        submit(){

            $(".loader").show();
            this.errors = {};
            let tokens = []
            this.resultPago.pago.map(function(pago){
                tokens.push(pago.token);
            });
            this.fields.tokens = tokens;

            axios.post('pago/registrar-pago-cuota-mora', this.fields).then(response =>{
                $(".loader").hide();
                if (response.data.status) {
                    toastr.success(response.data.message);
                    window.location.reload();
                }
                else{
                    toastr.warning(response.data.message,'Aviso');
                }
            }).catch(error => {
                $(".loader").hide();
                if(error.response.status ===422){
                    this.errors = error.response.data.errors || {};
                }
            });
        },
        getEstudiante: function(){
            axios.get('get-estudiante')
            .then(function (response) {
                // console.log(response.data);
                this.estudiante = response.data.estudiante;

            }.bind(this));
        },
        dateFormat: function(date) {
            return moment(date, 'YYYY-MM-DD').format('DD-MM-YYYY');
        },
    },
    mounted() {
        this.getEstudiante();
    }
};
</script>
