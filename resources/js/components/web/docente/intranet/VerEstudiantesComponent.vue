<template>
    <div class="container">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalPago" @click="getEstudiante">Ver Estudiantes</button>

        <!-- Modal -->
        <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Lista de Estudiantes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped col table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Estudiante</th>
                                                <th>DNI</th>
                                                <th>Correo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, i) in estudiantes" :key="i">
                                                <td>{{ i + 1 }}</td>
                                                <td>{{ item.nombres }}</td>
                                                <td>{{ item.nro_documento }}</td>
                                                <td>{{ item.usuario }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
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
            selectAdjunto: "Selecione",
            estudiantes: []
        };
    },
    props: ["idGrupoAula"],
    methods: {
        getEstudiante: function() {
            axios.get("cursos/get-estudiantes/" + this.idGrupoAula).then(
                function(response) {
                    this.estudiantes = response.data.estudiantes;
                }.bind(this)
            );
        }
    },
    mounted() {
        // this.getEstudiante();
    }
};
</script>
