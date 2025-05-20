<template>
    <div class="row">
        <h5 class="col-xs-12 col-md-12 text-center">{{ cantidad }} inscritos</h5>
        <div class="m-1">
            <button v-for="n in distribucion" :key="n.denominacion" type="button" class="btn btn-primary m-1">
                <!-- {{ area == 1 ? "B-" + (turno * 100 + n) : area == 2 ? "I-" + (turno * 100 + n) : "S-" + (turno * 100 + n) }} -->
                {{ n.denominacion }}
                <span class="badge badge-light"> {{ n.total }} </span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            areas: [],
            turnos: [],
            bgInfo: "bg-info",
            bgSuccess: "bg-success",
            bgWarning: "bg-warning",
            cantidad: 0,
            controlDivisor: 0,
            controlResiduo: 0,
            distribucion: 0
        };
    },
    props: ["area", "turno", "cantidadGrupo"],
    methods: {
        getAreas: function() {
            axios.get("../inscripciones/get-areas").then(
                function(response) {
                    this.areas = response.data;
                }.bind(this)
            );
        },
        getCantidad: function() {
            axios
                .get("dashboard/get-cantidad-matriculados", {
                    params: {
                        area: this.area,
                        turno: this.turno
                    }
                })
                .then(
                    function(response) {
                        this.distribucion = response.data;
                        let sum = 0;
                        response.data.forEach(e => {
                            sum = sum + parseInt(e.total);
                        });
                        this.cantidad = sum;
                        // this.cantidad = response.data;
                        // this.controlDivisor = parseInt(this.cantidad / this.cantidadGrupo);
                        // this.controlResiduo = parseInt(this.cantidad % this.cantidadGrupo);
                    }.bind(this)
                );
        }
    },
    watch: {
        cantidadGrupo: function(newVal, oldVal) {
            // watch it
            this.getCantidad();
        }
    },
    mounted() {
        this.getCantidad();

        console.log("Component mounted.");
    }
};
</script>
