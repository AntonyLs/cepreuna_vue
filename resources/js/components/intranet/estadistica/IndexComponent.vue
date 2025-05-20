<template>
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <h5>Reporte de Calificación docente por curso</h5>
                    </header>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Inicio</label>
                                <input type="date" class="form-control" v-model="fecha_ini" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Fin</label>
                                <input type="date" class="form-control" v-model="fecha_fin" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de participantes por área en un rago de fecha</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-1reporte-1grafico :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de promedio de calificación por área en un rago de fecha</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-1reporte-2grafico :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de promedio de la calificacion por área en un rago de fecha</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-1reporte-3grafico :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <h5>Reporte de Calificación docente por área</h5>
                    </header>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-0 col-4">
                                <label for="area">Área</label>
                                <select class="form-control" v-model="area" @change="changeArea">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Inicio</label>
                                <input type="date" class="form-control" v-model="fecha_ini" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                            <div class="form-group mb-0 col-4">
                                <label for="grupo">Fecha Fin</label>
                                <input type="date" class="form-control" v-model="fecha_fin" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de participantes por curso y área en un rango de fecha</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-2reporte-1grafico :area="area" :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de docentes por curso</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-2reporte-2grafico :area="area" :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de promedio de calificación por curso</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-2reporte-3grafico :area="area" :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <h5>Reporte del promedio de los cursos por fecha y grupo</h5>
                    </header>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-0 col-md-3">
                                <label for="area">Area</label>
                                <select class="form-control" v-model="area" @change="changeArea">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="turno">Turno</label>
                                <select class="form-control" v-model="turno" @change="changeTurno">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="grupo">Grupo</label>
                                <select class="form-control" v-model="grupo" @change="changeGrupo">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="grupo">Fecha</label>
                                <input type="date" class="form-control" v-model="fecha_fin" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <header class="card-header">
                                        <h6>Gráfico de promedio de los cursos por fecha y grupo</h6>
                                    </header>
                                    <div class="card-body">
                                        <i-3reporte-1grafico :grupo="grupo" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <header class="card-header">
                        <h5>Reporte del rendimiento de los cursos por grupo y rango de fechas</h5>
                    </header>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group mb-0 col-md-3">
                                <label for="area">Area</label>
                                <select class="form-control" v-model="area" @change="changeArea">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="area in areas" :value="area.id" :key="area.id">{{ area.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="turno">Turno</label>
                                <select class="form-control" v-model="turno" @change="changeTurno">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="turno in turnos" :value="turno.id" :key="turno.id">{{ turno.denominacion }}</option>
                                </select>
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="grupo">Grupo</label>
                                <select class="form-control" v-model="grupo" @change="changeGrupo">
                                    <option value="">--Seleccionar--</option>
                                    <option v-for="grupo in grupos" :value="grupo.id" :key="grupo.id">{{ grupo.grupo.denominacion }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group mb-0 col-md-3">
                                <label for="grupo">Fecha Inicio</label>
                                <input type="date" class="form-control" v-model="fecha_ini" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                            <div class="form-group mb-0 col-md-3">
                                <label for="grupo">Fecha Fin</label>
                                <input type="date" class="form-control" v-model="fecha_fin" @change="changeFecha()" />
                                <!-- <div v-if="errors && errors.grupo_aula" class="text-danger">{{ errors.grupo_aula[0] }}</div> -->
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <!-- <header class="card-header">
                                        <h6>grafico del rendimiento de los curso por grupo y rango de fechas</h6>
                                    </header> -->
                                    <div class="card-body">
                                        <i-4reporte-1grafico :grupo="grupo" :fecha_ini="fecha_ini" :fecha_fin="fecha_fin" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal  Editar de Inscripcion-->
    </div>
</template>

<script>
// import VueCharts from 'vue-chartjs'
// import { Bar } from 'vue-chartjs'
export default {
    // extends: Bar,
    props: ["permissions", "fecha_i", "fecha_f"],
    data() {
        return {
            areas: [],
            turnos: [],
            grupos: [],
            area: "",
            turno: "",
            grupo: "",
            fecha_ini: this.fecha_i,
            fecha_fin: this.fecha_f
        };
    },
    methods: {
        getAreas: function() {
            axios.get("/intranet/get-areas").then(response => {
                this.areas = response.data;
            });
        },
        getTurnos: function() {
            axios.get("/intranet/get-turnos").then(response => {
                this.turnos = response.data;
            });
        },
        getGrupoAulas: function() {
            axios
                .get("/intranet/get-grupo-aulas", {
                    params: {
                        area: this.area,
                        turno: this.turno
                    }
                })
                .then(response => {
                    // this.grupo = response.data[0].id;
                    this.grupos = response.data;
                });
        },
        changeArea: function() {
            this.getGrupoAulas();
        },
        changeTurno: function() {
            this.getGrupoAulas();
            // this.changeDocente();
        },
        changeGrupo: function() {
            this.getCargaAcademica();
        },
        changeFecha: function() {},
        changeArea: function() {
            // this.getAreas();
        }
    },

    mounted: function() {
        this.getAreas();
        this.getTurnos();
        this.getGrupoAulas();

        // this.renderChart({
        //     labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        //     datasets: [
        //         {
        //         label: 'GitHub Commits',
        //         backgroundColor: '#f87979',
        //         data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
        //         }
        //     ]
        // })
    }
};
</script>

<style></style>
