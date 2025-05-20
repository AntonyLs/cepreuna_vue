<template>
    <div >
        <div class="alert alert-success" role="alert">
            <h3>Habilitado!!</h3>
            <p>Estudiante {{estudiante.nombres}} {{estudiante.paterno}} {{estudiante.materno}} Está apto para la inscripción del examen de cepreuna noviembre 2021 - marzo 2022.</p>
            <a :href="'/dga/estudiantes/pdf-constancia/'+idmatricula" class="btn btn-success" target="_blank">Descargar Constancia</a>
        </div>
    </div>
</template>

<script>
export default {
    props:["idmatricula"],
    data:()=>({
        estudiante:'',
    }),
    methods:{

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
