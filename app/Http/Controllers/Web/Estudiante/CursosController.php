<?php

namespace App\Http\Controllers\Web\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\CalificacionDocente;
use App\Models\CalificacionDocenteDetalle;
use App\Models\CargaAcademica;
use App\Models\Criterio;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\Matricula;
use App\Models\Periodo;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:estudiante');
    }
    public function index()
    {
        $idEstudiante = Auth::user()->id;
        // dd($idEstudiante);
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();

        $periodo = Periodo::where('estado', '1')->first();

        // $docenteApto = DocenteApto::find($idDocenteApto);
        // // $carga = CargaAcademica::with('curso')->select('link')->where('docentes_id',$docenteApto->docentes_id)->get();
        $calificar = array();
        $cargas = CargaAcademica::with(["curso", "docente", "grupoAula"])
            ->where("grupo_aulas_id", $matricula->grupo_aulas_id)
            ->where("periodos_id", $periodo->id)
            ->where("estado", "1")
            ->orderBy("cursos_id")
            ->get();

        // $calificacionDocente = CalificacionDocente::with("asistenciaDocente")->where([["carga_academicas_id", 34], ["estado", "1"]])->orderBy("id", "desc")->first();
        // dd($calificacionDocente);
        foreach ($cargas as $carga) {
            // echo $carga->id . " -";
            $calificacionDocente = CalificacionDocente::with("asistenciaDocente", "docente")->where([["carga_academicas_id", $carga->id], ["estado", "1"]])->orderBy("id", "desc")->first();
            // $calificacionDocente = CalificacionDocente::with("asistenciaDocente")->where([["carga_academicas_id", 34], ["estado", "0"]])->orderBy("id", "desc")->first();
            if (isset($calificacionDocente)) {
                $fechaHoraFin = new DateTime($calificacionDocente->asistenciaDocente->fecha . " " . $calificacionDocente->asistenciaDocente->hora_fin);
                // $fechaHoraFin = new DateTime("2021-12-02 11:03:00");
                // $fechaHoraFin->modify('+24 hour');
                $fechaHoraFin->modify('+15 minutes');
                $fechaActual = new DateTime();

                if ($fechaActual > $fechaHoraFin) {
                    $calificacionDocente->estado = "0";
                    $calificacionDocente->save();
                } else {
                    $calificacionEstudiante = CalificacionDocenteDetalle::where([["estudiantes_id", $idEstudiante], ["calificacion_docentes_id", $calificacionDocente->id]])->get();
                    if (count($calificacionEstudiante) == 0) {
                        array_push($calificar, $calificacionDocente);
                    }
                }
            }
        }
        $response["calificar"] = json_encode($calificar);

        return view('web.estudiante.cursos', $response);
    }
    public function getCarga()
    {
        $idEstudiante = Auth::user()->id;
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();

        $periodo = Periodo::where('estado', '1')->first();

        // $docenteApto = DocenteApto::find($idDocenteApto);
        // // $carga = CargaAcademica::with('curso')->select('link')->where('docentes_id',$docenteApto->docentes_id)->get();

        $response["carga"] = CargaAcademica::with(["curso", "docente", "grupoAula"])
            ->where("grupo_aulas_id", $matricula->grupo_aulas_id)
            ->where("periodos_id", $periodo->id)
            ->where("estado", "1")
            ->orderBy("cursos_id")
            ->get();


        return response()->json($response);
    }
    public function indexCuadernillo()
    {
        return view('web.estudiante.cuadernillos');
    }
    public function getCursosEstudiante()
    {
        $idEstudiante = Auth::user()->id;
        $cuadernillos = [];
        $periodo = Periodo::where('estado', '1')->first();
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();
        $cargaAcademica = DB::table('carga_academicas as ca')
            ->select(
                'c.denominacion as curso',
                'c.id as id'
            )
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->where('ca.grupo_aulas_id', $matricula->grupo_aulas_id)
            ->get();
        foreach ($cargaAcademica as $k => $val) {
            $curriculaDetalle = CurriculaDetalle::where([['cursos_id', $val->id], ['curriculas_id', $matricula->curriculas_id]])->first();
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->curso = $val->curso;
            $obj->cuadernillos = DB::table('cuadernillos')->select('semana', 'path', 'id')->where([
                ['tipo', '2'],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->get();
            $cuadernillos[] = $obj;
        }
        $response["cuadernillos"] = $cuadernillos;

        return response()->json($response);
    }
    public function getUrlCuadernillo(Request $request)
    {
        $idEstudiante = Auth::user()->id;
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();

        $periodo = Periodo::where('estado', '1')->first();
        // $curricula = Curricula::where('areas_id',$request->area)->first();

        $curriculaDetalle = CurriculaDetalle::where([['cursos_id', $request->curso], ['curriculas_id', $matricula->curriculas_id]])->first();
        // return response()->json($curriculaDetalle);
        if (empty($curriculaDetalle)) {
            return "";
        } else {
            $cuadernillo = DB::table('cuadernillos')->select('path')->where([
                ['semana', $request->semana],
                ['tipo', '2'],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->first();

            if (empty($cuadernillo)) {
                return "";
            } else {
                return response()->json($cuadernillo);
            }
        }
    }
    public function getCriteriosDocente()
    {
        $response  = Criterio::where([["estado", "1"], ["tipo", "1"]])->get();

        return $response;
    }
    public function CalificarDocente(Request $request, $id)
    {
        $idEstudiante = Auth::user()->id;
        $rules = $request->validate([
            'preguntasValidar.*' => 'required|integer|max:10|min:1',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'preguntasValidar.*.min' => '* El valor minimo de calificación es de 1.',
            'preguntasValidar.*.max' => '* El valor maximo de calificación es de 10.',
        ]);

        $totalCriterios = count($request->preguntasValidar);
        DB::beginTransaction();
        try {
            foreach ($request->preguntas as $key => $value) {
                if (!empty($value)) {

                    $calificacionDetalle = new CalificacionDocenteDetalle;
                    $calificacionDetalle->puntaje = $value;
                    $calificacionDetalle->criterios_id = $key;
                    $calificacionDetalle->estudiantes_id = $idEstudiante;
                    $calificacionDetalle->calificacion_docentes_id = $id;
                    $calificacionDetalle->save();
                }
            }

            $totalPuntaje = CalificacionDocenteDetalle::where("calificacion_docentes_id", $id)->sum("puntaje");
            $calificacionDocente = CalificacionDocente::find($id);
            $calificacionDocente->participantes = $calificacionDocente->participantes + 1;
            $calificacionDocente->promedio = $totalPuntaje / ($calificacionDocente->participantes * $totalCriterios);
            $calificacionDocente->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
        // dd($request->all(), $id);
    }
}
