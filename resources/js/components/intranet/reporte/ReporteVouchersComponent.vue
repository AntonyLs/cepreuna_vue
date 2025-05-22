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
                <label for="fileType">Tipo de Archivo</label>
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
                  :disabled="!startDate || !endDate || !fileType || loading"
                >
                  {{ loading ? 'Generando...' : 'Generar Reporte' }}
                </button>
                <p v-if="message" class="mt-2 text-muted">{{ message }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      startDate: '',
      endDate: '',
      fileType: '',
      loading: false,
      message: ''
    };
  },
  methods: {
    async generateReport() {
      this.loading = true;
      this.message = 'Archivo ZIP en proceso. Espera unos segundos...';

      try {
        const res = await axios.post('/intranet/reporte/vouchers/pdf', {
          desde: this.startDate,
          hasta: this.endDate,
          tipo: this.fileType
        });

        const filename = res.data.filename;

        // Intentar descargar después de 5 segundos
        setTimeout(() => {
          this.checkAndDownload(filename);
        }, 5000);
      } catch (err) {
        this.message = 'Ocurrió un error al generar el reporte.';
      } finally {
        this.loading = false;
      }
    },

    async checkAndDownload(filename) {
      try {
        const url = `/intranet/reporte/vouchers/descargar/${filename}`;
        const response = await axios.get(url, { responseType: 'blob' });

        const blob = new Blob([response.data], { type: 'application/zip' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        link.click();

        this.message = 'Descarga iniciada correctamente.';
      } catch (error) {
        this.message = 'El archivo aún no está listo. Intenta nuevamente en unos segundos.';
      }
    }
  }
};
</script>

<style scoped>
.text-muted {
  color: #6c757d;
}
</style>
