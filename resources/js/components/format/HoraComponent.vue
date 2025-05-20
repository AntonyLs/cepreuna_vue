<template>
        <span>{{formatear()}}</span>
</template>

<script>
    import moment from 'moment'
    export default {
        name: "vue-fecha-format",
        props: {
            hora: {
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
                return moment("03-12-1992 "+this.hora).format(this.format);
                // moment("12-25-1995", "MM-DD-YYYY");
            }
        },
        mounted() {
            // console.log(this.fecha);
        },

    }
</script>
