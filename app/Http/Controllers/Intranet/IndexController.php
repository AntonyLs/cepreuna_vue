<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sede;
use App\Models\Area;
use App\Models\Turno;
use App\Models\Locales;
use App\Models\Aula;
use App\Models\GrupoAula;
use App\Models\Periodo;
use App\Models\DocenteApto;
use App\Models\InscripcionDocente;
use App\Models\PlantillaHorario;
use App\Models\Disponibilidades;
use App\Models\CargaAcademica;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\Curso;
use App\Models\Horario;
use App\Models\Grupo;
use App\Models\AuxiliarGrupo;
use App\Models\AsistenciaDocente;
use App\Models\Sesiones;
use App\Models\Docente;
use App\Models\Matricula;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaEstudianteDetalle;
use App\Models\Auxiliar;
use App\Models\Colegio;
use App\Models\ConceptoPago;
use App\Models\Estudiante;
use App\Models\TipoDocumento;
use App\Models\Programa;
use App\Models\GradoAcademico;
use App\Models\MatriculaDetalle;
use App\Models\Pais;
use App\Models\TipoColegio;
use App\Models\Ubigeo;
use App\Models\User;
use App\Models\HorarioInscripciones;
use App\Models\Criterio;
use App\Models\InscripcionDocenteDetalle;
use App\Models\AdjuntoGrado;
use App\Models\AdjuntoExperiencia;
use App\Models\AdjuntoCapacitaciones;
use App\Models\AdjuntoProducciones;
// use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Services\GWorkspace;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class IndexController extends Controller
{
    public function getTipoDocumentos()
    {
        $tipoDocumentos = TipoDocumento::select('id', 'denominacion')->get();

        return response()->json($tipoDocumentos);
    }
    public function getProgramas()
    {
        $programas = Programa::all();

        return response()->json($programas);
    }
    public function getGradosAcademicos()
    {
        $grado = GradoAcademico::all();

        return response()->json($grado);
    }
    public function getSedes(Request $request)
    {
        if (isset($request->all)) {
            $sedes = Sede::get();
        } else {
            $sedes = Sede::where("estado", '1')->get();
        }

        return response()->json($sedes);
    }
    public function getAreas()
    {
        $areas = Area::get();

        return response()->json($areas);
    }


    public function getTurnos()
    {
        $turnos = Turno::where('estado', '1')->get();

        return response()->json($turnos);
    }

    public function getLocales(Request $request)
    {
        // dd($request->all());
        $response = Locales::select("id", "denominacion")
            ->where("sedes_id", $request->sede)->get();
        return response()->json($response);
    }
    public function getOnlyAulas(Request $request)
    {
        $response = Aula::where('locales_id', $request->local)->get();
        return response()->json($response);
    }
    public function getOnlyGrupos()
    {
        $response = Grupo::where('estado', '1')->get();
        return response()->json($response);
    }
    public function getGrupos(Request $request)
    {
        // dd($request);
        $local = $request->local;
        $periodo = Periodo::where("estado", "1")->first();
        $response = GrupoAula::with(["aula", "grupo"])
            ->whereHas('aula', function (Builder $query) use ($local) {
                $query->where('locales_id', $local);
            })
            ->where([
                ["areas_id", $request->area],
                ["turnos_id", $request->turno],
                ["periodos_id", $periodo->id]
            ])
            ->get();

        return response()->json($response);
    }
    public function getGrupoAula(Request $request)
    {
        // $area = $request->area;
        // $turno = $request->turno;
        $response = GrupoAula::with(["aula", "grupo"])
            ->select("grupo_aulas.*", "g.denominacion as grupo")
            ->join("grupos as g", "g.id", "grupo_aulas.grupos_id")
            ->orderBy("g.denominacion", "asc");
        if (isset($request->area)) {
            $response = $response->where("areas_id", $request->area);
        }
        if (isset($request->turno)) {
            $response = $response->where("turnos_id", $request->turno);
        }
        if (isset($request->sede)) {
            $response = $response->join("aulas as a", "a.id", "grupo_aulas.aulas_id")
                ->join("locales as l", "l.id", "a.locales_id")
                ->where("l.sedes_id", $request->sede);
        }
        $response = $response->orderBy("g.denominacion", "asc");
        $response = $response->get();

        return response()->json($response);
    }
    public function getGrupoAulaAux(Request $request)
    {
        // $area = $request->area;
        // $turno = $request->turno;
        $gruposAsignados =  AuxiliarGrupo::select("grupo_aulas_id")->get();
        // dd($gruposAsignados);
        $response = GrupoAula::select("grupo_aulas.id", DB::raw("CONCAT(g.denominacion,'-',s.denominacion) as denominacion"))
            ->join("grupos as g", "g.id", "grupo_aulas.grupos_id")
            ->join("aulas as a", "a.id", "grupo_aulas.aulas_id")
            ->join("locales as l", "l.id", "a.locales_id")
            ->join("sedes as s", "s.id", "l.sedes_id")
            ->whereNotIn('grupo_aulas.id', $gruposAsignados);

        // if (isset($request->sede)) {
        //     $response = $response->join("aulas as a", "a.id", "grupo_aulas.aulas_id")
        //         ->join("locales as l", "l.id", "a.locales_id")
        //         ->where("l.sedes_id", $request->sede);
        // }
        $response = $response->orderBy("s.denominacion", "asc");
        $response = $response->orderBy("g.denominacion", "asc")
            ->get();

        return response()->json($response);
    }

    public function getDocente(Request $request)
    {

        $periodo = Periodo::where("estado", "1")->first();
        // $area = $request->area;
        // $turno = $request->turno;
        // $response = DocenteApto::with(["docente"])
        //             // ->pluck("id")
        //             ->whereHas('docente', function (Builder $query) use ($request) {
        //                 $query->where('nro_documento', 'like', "%$request->q%");
        //             })
        //             ->where("periodos_id",$periodo->id)->get();
        // ->where(DB::raw('CONCAT(denominacion," ",distrito)'), 'like', "%$request->q%")->get()
        $response = DB::table("docente_aptos as da")
            ->select(
                "d.id",
                DB::raw("CONCAT(d.nro_documento,' | ',d.paterno,' ',d.materno,' ',d.nombres, ' | ',d.celular) as text")
            )
            ->join("docentes as d", "d.id", "da.docentes_id")
            // ->where('d.nro_documento', 'like', "%$request->q%")
            ->get();
        // dd($response);
        return response()->json($response);
    }
    public function getDisponibilidad(Request $request)
    {

        $periodo = Periodo::where("estado", "1")->first();
        if (isset($request->turno)) {
            $turno = Turno::where("id", $request->turno)->get();
        } else {
            $turno = Turno::get();
        }
        $inscripcionDocente = InscripcionDocente::where("docentes_id", $request->docente)->where("periodos_id", $periodo->id)->first();
        // dd($inscripcionDocente);
        $cursosAreas = [];
        $curricula = Curricula::with("area")->where("estado", "1")->get();
        // $areaDisponibilidad = Area::get();
        foreach ($curricula as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->area = $value->area->denominacion;
            $objeto->cursos = DB::table("inscripcion_cursos as ic")
                ->select(
                    "c.denominacion"
                )
                ->leftJoin("curricula_detalles as cd", "cd.id", "ic.curricula_detalles_id")
                ->leftJoin("cursos as c", "c.id", "cd.cursos_id")
                ->where("ic.inscripcion_docentes_id", $inscripcionDocente->id)
                ->where("cd.curriculas_id", $value->id)
                ->get();
            $cursosAreas[] = $objeto;
        }
        $response["cursos"] = $cursosAreas;

        // ******************disponilidad inscripcion***********
        $disponibilidad = [];
        foreach ($turno as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->turno = $value->denominacion;

            $plantillaHorario = [];
            $plantilla = PlantillaHorario::select(
                "id",
                DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
                DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
                "tipo"
            )
                ->where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $obj = new \stdClass;
                $obj->hora_inicio = $val->horaInicio;
                $obj->hora_fin = $val->horaFin;
                $obj->tipo = $val->tipo;
                $obj->disponibilidad = Disponibilidades::with(['sede'])
                    ->where("inscripcion_docentes_id", $inscripcionDocente->id)
                    ->where("plantilla_horarios_id", $val->id)
                    ->orderBy("dia", "asc")
                    ->get();
                $plantillaHorario[] = $obj;
            }
            $objeto->disponibilidad = $plantillaHorario;
            $disponibilidad[] =  $objeto;
        }
        $response["disponibilidad"] = $disponibilidad;
        // ******************disponilidad horario***********
        $horario = [];
        foreach ($turno as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->turno = $value->denominacion;

            $plantillaHorario = [];
            $plantilla = PlantillaHorario::select(
                "id",
                DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
                DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
                "tipo"
            )
                ->where("estado", "1")
                ->where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $obj = new \stdClass;
                $obj->hora_inicio = $val->horaInicio;
                $obj->hora_fin = $val->horaFin;
                $obj->tipo = $val->tipo;
                $obj->horario = Horario::with(['curso', 'carga'])
                    ->select("horarios.*", "g.denominacion as grupo", "s.denominacion as sede")
                    ->whereHas('carga', function (Builder $query) use ($request) {
                        $query->where('docentes_id', $request->docente);
                    })
                    ->join("carga_academicas as ca", "ca.id", "horarios.carga_academicas_id")
                    ->join("grupo_aulas as ga", "ga.id", "ca.grupo_aulas_id")
                    ->join("grupos as g", "g.id", "ga.grupos_id")
                    ->join("aulas as a", "a.id", "ga.aulas_id")
                    ->join("locales as l", "l.id", "a.locales_id")
                    ->join("sedes as s", "s.id", "l.sedes_id")
                    // ->where("carga_academicas_id",$inscripcionDocente->id)
                    ->where("plantilla_horarios_id", $val->id)
                    ->where("ca.estado", "1")
                    ->orderBy("dia", "asc")
                    ->get();
                $plantillaHorario[] = $obj;
            }
            $objeto->disponibilidad = $plantillaHorario;
            $horario[] =  $objeto;
        }
        $response["horario"] = $horario;

        $response["carga_academica"] = CargaAcademica::with(["grupoAula", "curso"])->where("docentes_id", $request->docente)->get();
        $response["docente"] = Docente::find($request->docente);

        return response()->json($response);
    }
    public function getCargaAcademica(Request $request)
    {
        $periodo = Periodo::where("estado", "1")->first();

        $cargaAcademica = CargaAcademica::with(["curso", "docente"])
            ->select('carga_academicas.*', 'da.usuario')
            ->leftJoin('docente_aptos as da', 'da.docentes_id', 'carga_academicas.docentes_id')
            ->where("carga_academicas.grupo_aulas_id", $request->grupo);
        if (isset($request->estado)) {
            $cargaAcademica =  $cargaAcademica->where("carga_academicas.estado", "1");
        }
        if (isset($request->tipo)) {
            $cargaAcademica = $cargaAcademica->where("carga_academicas.tipo", "1");
        }
        $cargaAcademica = $cargaAcademica->where("carga_academicas.periodos_id", $periodo->id)
            ->orderBy("carga_academicas.cursos_id", "asc")
            ->orderBy("carga_academicas.tipo", "asc")
            ->get();

        $response["cargaAcademica"] = $cargaAcademica;

        $grupoAula = GrupoAula::find($request->grupo);
        $response["turno"] = Turno::find($grupoAula->turnos_id);
        $plantillaHorario = [];
        $plantilla = PlantillaHorario::select(
            "id",
            DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
            DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
            "tipo"
        )
            ->where("turnos_id", $grupoAula->turnos_id)
            ->where("estado", "1")
            ->get();

        foreach ($plantilla as $k => $val) {
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->hora_inicio = $val->horaInicio;
            $obj->hora_fin = $val->horaFin;
            $obj->tipo = $val->tipo;
            $obj->horario = Horario::with(['curso', 'carga'])
                ->whereHas('carga', function (Builder $query) use ($request) {
                    $query->where('grupo_aulas_id', $request->grupo)
                        ->where("estado", "1");
                })

                // ->where("carga_academicas_id",$inscripcionDocente->id)
                ->where("plantilla_horarios_id", $val->id)
                // ->where("ca.estado","1")
                ->orderBy("dia", "asc")
                ->get();
            $plantillaHorario[] = $obj;
        }
        $response["horario"] = $plantillaHorario;
        return response()->json($response);
    }
    public function getGrupoAulaAuxiliar(Request $request)
    {

        if (auth()->user()->hasRole('Super Admin|Administrador|Coordinador Auxiliar|Coordinador Auxiliar 2|Computo')) {
            $response = GrupoAula::select("grupo_aulas.*", DB::raw("CONCAT(s.denominacion,' ',g.denominacion) as grupo"))
                ->join("grupos as g", "g.id", "grupo_aulas.grupos_id");
        } else {
            $id = Auth::user()->id;
            $auxiliar = Auxiliar::where("users_id", $id)->first();
            $response = AuxiliarGrupo::select("grupo_aulas.*", DB::raw("CONCAT(s.denominacion,' ',g.denominacion) as grupo"))
                ->join("grupo_aulas", "grupo_aulas.id", "auxiliar_grupos.grupo_aulas_id")
                ->join("grupos as g", "g.id", "grupo_aulas.grupos_id")
                ->where("auxiliar_grupos.auxiliares_id", $auxiliar->id);
        }
        $response = $response->join("aulas as a", "a.id", "grupo_aulas.aulas_id")
            ->join("locales as l", "l.id", "a.locales_id")
            ->join("sedes as s", "s.id", "l.sedes_id")
            ->orderBy("g.denominacion", "asc");
        // $response = GrupoAula::select("grupo_aulas.*", "g.denominacion as grupo")
        //     ->join("grupos as g", "g.id", "grupo_aulas.grupos_id")
        //     ->orderBy("g.denominacion", "asc");
        // ->where("ag.users_id",$id);
        if (isset($request->sede)) {
            $response = $response->where("l.sedes_id", $request->sede);
        }
        if (isset($request->area)) {
            $response = $response->where("areas_id", $request->area);
        }
        if (isset($request->turno)) {
            $response = $response->where("turnos_id", $request->turno);
        }
        $response = $response->get();

        return response()->json($response);
    }
    public function getCargaAcademicaAsistencia(Request $request)
    {
        // dd($request->fecha);
        $fecha = new \DateTime($request->fecha);
        $semana = $fecha->format("N");
        $dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes"];

        $response["dia"] = $dias[(int)($semana - 1)];
        $response["fecha"] = $fecha->format("d/m/Y");

        $periodo = Periodo::where("estado", "1")->first();

        $response["cargaAcademica"] = CargaAcademica::with(["curso", "docente"])
            ->select('carga_academicas.*', 'da.usuario')
            ->leftJoin('docente_aptos as da', 'da.docentes_id', 'carga_academicas.docentes_id')
            ->where("carga_academicas.grupo_aulas_id", $request->grupo)
            ->where("carga_academicas.periodos_id", $periodo->id)
            ->where("carga_academicas.estado", "1")
            ->orderBy("carga_academicas.cursos_id", "asc")
            ->orderBy("carga_academicas.tipo", "asc")
            ->get();


        $grupoAula = GrupoAula::find($request->grupo);
        // dd($grupoAula);
        $response["turno"] = Turno::find($grupoAula->turnos_id);
        $plantillaHorario = [];
        $plantilla = PlantillaHorario::select(
            "id",
            DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
            DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
            "tipo"
        )
            ->where("turnos_id", $grupoAula->turnos_id)
            // ->where("dia",$semana)
            ->where("estado", "1")
            ->get();

        foreach ($plantilla as $k => $val) {
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->hora_inicio = $val->horaInicio;
            $obj->hora_fin = $val->horaFin;
            $obj->tipo = $val->tipo;
            $obj->horario = Horario::with(['curso', 'carga'])
                ->select("horarios.*", "ad.estado", "ad.id as idAsistencia", "ad.docentes_id as IdDocente")
                ->whereHas('carga', function (Builder $query) use ($request) {
                    $query->where('grupo_aulas_id', $request->grupo)
                        ->where("estado", "1");
                })
                // ->leftJoin("asistencia_docentes as ad","ad.carga_academicas_id","horarios.carga_academicas_id")
                ->leftJoin('asistencia_docentes as ad', function ($join) use ($fecha) {
                    $join->on('ad.carga_academicas_id', '=', 'horarios.carga_academicas_id')
                        ->where('ad.fecha', '=', $fecha);
                })
                // ->where("carga_academicas_id",$inscripcionDocente->id)
                ->where("plantilla_horarios_id", $val->id)
                ->where("dia", $semana)
                ->orderBy("dia", "asc")
                ->first();
            $plantillaHorario[] = $obj;
        }
        $response["horario"] = $plantillaHorario;
        return response()->json($response);
    }
    public function getCargaAcademicaRegularizar(Request $request)
    {
        $periodo = Periodo::where("estado", "1")->first();

        $response["cargaAcademica"] = CargaAcademica::with(["curso", "docente"])
            ->select('carga_academicas.*', 'da.usuario')
            ->leftJoin('docente_aptos as da', 'da.docentes_id', 'carga_academicas.docentes_id')
            ->where("carga_academicas.grupo_aulas_id", $request->grupo)
            ->where("carga_academicas.periodos_id", $periodo->id)
            // ->where("carga_academicas.estado","1")
            ->orderBy("carga_academicas.cursos_id", "asc")
            ->orderBy("carga_academicas.tipo", "asc")
            ->get();
        $response["plantilla"] = PlantillaHorario::where([["estado", "1"], ["tipo", "1"]])->get();
        return $response;
    }
    public function getSesiones(Request $request)
    {
        // dd($request->dicenbt);
        $response["sesion"] = Sesiones::where([
            ["carga_academicas_id", $request->carga],
            ["fecha", $request->fecha]
        ])
            ->first();
        $response["docente"] = Docente::find($request->docente);
        $response["carga"] = CargaAcademica::find($request->carga);
        return $response;
    }
    public function getAsistenciaDocente(Request $request)
    {
        $response["asistencia"] = AsistenciaDocente::with("sesiones")->find($request->asistencia);
        return $response;
    }
    public function getAsistenciaEstudiante(Request $request)
    {
        // dd($request);
        $periodo = Periodo::where("estado", "1")->first();
        $asistencia = AsistenciaEstudiante::where([
            ["grupo_aulas_id", $request->grupo],
            ["fecha", $request->fecha]
        ])
            ->first();
        // dd($asistencia);
        $response["status"] = false;
        $response["observacion"] = '';
        if ($asistencia) {
            $response["asistencia"] = $asistencia;
            $response["id"] = $asistencia->id;
            $response["observacion"] = $asistencia->observacion;
            $response["estudiantes"] = AsistenciaEstudianteDetalle::with("estudiante")
                // ->whereHas('estudiante', function (Builder $query) {
                //     $query->orderBy('paterno',"asc");
                // })
                ->select("asistencia_estudiante_detalles.*")
                ->join("estudiantes as e", "e.id", "asistencia_estudiante_detalles.estudiantes_id")
                ->where([
                    ["asistencia_estudiantes_id", $asistencia->id]
                ])
                ->orderBy('e.paterno', "asc")
                ->orderBy('e.materno', "asc")
                ->orderBy('e.nombres', "asc")
                ->get();
            $response["status"] = true;
        } else {
            $response["estudiantes"] = Matricula::with("estudiante")
                ->select("matriculas.*")
                // ->whereHas('estudiante', function (Builder $query) {
                //     $query->orderBy('paterno',"asc");
                // })
                ->join("estudiantes as e", "e.id", "matriculas.estudiantes_id")
                ->where([
                    ["grupo_aulas_id", $request->grupo],
                    ["periodos_id", $periodo->id]
                ])
                ->orderBy('e.paterno', "asc")
                ->orderBy('e.materno', "asc")
                ->orderBy('e.nombres', "asc")
                ->get();
            $response["status"] = false;
        }


        return $response;
    }
    public function getAsistenciaEstudianteExcel($id)
    {
        $estudiantes = AsistenciaEstudianteDetalle::with("estudiante")
            // ->whereHas('estudiante', function (Builder $query) {
            //     $query->orderBy('paterno',"asc");
            // })
            ->select("asistencia_estudiante_detalles.*")
            ->join("estudiantes as e", "e.id", "asistencia_estudiante_detalles.estudiantes_id")
            ->where([
                ["asistencia_estudiantes_id", $id]
            ])
            ->orderBy('e.paterno', "asc")
            ->orderBy('e.materno', "asc")
            ->orderBy('e.nombres', "asc")
            ->get();
        $asistencia = AsistenciaEstudiante::with("grupo")->find($id);
        $usuario = User::find($asistencia->users_id);

        $fecha = date("d/m/Y", strtotime($asistencia->fecha));
        $hora = date("H:i:s", strtotime($asistencia->created_at));

        $datos = [];
        foreach ($estudiantes as $key => $value) {
            $estado = "Presente";
            switch ($value->estado) {
                case '1':
                    $estado = "Presente";
                    break;
                case '2':
                    $estado = "Tarde";
                    break;
                case '3':
                    $estado = "Falta";
                    break;

                default:
                    # code...
                    break;
            }
            $objeto = new \stdClass;
            $objeto->dni = $value->estudiante->nro_documento;
            $objeto->paterno = $value->estudiante->paterno;
            $objeto->materno = $value->estudiante->materno;
            $objeto->nombres = $value->estudiante->nombres;
            $objeto->fecha = $fecha;
            $objeto->estado = $estado;
            $objeto->usuario = $usuario->paterno . ' ' . $usuario->materno . ' ' . $usuario->name;
            $objeto->hora = $hora;

            $datos[] = $objeto;
        }
        $response["datos"] = $datos;
        return $response;
    }
    public function getAsistenciaDocenteExcel(Request $request)
    {
        $asistencia = AsistenciaDocente::with(["docente", "carga", "sesiones", "user"])
            ->where("fecha", $request->fecha)
            ->whereHas('carga', function (Builder $query) use ($request) {
                $query->where('grupo_aulas_id', $request->grupo);
                // ->where("estado", "1");
            })
            ->get();
        // dd($asistencia);
        $datos = [];
        foreach ($asistencia as $key => $value) {
            $estado = "Presente";
            switch ($value->estado) {
                case '1':
                    $estado = "Presente";
                    break;
                case '2':
                    $estado = "Tarde";
                    break;
                case '3':
                    $estado = "Falta";
                    break;

                default:
                    # code...
                    break;
            }

            $objeto = new \stdClass;
            $objeto->dni = $value->docente->nro_documento;
            $objeto->paterno = $value->docente->paterno;
            $objeto->materno = $value->docente->materno;
            $objeto->nombres = $value->docente->nombres;
            $objeto->curso = $value->carga->curso->denominacion;
            $objeto->estado = $estado;
            $objeto->tema = isset($value->sesiones->tema) ? $value->sesiones->tema : "";
            $objeto->fecha = date("d/m/Y h:i a", strtotime($value->created_at));
            $objeto->usuario = $value->user->paterno . ' ' . $value->user->materno . ' ' . $value->user->name;
            $objeto->observacion = $value->observacion;

            $datos[] = $objeto;
        }
        $response["datos"] = $datos;
        return $response;
    }
    public function getListaEstudianteExcel($id)
    {
        $estudiantes = Matricula::with("estudiante")
            ->select(
                "matriculas.*",
                DB::raw("(SELECT GROUP_CONCAT(a.celular SEPARATOR  ' | ')
                FROM estudiante_apoderados AS ea
                JOIN apoderados as a ON a.id=ea.apoderados_id
                WHERE
                    matriculas.estudiantes_id = ea.estudiantes_id

                    GROUP BY ea.estudiantes_id
                ) AS Apoderados")
            )
            // ->whereHas('estudiante', function (Builder $query) {
            //     $query->orderBy('paterno',"asc");
            // })
            ->join("estudiantes as e", "e.id", "matriculas.estudiantes_id")

            ->where([
                ["grupo_aulas_id", $id]
            ])
            ->orderBy('e.paterno', "asc")
            ->orderBy('e.materno', "asc")
            ->orderBy('e.nombres', "asc")
            ->get();
        $grupoAula = GrupoAula::with(["grupo", "turno", "area", "aula"])->find($id);

        $datos = [];
        foreach ($estudiantes as $key => $value) {
            // $apoderado = EstudianteApoderado::with("apoderado")->where("estudiasntes_id",)
            $objeto = new \stdClass;
            $objeto->dni = $value->estudiante->nro_documento;
            $objeto->paterno = $value->estudiante->paterno;
            $objeto->materno = $value->estudiante->materno;
            $objeto->nombres = $value->estudiante->nombres;
            $objeto->correo = $value->estudiante->usuario;
            $objeto->telefono = $value->estudiante->celular;
            $objeto->apoderado = $value->Apoderados;

            $datos[] = $objeto;
        }
        $response["datos"] = $datos;
        return $response;
    }
    public function getRol()
    {

        $response = DB::table('model_has_roles')
            ->select('role_id')
            ->where('model_id', Auth::user()->id)
            ->first();

        return response()->json($response);
    }
    public function getRoles()
    {
        $response = Role::where([['id', '!=', '1'], ['guard_name', 'web']])->get();

        return $response;
    }
    public function getPermisos()
    {
        $response = Permission::get();

        return $response;
    }
    public function getCursosCurricula(Request $request)
    {

        $curricula = Curricula::where('areas_id', $request->area)->first();
        // $response = CurriculaDetalle::with('curso')->where('curriculas_id',$curricula->id)->get();
        $response = DB::table('curricula_detalles as cd')
            ->select('cd.id', 'c.denominacion')
            ->join('cursos as c', 'c.id', 'cd.cursos_id')
            ->where('cd.curriculas_id', $curricula->id)
            ->get();
        return response()->json($response);
    }
    public function getCursos()
    {
        $response = Curso::all();
        return $response;
    }
    public function getDocenteCursos(Request $request)
    {
        // dd($request);
        $cursosAreas = [];
        $curricula = Curricula::with("area")->where("estado", "1")->get();
        // $areaDisponibilidad = Area::get();
        foreach ($curricula as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->area = $value->area->denominacion;
            // $objeto->cursos = DB::table("inscripcion_cursos as ic")
            //                 ->select(
            //                     // "c.denominacion"
            //                     "cd.*"
            //                 )
            //                 ->rightJoin("curricula_detalles as cd","ic.curricula_detalles_id","cd.id")
            //                 // ->join("cursos as c","c.id","cd.cursos_id")
            //                 ->where("ic.inscripcion_docentes_id",$request->inscripcion)
            //                 ->where("cd.curriculas_id",$value->id)
            //                 ->get();
            $objeto->cursos = CurriculaDetalle::with("curso")
                ->select("curricula_detalles.*", "ic.curricula_detalles_id as select")
                // ->rightJoin("inscripcion_cursos as ic","ic.curricula_detalles_id","curricula_detalles.id")
                ->leftJoin("inscripcion_cursos as ic", function ($join) use ($request) {
                    $join->on('ic.curricula_detalles_id', '=', 'curricula_detalles.id')
                        ->where('inscripcion_docentes_id', $request->inscripcion);
                })
                ->where("curricula_detalles.curriculas_id", $value->id)
                ->get();
            $cursosAreas[] = $objeto;
        }
        // $response["cursos"] = $cursosAreas;
        return $cursosAreas;
    }
    public function getDocenteDisponibilidad(Request $request)
    {
        $sede = Disponibilidades::with("sede")->select("sedes_id", "prioridad")->where("inscripcion_docentes_id", $request->inscripcion)
            ->groupBy("sedes_id", "prioridad")->get();

        $turno = Turno::get();
        $horario = [];
        foreach ($turno as $key => $value) {
            $obj = new \stdClass;
            $obj->id = $value->id;
            $obj->turno = $value->denominacion;
            $obj->horario = plantillaHorario::where("turnos_id", $value->id)->get();
            $horario[] =  $obj;
        }
        $disponibilidad = Disponibilidades::where("inscripcion_docentes_id", $request->inscripcion)->get();
        $lu = $ma = $mi = $ju = $vi = [];
        // foreach ($disponibilidad as $key => $value) {
        //     switch ($value->dia) {
        //         case '1':
        //             $lu[$value->plantilla_horarios_id] = '1-'.$value->plantilla_horarios_id.'-'.$value->sedes_id;
        //             break;
        //         case '2':
        //             $ma[$value->plantilla_horarios_id] = '1-'.$value->plantilla_horarios_id.'-'.$value->sedes_id;
        //             break;
        //         case '3':
        //             $mi[$value->plantilla_horarios_id] = '1-'.$value->plantilla_horarios_id.'-'.$value->sedes_id;
        //             break;
        //         case '4':
        //             $ju[$value->plantilla_horarios_id] = '1-'.$value->plantilla_horarios_id.'-'.$value->sedes_id;
        //             break;
        //         case '5':
        //             $vi[$value->plantilla_horarios_id] = '1-'.$value->plantilla_horarios_id.'-'.$value->sedes_id;
        //             break;

        //         default:

        //             break;
        //     }
        // }

        // $
        $response["disponibilidad"] = $disponibilidad;
        $response["sede"] = $sede;
        $response["horario"] = $horario;
        return $response;
    }

    public function getPaises()
    {
        $paises = Pais::get();

        return response()->json($paises);
    }
    public function getDepartamentos()
    {
        $departamentos = Ubigeo::select('codigo_departamento', 'departamento')->distinct()->get();

        return response()->json($departamentos);
    }
    public function getProvincias(Request $request)
    {
        $provincias = Ubigeo::select('codigo_provincia', 'provincia')->distinct()->where('codigo_departamento', $request->codigo)->get();

        return response()->json($provincias);
    }
    public function getDistritos(Request $request)
    {
        $distritos = Ubigeo::select('id', 'distrito')->distinct()->where('codigo_provincia', $request->codigo)->get();

        return response()->json($distritos);
    }
    public function getColegios(Request $request)
    {
        // dd($request->q);
        $colegios = Colegio::select(
            DB::raw('CONCAT(denominacion," ",distrito) as denominacion'),
            'id',
            'tipo_colegios_id'
        )
            ->where(DB::raw('CONCAT(denominacion," ",distrito)'), 'like', "%$request->q%")->get();

        return response()->json($colegios);
    }
    public function getTipoColegios()
    {
        // dd($request->q);
        $tipoColegios = TipoColegio::select(
            'denominacion',
            'id'
        )->get();

        return response()->json($tipoColegios);
    }
    public function getConceptos()
    {
        $conceptos = ConceptoPago::select(
            'denominacion',
            'id'
        )->get();
        return response()->json($conceptos);
    }
    public function eliminarCorreo($id)
    {
        DB::beginTransaction();
        try {
            $estudiante = Estudiante::find($id);
            $estudiante->idgsuite = '';
            $estudiante->save();

            $apiGsuite = new GWorkspace();
            $datos = json_encode(array(
                "userId" => $estudiante->usuario,
            ));
            $eliminar = json_decode($apiGsuite->eliminarCorreo($datos));

            DB::commit();

            $response["message"] = 'Correo eliminado correctamente';
            $response["status"] =  true;

            if ($eliminar->success == false) {
                $response["message"] = 'El correo que intenta eliminar no existe, o ya ah sido eliminado.';
                $response["status"] =  false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function getCargaAcademicaEstudiante($id)
    {
        $response["matricula"] = Matricula::where("estudiantes_id", $id)->first();
        // dd($matricula);
        $response["detalles"] = MatriculaDetalle::with(["cargaAcademica", "curso"])->where("matriculas_id", $response["matricula"]->id)->get();
        return $response;
    }
    public function getCurriculaDetalle($id)
    {
        $curricula = Curricula::where("areas_id", $id)->first();
        $response["cursos"] = CurriculaDetalle::with("curso")->where("curriculas_id", $curricula->id)->get();

        $turno = Turno::get();
        $horario = [];
        foreach ($turno as $key => $value) {
            $obj = new \stdClass;
            $obj->id = $value->id;
            $obj->turno = $value->denominacion;
            // $obj->horario = plantillaHorario::where("turnos_id",$value->id)->get();
            $plantilla = plantillaHorario::where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $objeto_plantilla = new \stdClass;
                $objeto_plantilla->id = $val->id;
                $objeto_plantilla->hora_inicio = $val->hora_inicio;
                $objeto_plantilla->hora_fin = $val->hora_fin;
                $objeto_plantilla->horario = HorarioInscripciones::with('curso')
                    ->where("plantilla_horarios_id", $val->id)
                    ->where("areas_id", $id)
                    // ->whereIn("curricula_detalles_id",$request->cursos)
                    ->orderBy("dia", "asc")
                    ->get();
                $obj->plantilla[] = $objeto_plantilla;
            }
            $horario[] =  $obj;
        }
        $response["horarios"] = $horario;
        return $response;
    }
    public function getCriterioDocenteInscripcion($id)
    {
        $calificacion = InscripcionDocenteDetalle::with("criterio")->where("inscripcion_docentes_id", $id)
            ->orderBy("criterios_id", "asc")
            ->get();
        if (count($calificacion) > 0) {
            // $data = $calificacion;
            $data = Criterio::select("criterios.*", "idd.puntaje as Puntos")
                // ->rightJoin("inscripcion_docente_detalles as idd","idd.criterios_id","criterios.id")
                ->leftJoin('inscripcion_docente_detalles as idd', function ($join) use ($id) {
                    $join->on("idd.criterios_id", "=", "criterios.id")
                        ->where('idd.inscripcion_docentes_id', $id);
                })
                // ->where("idd.inscripcion_docentes_id", $id)
                ->where("criterios.tipo", "3")->where("criterios.estado", "1")->get();
            // dd($data);
            $status = true;
            $varibles = [];
            foreach ($data as $key => $value) {
                $varibles[$value->id] = $value->Puntos != NULL ? $value->Puntos : 0;
            }
            $puntaje = $varibles;
        } else {
            $data = Criterio::where("tipo", "3")->where("estado", "1")->get();
            $status = false;
            // $puntaje[] = [];
            $varibles = [];
            foreach ($data as $key => $value) {
                $varibles[$value->id] = $value->Puntos != NULL ? $value->Puntos : 0;
            }
            $puntaje = $varibles;
        }
        // $response = Criterio::where("tipo","3")->get();
        $response["data"] = $data;
        $response["puntaje"] = $puntaje;
        $response["status"] = $status;
        return $response;
    }
    public function getInscripcionFile($id)
    {
        $response["grado"] = AdjuntoGrado::with(["gradoAcademico", "programa"])->where("inscripcion_docentes_id", $id)->get();
        $response["experiencia"] = AdjuntoExperiencia::with("experiencia")->where("inscripcion_docentes_id", $id)->get();
        $response["capacitacion"] = AdjuntoCapacitaciones::with("capacitacionTipo")->where("inscripcion_docentes_id", $id)->get();
        $response["produccion"] = AdjuntoProducciones::with("produccion")->where("inscripcion_docentes_id", $id)->get();

        return $response;
    }
    public function getPropuestaHorario(Request $request)
    {

        $curricula = Curricula::where("areas_id", $request->area)->first();
        $cursos = CurriculaDetalle::with("curso")->where("curriculas_id", $curricula->id)->get();

        $response["turno"] = Turno::find($request->turno);
        $horario = [];
        $idPlantilla = [];
        $plantilla = plantillaHorario::where("turnos_id", $request->turno)->get();
        foreach ($plantilla as $k => $val) {
            $idPlantilla[] = $val->id;
            $objeto_plantilla = new \stdClass;
            $objeto_plantilla->id = $val->id;
            $objeto_plantilla->hora_inicio = $val->hora_inicio;
            $objeto_plantilla->hora_fin = $val->hora_fin;
            $objeto_plantilla->horario = HorarioInscripciones::with('curso')
                ->where("plantilla_horarios_id", $val->id)
                ->where("areas_id", $request->area)
                // ->whereIn("curricula_detalles_id",$request->cursos)
                ->orderBy("dia", "asc")
                ->get();
            $horario[] = $objeto_plantilla;
        }
        $response["horarios"] = $horario;

        $carga = [];
        foreach ($cursos as $key => $value) {
            $obj = new \stdClass;
            $obj->id = $value->id;
            $obj->color = $value->curso->color;
            $obj->denominacion = $value->curso->denominacion;
            $aptos = InscripcionDocente::with("docente")->select("inscripcion_docentes.*")
                ->join("inscripcion_cursos as ic", "ic.inscripcion_docentes_id", "inscripcion_docentes.id")
                ->where("inscripcion_docentes.apto", "1")
                ->where("ic.curricula_detalles_id", $value->id)
                ->orderBy("ic.prioridad", "asc")
                ->orderBy("puntaje", "desc")
                ->get();
            $docentesDisponible = [];
            foreach ($aptos as $k => $val) {
                // $docente = Disponibilidades::select("disponibilidades.*")
                //                 ->where("sedes_id",$request->sede)
                //                 ->whereIn("plantilla_horarios_id",$idPlantilla)
                //                 ->where("inscripcion_docentes_id",$val->id)
                //                 ->get();
                $horarioInscripcion = HorarioInscripciones::where("curricula_detalles_id", $value->id)
                    ->whereIn("plantilla_horarios_id", $idPlantilla)
                    ->get();

                $estado = true;
                if (count($horarioInscripcion) > 0) {
                    foreach ($horarioInscripcion as $h) {
                        $disponibilidad = Disponibilidades::where("dia", $h->dia)
                            ->where("plantilla_horarios_id", $h->plantilla_horarios_id)
                            ->where("inscripcion_docentes_id", $val->id)
                            ->where("sedes_id", $request->sede)
                            ->get();
                        if (count($disponibilidad) == 0) {
                            $estado = false;
                        }
                    }
                } else {
                    $estado = false;
                }

                if ($estado) {
                    $objDisponible =  new \stdClass;
                    $objDisponible->id = $val->id;
                    $objDisponible->documento = $val->docente->nro_documento;
                    $objDisponible->docente = $val->docente->paterno . " " . $val->docente->materno . " " . $val->docente->nombres;
                    $docentesDisponible[] = $objDisponible;
                }
            }
            $obj->docente = isset($docentesDisponible[$request->grupo - 1]) ? $docentesDisponible[$request->grupo - 1] : new \stdClass;
            $carga[] = $obj;
        }
        $response["cargas"] = $carga;

        return $response;
    }
    public function getDocumentos()
    {

        $documentos = DB::connection("mysql2")->table("tipo_documentos")->get();

        return response()->json($documentos);
    }
    public function getMeses()
    {
        $meses = DB::connection("mysql2")->table("meses")->get();

        return response()->json($meses);
    }
}
