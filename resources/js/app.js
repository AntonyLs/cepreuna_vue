/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");
window.moment = require("moment");
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
// IMPORTS
import Vue from "vue";
import VueCal from "vue-cal";
import "vue-cal/dist/vuecal.css";
import "vue-cal/dist/i18n/es.es.js";
import "vue-cal/dist/vuecal.css";
import vSelect from "vue-select";
import { ServerTable, ClientTable } from "vue-tables-2";
import VueConfirmDialog from "vue-confirm-dialog";
import QrcodeVue from "qrcode.vue";
// CONFIGURACIONES

var options = {
    perPage: 10,
    perPageValues: [10, 15, 20],
    sortIcon: {
        is: "fa-sort",
        base: "fas",
        up: "fa-sort-up",
        down: "fa-sort-down"
    },
    texts: {
        count: "Mostrando {from} a {to} de {count} registros |{count} registros|Un registro",
        first: "Primero",
        last: "Ultimo",
        filter: "Filtro:",
        filterPlaceholder: "Consulta de Busqueda",
        limit: "Registros:",
        page: "Pagina:",
        noResults: "No hay registros coincidentes",
        noRequest: "Seleccione al menos un filtro para obtener resultados",
        filterBy: "Filtrar por {column}",
        loading: "Cargando...",
        defaultOption: "Seleccionar {column}",
        columns: "Columnas"
    }
};

// COMPONENTES
Vue.use(VueConfirmDialog);
Vue.component("vue-confirm-dialog", VueConfirmDialog.default);

Vue.use(ClientTable, {}, false, "bootstrap4", "default");
Vue.use(ServerTable, options, false, "bootstrap4", "default");

Vue.component("vue-cal", VueCal);

Vue.component("v-select", vSelect);
Vue.component("qrcode-vue", QrcodeVue);
// Vue.use(VueExcelXlsx);
// Vue.component("downloadExcel", JsonExcel);

Vue.component("excel-export", require("./components/excel/ExcelComponent.vue").default);
Vue.component("fecha-format", require("./components/format/FechaComponent.vue").default);
Vue.component("hora-format", require("./components/format/HoraComponent.vue").default);
Vue.component("comunicado", require("./components/ComunicadoComponent.vue").default);
Vue.component("example-component", require("./components/ExampleComponent.vue").default);
Vue.component("example-chart", require("./components/intranet/estadistica/ChartComponent.vue").default);

Vue.component("w-form-inscripcion-estudiante", require("./components/web/estudiante/FormInscripcionComponent.vue").default);
Vue.component("w-form-inscripcion-estudiante-extemporaneo", require("./components/web/estudiante/FormInscripcionExtemporaneoComponent.vue").default);
Vue.component("w-simulacro-inscripcion-estudiante", require("./components/web/estudiante/SimulacroInscripcionComponent.vue").default);

Vue.component("w-simulacro-disponibilidad-estudiante", require("./components/web/docente/FormDisponibilidadComponent.vue").default);


Vue.component("w-pago", require("./components/web/PagoComponent.vue").default);

Vue.component("upload-imagen", require("./components/web/estudiante/UploadImagen.vue").default);
Vue.component("upload-imagen-directivo", require("./components/intranet/administrarNosotros/UploadImagen.vue").default);

Vue.component("w-form-inscripcion-docente", require("./components/web/docente/FormInscripcionComponent.vue").default);
Vue.component("w-consultar-local", require("./components/web/ConsultarLocal.vue").default);
Vue.component("w-dashboard-local", require("./components/web/estudiante/DashboardLocal.vue").default);
// COMPONENTES INTRANET DOCENTE
Vue.component("w-perfil-docente", require("./components/web/docente/intranet/PerfilDocenteComponent.vue").default);
Vue.component("w-cursos-docente", require("./components/web/docente/intranet/CursosDocenteComponent.vue").default);
Vue.component("w-horario-docente", require("./components/web/docente/intranet/HorarioDocenteComponent.vue").default);
Vue.component("w-cuadernillo-docente", require("./components/web/docente/intranet/CuadernilloDocenteComponent.vue").default);
Vue.component("w-temario-docente", require("./components/web/docente/intranet/TemarioDocenteComponent.vue").default);
Vue.component("w-cuadernillod-pdf", require("./components/web/docente/intranet/CuadernilloPdfComponent.vue").default);
Vue.component("w-sesiones-docente", require("./components/web/docente/intranet/SesionesDocenteComponent.vue").default);
Vue.component("w-asistencia-docente", require("./components/web/docente/intranet/AsistenciaDocenteComponent.vue").default);
Vue.component("w-ver-estudiantes", require("./components/web/docente/intranet/VerEstudiantesComponent.vue").default);

