<template>
    <div class="card">
        <div class="card-header"><h5 class="mb-0">División de Grupos</h5></div>
        <div class="card-body">
            <button :class="helpStatus ? 'btn btn-info' : 'btn btn-outline-info'" @click="helpStatus = true" class="p-mr-3">Pre Distribución</button>
            <button :class="!helpStatus ? 'btn btn-info' : 'btn btn-outline-info'" @click="helpStatus = false">Distribución actual</button>
            <div v-if="helpStatus">
                <div class="row">
                    <div class="col-xs-12 col-md-12">
                        <div class="float-right">
                            <div class="form-group">
                                <label for="cantidad">Cantidad por Grupo</label>
                                <input
                                    type="text"
                                    v-model="cantidadGrupo"
                                    class="form-control"
                                    name="cantidad"
                                    id="cantidad"
                                    aria-describedby="cantidadhelp"
                                    placeholder="Ingrese cantidad"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-xs-4 p-2" v-for="area in areas" :key="area.id">
                        <div class="row" style="margin:1px">
                            <div class="card text-white mb-3 col-xs-12 col-md-12" :class="[area.id == 1 ? bgSuccess : area.id == 2 ? bgInfo : bgWarning]">
                                <div class="card-header text-center">
                                    <h5 class="card-title mb-0">{{ area.denominacion.toUpperCase() }}</h5>
                                </div>
                                <div class="card-body  p-1">
                                    <div class="col-md-12 col-xs-12 p-1" v-for="turno in turnos" :key="turno.id">
                                        <div class="card text-primary">
                                            <div class="card-header p-1">
                                                <h6 class="text-center mb-0">{{ turno.denominacion }}</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <i-pre-grupos :area="area.id" :turno="turno.id" :cantidadGrupo="cantidadGrupo"></i-pre-grupos>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="!helpStatus">
                <div class="row">
                    <div class="col-md-4 col-xs-4 p-2" v-for="area in areas" :key="area.id">
                        <div class="row" style="margin:1px">
                            <div class="card text-white mb-3 col-xs-12 col-md-12" :class="[area.id == 1 ? bgSuccess : area.id == 2 ? bgInfo : bgWarning]">
                                <div class="card-header text-center">
                                    <h5 class="card-title mb-0">{{ area.denominacion.toUpperCase() }}</h5>
                                </div>
                                <div class="card-body  p-1">
                                    <div class="col-md-12 col-xs-12 p-1" v-for="turno in turnos" :key="turno.id">
                                        <div class="card text-primary">
                                            <div class="card-header p-1">
                                                <h6 class="text-center mb-0">{{ turno.denominacion }}</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <i-grupos :area="area.id" :turno="turno.id" :cantidadGrupo="50"></i-grupos>
                                            </div>
                                        </div>
                                    </div>
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
export default {
    data() {
        return {
            areas: [],
            turnos: [],
            bgInfo: "bg-info",
            bgSuccess: "bg-success",
            bgWarning: "bg-warning",
            cantidadGrupo: 60,
            helpStatus: true
        };
    },
    methods: {
        getAreas: function() {
            axios.get("../inscripciones/get-areas").then(
                function(response) {
                    this.areas = response.data;
                }.bind(this)
            );
        },
        getTurnos: function() {
            axios.get("../inscripciones/get-turnos").then(
                function(response) {
                    this.turnos = response.data;
                }.bind(this)
            );
        },
        total(area, turno) {
            let cantidad = 0;
            axios
                .get("dashboard/get-cantidad", {
                    params: {
                        area: area,
                        turno: turno
                    }
                })
                .then(
                    function(response) {
                        console.log(response.data);
                        this.cantidad = response.data;
                    }.bind(this)
                );
            return `${cantidad}`;
        }
    },

    mounted() {
        this.getAreas();
        this.getTurnos();
        console.log("Component mounted.");
    }
};
</script>
