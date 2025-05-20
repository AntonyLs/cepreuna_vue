<template>

    <div>
            <div v-if="(pagos).length != 0">
                <nav>
                    <div  class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a  v-for="(pago,i) in pagos" :key="pago.id" class="nav-link" :class="(i+1) == 1 ? 'active':''" id="nav-home-tab" data-toggle="tab" :href="'#nav-'+i" role="tab" aria-controls="nav-home" aria-selected="true">V{{ i+1 }}</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div v-for="(pago,i) in pagos" :key="pago.id" class="tab-pane fade show" :class="(i+1) == 1 ? 'active':''" :id="'nav-'+i" role="tabpanel" aria-labelledby="nav-home-tab">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xs-12 col-md-6">
                                    <b>Secuencia</b>
                                    <p class="m-0">{{ pago.secuencia }}</p>
                                    <b>Fecha</b>
                                    <p class="m-0">{{ (pago.fecha).split("-")[2]+'-'+(pago.fecha).split("-")[1]+'-'+(pago.fecha).split("-")[0] }}</p>
                                    <div v-if="pago.folio != null && pago.folio !='' ">
                                        <b>Folio</b>
                                        <p class="m-0">{{ pago.folio }}</p>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <b>Monto</b>
                                    <p class="m-0">{{ pago.monto }}</p>
                                    <b>Tipo de Pago</b>
                                    <p class="m-0">{{ pago.tipo_pago==1 ? 'Deposito Normal': 'Por Descuento' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="alert alert-warning" role="alert">
                    <strong>No existen vouchers para esta inscripción</strong>
                </div>
            </div>
    </div>
</template>

<script>
export default {
    data:()=>({
        pagos:[],
    }),
    methods:{
        getVouchersPago: function(){
            axios.get('pago/vouchers-pago')
            .then(function (response) {
                // console.log(response.data);
                this.pagos = response.data;
                // this.totalPagar = response.data.total_pagar;
                // this.cuota = response.data.cronograma.nro_cuota;
            }.bind(this));
        },
    },
    mounted() {
        this.getVouchersPago();
    }
};
</script>
