<script>
import { Line } from 'vue-chartjs'
export default {
    extends: Line,
    props: ["grupo","fecha_ini","fecha_fin"],
    data() {
        return {
            labels:[],
            data:[]
        };
    },

    methods: {
        getData: function(){
            axios
            .get("/intranet/estadistica/g-4reportes-1grafico", {
                params: {
                    grupo: this.grupo,
                    fecha_ini: this.fecha_ini,
                    fecha_fin: this.fecha_fin
                }
            })
            .then(response => {
                // this.url = "docentes/pdf/"+response.data;
                this.labels = response.data.labels;
                this.data = response.data.data;
                // console.log(response.data);
                this.renderChart({
                    labels: this.labels,
                    datasets: this.data
                },{
                    // responsive:true,
                    scales:{
                        yAxes:[
                            {
                                ticks:{
                                    max:5.5,
                                    // min:0,
                                    // beginAtZero: true
                                }
                            }
                        ]
                    }
                })
            });
        }
    },
    mounted() {
        this.getData();
    },
    watch: {
        grupo:function(value){
            this.getData();
        },
        fecha_ini:function(value){
            this.getData();
        },
        fecha_fin:function(value){
            this.getData();
        }
    },
};
</script>

<style></style>