// COMPONENTES INTRANET ESTUDIANTE
Vue.component("w-matricula-estudiante", require("./components/web/estudiante/intranet/MatriculaEstudianteComponent.vue").default);
Vue.component("w-perfil-estudiante", require("./components/web/estudiante/intranet/PerfilEstudianteComponent.vue").default);
Vue.component("w-cursos-estudiante", require("./components/web/estudiante/intranet/CursosEstudianteComponent.vue").default);
Vue.component("w-horario-estudiante", require("./components/web/estudiante/intranet/HorarioEstudianteComponent.vue").default);
Vue.component("w-cuadernillo-estudiante", require("./components/web/estudiante/intranet/CuadernilloEstudianteComponent.vue").default);
Vue.component("w-cuadernilloe-pdf", require("./components/web/estudiante/intranet/CuadernilloPdfComponent.vue").default);
Vue.component("w-asistencia-estudiante", require("./components/web/estudiante/intranet/AsistenciaEstudianteComponent.vue").default);
Vue.component("w-resumen-pago", require("./components/web/estudiante/intranet/ResumenPagoComponent.vue").default);
Vue.component("w-vouchers-pago", require("./components/web/estudiante/intranet/VouchersPagoComponent.vue").default);
Vue.component("w-pago-normal", require("./components/web/estudiante/intranet/PagoNormalComponent.vue").default);
Vue.component("w-pago-mora", require("./components/web/estudiante/intranet/PagoMoraComponent.vue").default);
Vue.component("w-agregar-pago-cuota", require("./components/web/estudiante/intranet/AgregarPagoCuotaComponent.vue").default);
Vue.component("w-confirmar-datos", require("./components/web/estudiante/intranet/ConfirmarDatosComponent.vue").default);
Vue.component("w-comunicado-habilitado", require("./components/web/estudiante/intranet/ComunicadoHabilitadoComponent.vue").default);

// *************************************
// COMPONENTES DE LA PARTE INTRANET
// *****************************************
//Componentes Dashboard
Vue.component("i-pre-distribucion", require("./components/intranet/dashboard/PreDistribucionComponent.vue").default);
Vue.component("i-pre-grupos", require("./components/intranet/dashboard/PreGruposComponent.vue").default);
Vue.component("i-grupos", require("./components/intranet/dashboard/GruposComponent.vue").default);

// Componentes Inscripciones
Vue.component("i-lista-inscripcion-estudiante", require("./components/intranet/inscripcion/estudiante/ListaComponent.vue").default);
Vue.component("i-actualizar-inscripcion-estudiante", require("./components/intranet/inscripcion/estudiante/ActualizarComponent.vue").default);
Vue.component("i-agregar-pago-estudiante", require("./components/intranet/inscripcion/estudiante/AgregarPagoComponent.vue").default);
Vue.component("i-agregar-mora-estudiante", require("./components/intranet/inscripcion/estudiante/AgregarMoraComponent.vue").default);

Vue.component("i-lista-inscripcion-docente", require("./components/intranet/inscripcion/docente/ListaComponent.vue").default);

Vue.component("i-lista-inscripcion-calificacion-docente", require("./components/intranet/inscripcion/calificacionDocente/ListaComponent.vue").default);
//matricula
Vue.component("i-lista-matricula-pendiente", require("./components/intranet/matricula/PendienteComponent.vue").default);
Vue.component("i-lista-matricula-matriculado", require("./components/intranet/matricula/MatriculadoComponent.vue").default);
Vue.component("i-lista-matricula-habilitar", require("./components/intranet/matricula/HabilitarComponent.vue").default);
//Asistencia Docente
Vue.component("i-lista-asistencia-docente", require("./components/intranet/asistencia/DocenteComponent.vue").default);

