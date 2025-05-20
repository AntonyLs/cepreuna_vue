<script>
import { Pie } from "vue-chartjs";
export default {
    extends: Pie,
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
                .get("/intranet/estadistica/g-1reportes-2grafico", {
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
                                    label: "Promedio",
                                    backgroundColor: "#3cba9f",
                                    data: this.data,
                                    backgroundColor: ["rgb(255, 99, 132)", "rgb(54, 162, 235)", "rgb(255, 205, 86)"]
                                }
                            ]
                        },
                        {
                            responsive: true
                        }
                    );
                });
        }
    },
    mounted() {
        this.getData();
    },
    watch: {
        area: function(value) {
            this.getData();
        },
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
