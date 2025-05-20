<template>
    <div class="card">
        <div class="card-body row text-center p-2">
            <div v-if="totalPagar > totalPagado" class="col">
                <div class="text-value-xl" :class="totalPagado<totalPagar ? 'text-danger':'text-secondary'">S/ {{ deudaPendiente }}</div>
                <div class="text-uppercase text-muted small font-weight-bold">Deuda Pendiente </div>
                <div class="small text-muted">en la cuota N° {{ cuota }}</div>
            </div>
            <div v-else class="col">
                <div class="text-value-xl">S/ 0.00</div>
                <div class="text-uppercase text-muted small font-weight-bold">Deuda Pendiente </div>
                <div class="small text-muted">en la cuota N° {{ cuota }}</div>
            </div>
            <div class="c-vr"></div>
            <div class="col">
                <div class="text-value-xl">S/ {{ totalPagado.toFixed(2) }}</div>
                <div class="text-uppercase text-muted small font-weight-bold">Total Pagado</div>
                <div class="small text-muted">a la fecha actual</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props:['horario','turno_horas'],
    data:()=>({
        totalPagado:0,
        totalPagar:0,
        cuota:0
    }),
    methods:{
        getResumenPago: function(){
            axios.get('pago/resumen-pago')
            .then(function (response) {

                this.totalPagado = parseFloat(response.data.total_pagado);
                this.totalPagar = parseFloat(response.data.total_pagar);
                this.cuota = response.data.cronograma.nro_cuota;

            }.bind(this));
        },
    },
    mounted() {
        this.getResumenPago();
    },
    computed: {
        deudaPendiente: function() {
            return (this.totalPagar - this.totalPagado + 0.60).toFixed(2);
        }
    }
};
</script>
