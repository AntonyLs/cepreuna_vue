<template>
    <div>
        <div class="container-fluit">
            <h4>{{titulo}}</h4>
            <div class="row">
                <div class="col-md-12 content ql-editor" v-html="contenido">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";
export default {
    props:["mostrar"],
    data(){
        return {
            titulo:'',
            contenido:''
        }
    },
    mounted(){
        axios
        .get("/comunicado/mostrar/"+this.mostrar, {
            params: {

            },
        })
        .then(
            function (response) {
                if(response.data.status){
                    this.titulo = response.data.data.titulo;
                    this.contenido = response.data.data.contenido;
                }
            }.bind(this)
        );
    }
}
</script>
