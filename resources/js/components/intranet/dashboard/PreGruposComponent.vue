<template>
    <div class="row">
        <h5 class="col-xs-12 col-md-12 text-center">{{cantidad}} inscritos</h5>
        <div class="m-1">
            <button v-for="n in controlDivisor" :key="n" type="button" class="btn btn-primary m-1">
            {{ area == 1 ? 'B-'+(turno*100 + n) : area == 2 ? 'I-'+(turno*100 + n) : 'S-'+(turno*100 + n) }} <span class="badge badge-light"> {{ cantidadGrupo }} </span>
            </button>
            <button type="button" class="btn btn-primary m-1">
            {{ area == 1 ? 'B-'+(turno*100+controlDivisor+1) :area == 2 ? 'I-'+(turno*100+controlDivisor+1) : 'S-'+(turno*100+controlDivisor+1) }} <span class="badge badge-light"> {{ controlResiduo }} </span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return{
                areas:[],
                turnos:[],
                bgInfo:'bg-info',
                bgSuccess:'bg-success',
                bgWarning:'bg-warning',
                cantidad:0,
                controlDivisor:0,
                controlResiduo:0,
            }
        },
        props: ['area','turno','cantidadGrupo'],
        methods: {
            getAreas: function(){
                axios.get('../inscripciones/get-areas')
                .then(function (response) {
                    this.areas = response.data;
                }.bind(this));
            },
            getCantidad: function() {
                axios.get('dashboard/get-cantidad',{
                    params: {
                        area: this.area,
                        turno: this.turno
                    }
                }).then(function(response){
                    this.cantidad = response.data;
                    this.controlDivisor = parseInt(this.cantidad/this.cantidadGrupo);
                    this.controlResiduo = parseInt(this.cantidad%this.cantidadGrupo);
                }.bind(this));

            },
        },
        watch: {
      	cantidadGrupo: function(newVal, oldVal) { // watch it
                this.getCantidad();
            }
        },
        mounted() {
            this.getCantidad();

            console.log('Component mounted.')
        }
    }
</script>
