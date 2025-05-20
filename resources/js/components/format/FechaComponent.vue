<template>
        <span>{{formatear()}}</span>
</template>

<script>
    import moment from 'moment'
    export default {
        name: "vue-fecha-format",
        props: {
            fecha: {
                type: String,
                default: ''
            },
            format: {
                type: String,
                default: ''
            }
        },
        methods: {
            formatear() {
                return moment(this.fecha).format(this.format);
                // moment("12-25-1995", "MM-DD-YYYY");
            }
        },
        mounted() {
            // console.log(this.fecha);
        },

    }
</script>