//administracion
Vue.component("i-administracion-horario", require("./components/intranet/administracion/HorarioComponent.vue").default);
Vue.component("i-administracion-horario-inscripcion", require("./components/intranet/administracion/HorarioInscripcionComponent.vue").default);
Vue.component("i-administracion-propuesta-horario", require("./components/intranet/administracion/PropuestaHorarioComponent.vue").default);
Vue.component("i-administracion-grupo-aula", require("./components/intranet/administracion/GrupoAulaComponent.vue").default);
Vue.component("i-administracion-docente", require("./components/intranet/administracion/DocenteComponent.vue").default);
Vue.component("i-administracion-estudiante", require("./components/intranet/administracion/EstudianteComponent.vue").default);
Vue.component("i-subir-foto", require("./components/intranet/administracion/SubirFotoComponent.vue").default);
Vue.component("i-administracion-auxiliar", require("./components/intranet/administracion/AuxiliarComponent.vue").default);
Vue.component("i-administracion-coordinador-auxiliar", require("./components/intranet/administracion/CoordinadorAuxiliarComponent.vue").default);
Vue.component("i-administracion-cron-matricula", require("./components/intranet/administracion/CronMatriculaComponent.vue").default);
Vue.component("i-administracion-cron-grupo", require("./components/intranet/administracion/CronGrupoComponent.vue").default);
Vue.component("i-administracion-cron-habilitacion", require("./components/intranet/administracion/CronHabilitacionComponent.vue").default);
Vue.component("i-administracion-cron-docente", require("./components/intranet/administracion/CronDocenteComponent.vue").default);
Vue.component("i-administracion-cron-correo", require("./components/intranet/administracion/CronCorreoComponent.vue").default);
Vue.component("i-administracion-cronograma-pagos", require("./components/intranet/administracion/CronogramaPagos.vue").default);
Vue.component("i-administracion-tarifario-estudiante", require("./components/intranet/administracion/TarifarioEstudianteComponent.vue").default);
Vue.component("i-administracion-vacantes", require("./components/intranet/administracion/VacanteComponent.vue").default);
Vue.component("i-administracion-cron-encuesta", require("./components/intranet/administracion/CronEncuestaComponent.vue").default);

// configuracion
Vue.component("i-configuracion-sede", require("./components/intranet/configuracion/SedeComponent.vue").default);
Vue.component("i-configuracion-local", require("./components/intranet/configuracion/LocalComponent.vue").default);
Vue.component("i-configuracion-aula", require("./components/intranet/configuracion/AulaComponent.vue").default);
Vue.component("i-configuracion-grupo", require("./components/intranet/configuracion/GrupoComponent.vue").default);
Vue.component("i-configuracion-inscripciones", require("./components/intranet/configuracion/InscripcionesComponent.vue").default);
Vue.component("i-configuracion-criterios", require("./components/intranet/configuracion/CriteriosComponent.vue").default);
Vue.component("i-configuracion-tarifas", require("./components/intranet/configuracion/TarifasComponent.vue").default);

Vue.component("i-configuracion-area", require("./components/intranet/configuracion/AreaComponent.vue").default);
Vue.component("i-configuracion-grado-academico", require("./components/intranet/configuracion/GradoAcademicoComponent.vue").default);
Vue.component("i-configuracion-plantilla-horario", require("./components/intranet/configuracion/PlantillaHorarioComponent.vue").default);
Vue.component("i-configuracion-curricula", require("./components/intranet/configuracion/CurriculaComponent.vue").default);

Vue.component("i-configuracion-turnos", require("./components/intranet/configuracion/TurnosComponent.vue").default);
Vue.component("i-configuracion-parentescos", require("./components/intranet/configuracion/ParentescosComponent.vue").default);
Vue.component("i-configuracion-programas", require("./components/intranet/configuracion/ProgramasComponent.vue").default);
Vue.component("i-configuracion-tipo-documento", require("./components/intranet/configuracion/TipoDocumentoComponent.vue").default);

Vue.component("i-configuracion-periodos", require("./components/intranet/configuracion/PeriodoComponent.vue").default);
Vue.component("i-configuracion-cursos", require("./components/intranet/configuracion/CursoComponent.vue").default);
Vue.component("i-configuracion-colegios", require("./components/intranet/configuracion/ColegioComponent.vue").default);

//auxiliar
Vue.component("i-auxiliar-habilitacion", require("./components/intranet/auxiliar/HabilitacionComponent.vue").default);
Vue.component("i-auxiliar-habilitacion-buscar", require("./components/intranet/auxiliar/HabilitacionBuscarComponent.vue").default);
Vue.component("i-auxiliar-horario", require("./components/intranet/auxiliar/HorarioComponent.vue").default);
Vue.component("i-datos-docente", require("./components/intranet/auxiliar/DatosDocenteComponent.vue").default);
Vue.component("i-auxiliar-asistencia-docente", require("./components/intranet/auxiliar/asistencia/DocenteComponent.vue").default);
Vue.component("i-auxiliar-asistencia-estudiante", require("./components/intranet/auxiliar/asistencia/EstudianteComponent.vue").default);
//coordinador auxiliar
Vue.component("i-coordinador-auxiliar-asistencia-docente", require("./components/intranet/coordinadorAuxiliar/asistencia/DocenteComponent.vue").default);
Vue.component("i-coordinador-auxiliar-docente-horario", require("./components/intranet/coordinadorAuxiliar/docente/HorarioComponent.vue").default);
Vue.component("i-coordinador-auxiliar-asistencia-estudiante", require("./components/intranet/coordinadorAuxiliar/asistencia/EstudianteComponent.vue").default);
// Vue.component('i-auxiliar-asistencia-estudiante', require('./components/intranet/auxiliar/asistencia/EstudianteComponent.vue').default);
// coordinador docente
Vue.component("i-coordinadord-cuadernillo", require("./components/intranet/coordinadorDocente/CuadernilloComponent.vue").default);
Vue.component("i-coordinadord-temario", require("./components/intranet/coordinadorDocente/TemarioComponent.vue").default);

