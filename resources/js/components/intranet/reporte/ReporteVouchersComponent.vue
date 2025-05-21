<template>
  <div class="container mt-5">
    <h1 class="mb-4 text-center">Reporte de Vouchers</h1>

    <div class="row justify-content-center mb-4">
      <div class="col-md-5">
        <label for="startDate" class="form-label">Fecha de Inicio:</label>
        <input type="date" id="startDate" class="form-control" v-model="startDate">
      </div>
      <div class="col-md-5">
        <label for="endDate" class="form-label">Fecha de Fin:</label>
        <input type="date" id="endDate" class="form-control" v-model="endDate">
      </div>
    </div>

    <div class="text-center">
      <button class="btn btn-primary" :disabled="!startDate || !endDate" @click="generateReport">
        Generar PDF
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      startDate: '',
      endDate: ''
    };
  },
  methods: {
    generateReport() {
      const url = `/intranet/reporte/vouchers/pdf?fecha_inicio=${this.startDate}&fecha_fin=${this.endDate}`;
      window.open(url, '_blank');
    }
  }
};
</script>
