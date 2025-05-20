<template>
<div>
    <a v-if="url != '' " name="" id="" class="btn btn-link p-0" style="font-size:20px" :href="'/storage/documentos/'+url" role="button" download>
        <i class="fas fa-file-pdf"></i>
    </a>
</div>

</template>

<script>
import 'vue-select/dist/vue-select.css';
import toastr from "toastr";
import $ from 'jquery';

export default {
    data:()=>({

        url:''
    }),
    props: ['semana','curso'],
    methods:{

        getUrl: function(){
            axios.get('get-url-cuadernillo',{
                params: {
                    semana: this.semana,
                    curso: this.curso,
                }
            })
            .then(function (response) {
                console.log(response.data.path)

                if (response.data.path !== undefined) {
                    this.url = response.data.path;
                }

            }.bind(this));
        }
    },
    mounted() {
        this.getUrl();
    }
}
</script>

<style>
</style>