// Reportes
Vue.component("i-reporte-estudiante", require("./components/intranet/reporte/ReporteEstudianteComponent.vue").default);
Vue.component("i-reporte-docente", require("./components/intranet/reporte/ReporteDocenteComponent.vue").default);
Vue.component("i-reporte-asistencia-docente", require("./components/intranet/reporte/ReporteAsistenciaDocenteComponent.vue").default);
Vue.component("i-reporte-docente-calificacion", require("./components/intranet/reporte/ReporteDocenteCalificacionComponent.vue").default);
Vue.component("i-reporte-pagos", require("./components/intranet/reporte/ReportePagosComponent.vue").default);
Vue.component("i-reporte-docente-ingresantes", require("./components/intranet/reporte/ReporteDocenteIngresantesComponent.vue").default);
Vue.component("i-reporte-vouchers", require("./components/intranet/reporte/ReporteVouchersComponent.vue").default);

Vue.component("i-estadistica", require("./components/intranet/estadistica/IndexComponent.vue").default);
// ***************GRAFICOS ESTADISTICA**************
Vue.component("i-1reporte-1grafico", require("./components/intranet/estadistica/1Reportes/1GraficoComponent.vue").default);
Vue.component("i-1reporte-2grafico", require("./components/intranet/estadistica/1Reportes/2GraficoComponent.vue").default);
Vue.component("i-1reporte-3grafico", require("./components/intranet/estadistica/1Reportes/3GraficoComponent.vue").default);
Vue.component("i-2reporte-1grafico", require("./components/intranet/estadistica/2Reportes/1GraficoComponent.vue").default);
Vue.component("i-2reporte-2grafico", require("./components/intranet/estadistica/2Reportes/2GraficoComponent.vue").default);
Vue.component("i-2reporte-3grafico", require("./components/intranet/estadistica/2Reportes/3GraficoComponent.vue").default);
Vue.component("i-3reporte-1grafico", require("./components/intranet/estadistica/3Reportes/1GraficoComponent.vue").default);
Vue.component("i-4reporte-1grafico", require("./components/intranet/estadistica/4Reportes/1GraficoComponent.vue").default);
// **************************************************
// Aplicativo
Vue.component("i-aplicativo-comunicado", require("./components/intranet/aplicativo/ComunicadoComponent.vue").default);
// Recursos Humanos
Vue.component("i-expediente-pago-docente", require("./components/intranet/recursosHumanos/pagos/docentes/ExpedienteDocenteComponent.vue").default);
Vue.component("i-habilitacion-pago-docente", require("./components/intranet/recursosHumanos/pagos/docentes/HabilitacionDocenteComponent.vue").default);
Vue.component("i-lista-docente-horas-mes", require("./components/intranet/recursosHumanos/pagos/docentes/HorasMesDocenteComponent.vue").default);
Vue.component("i-recursos-humanos-cron-accesos", require("./components/intranet/recursosHumanos/CronAccesosComponent.vue").default);
//Devoluciones
Vue.component("i-lista-devoluciones", require("./components/intranet/recursosHumanos/devoluciones/DevolucionesComponent.vue").default);

//Libro de Reclamaciones
Vue.component("i-libro-reclamaciones", require("./components/intranet/libroReclamaciones/LibroReclamacionesComponent.vue").default);
//Administrar - nosotros
Vue.component("i-directivos", require("./components/intranet/administrarNosotros/DirectivosComponent.vue").default);
Vue.component("i-objetivos", require("./components/intranet/administrarNosotros/ObjetivosComponent.vue").default);
Vue.component("i-misionvision", require("./components/intranet/administrarNosotros/MisionVisionComponent.vue").default);
Vue.component("i-historia", require("./components/intranet/administrarNosotros/HistoriaComponent.vue").default);
Vue.component("i-ciclo", require("./components/intranet/administrarNosotros/CicloComponent.vue").default);
// Administrar - Usuarios
Vue.component("i-usuario", require("./components/intranet/administrarUsuario/UsuarioComponent.vue").default);
Vue.component("i-permisos", require("./components/intranet/administrarUsuario/PermisosComponent.vue").default);
Vue.component("i-roles", require("./components/intranet/administrarUsuario/RolesComponent.vue").default);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app"
});
