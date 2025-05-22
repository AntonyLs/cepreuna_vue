<template>
  <div>
    <vue-confirm-dialog></vue-confirm-dialog>
    <div class="row">
      <div class="col-sm-12">
        <div class="row">
          <div class="col-md-12">
            <div class="row justify-content-center">
              <!-- Fecha de Inicio -->
              <div class="form-group mb-0 col-md-3">
                <label for="startDate">Fecha de Inicio</label>
                <input type="date" id="startDate" class="form-control" v-model="startDate">
              </div>

              <!-- Fecha de Fin -->
              <div class="form-group mb-0 col-md-3">
                <label for="endDate">Fecha de Fin</label>
                <input type="date" id="endDate" class="form-control" v-model="endDate">
              </div>

              <!-- Tipo de Archivo -->
              <div class="form-group mb-0 col-md-3">
                <label for="archivo">Tipo de Archivo</label>
                <select class="form-control" id="fileType" v-model="fileType">
                  <option value="">--Seleccionar--</option>
                  <option value="imagenes">Imágenes</option>
                  <option value="documentos">PDFs</option>
                </select>
              </div>
            </div>

            <!-- Botón centrado abajo -->
            <div class="row mt-4">
              <div class="col text-center">
                <button
                  class="btn btn-primary"
                  style="min-width: 200px;"
                  @click="generateReport"
                  :disabled="!startDate || !endDate || !fileType"
                >
                  Generar PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      startDate: '',
      endDate: '',
      fileType: ''
    };
  },
  methods: {
    generateReport() {
      if (!this.startDate || !this.endDate || !this.fileType) {
        alert('Por favor debe completas las opciones: fechas y tipo de archivo.');
        return;
      }

      const url = `/intranet/reporte/vouchers/pdf?desde=${this.startDate}&hasta=${this.endDate}&tipo=${this.fileType}`;
      window.open(url, '_blank');
    }
  }
};
</script>

