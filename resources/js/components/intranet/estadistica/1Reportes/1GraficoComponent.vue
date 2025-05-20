<script>
import { Bar } from "vue-chartjs";
export default {
    extends: Bar,
    props: ["area", "fecha_ini", "fecha_fin"],
    data() {
        return {
            labels: [],
            data: []
        };
    },

    methods: {
        getData: function() {
            axios
                .get("/intranet/estadistica/g-1reportes-1grafico", {
                    params: {
                        fecha_ini: this.fecha_ini,
                        fecha_fin: this.fecha_fin
                    }
                })
                .then(response => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.labels = response.data.labels;
                    this.data = response.data.data;
                    // console.log(response.data);
                    this.renderChart(
                        {
                            labels: this.labels,
                            datasets: [
                                {
                                    label: "Participantes",
                                    backgroundColor: "#3cba9f",
                                    data: this.data
                                }
                            ]
                        },
                        {
                            responsive: true,
                            scales: {
                                yAxes: [
                                    {
                                        ticks: {
                                            min: 0,
                                            beginAtZero: true
                                        }
                                    }
                                ]
                            }
                        }
                    );
                });
        }
    },
    mounted() {
        this.getData();
        // this.renderChart({
        //     labels: this.labels,
        //     datasets: [
        //         {
        //             label: 'Cursos',
        //             backgroundColor: '#f87979',
        //             data: this.data
        //         }
        //     ]
        // },{responsive:true})
    },
    watch: {
        fecha_ini: function(value) {
            this.getData();
        },
        fecha_fin: function(value) {
            this.getData();
        }
    }
};
</script>

<style></style>
