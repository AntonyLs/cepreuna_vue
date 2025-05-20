<script>
import { Bar } from "vue-chartjs";
export default {
    extends: Bar,
    props: ["grupo", "fecha_fin"],
    data() {
        return {
            labels: [],
            data: [],
            colors: []
        };
    },

    methods: {
        getData: function() {
            axios
                .get("/intranet/estadistica/g-3reportes-1grafico", {
                    params: {
                        fecha_fin: this.fecha_fin,
                        grupo: this.grupo
                    }
                })
                .then(response => {
                    // this.url = "docentes/pdf/"+response.data;
                    this.labels = response.data.labels;
                    this.data = response.data.data;
                    this.colors = response.data.colors;
                    // console.log(response.data);
                    this.renderChart(
                        {
                            labels: this.labels,
                            datasets: [
                                {
                                    label: "Promedio",
                                    backgroundColor: "#3cba9f",
                                    data: this.data,
                                    backgroundColor: this.colors
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
    },
    watch: {
        grupo: function(value) {
            this.getData();
        },
        fecha_fin: function(value) {
            this.getData();
        }
    }
};
</script>

<style></style>
