<?php

namespace App\Http\Controllers\Web\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Matricula;
use App\Models\Turno;
use App\Models\PlantillaHorario;
use App\Models\Horario;
use DB;


class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:estudiante');
    }
    public function index(){
        // dd(Auth::user()->nombres);
        return view("web.estudiante.horario");
    }
    public function getHorario(){
        // dd(Auth::user()->docentes_id);
        $estudiante = Auth::user()->id;
        $matricula = Matricula::select("matriculas.*","g.denominacion as grupo","a.denominacion as area")
                    ->join("grupo_aulas as ga","ga.id","matriculas.grupo_aulas_id")
                    ->join("grupos as g","g.id","ga.grupos_id")
                    ->join("areas as a","a.id","ga.areas_id")
                    ->where("estudiantes_id",$estudiante)
                    ->first();
        $response["grupo"] = $matricula->grupo;
        $response["area"] = $matricula->area;
        $turno = Turno::where("id",$matricula->turnos_id)->get();
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
                ->where("estado","1")
                ->where("turnos_id",$value->id)
                ->get();
            foreach ($plantilla as $k => $val) {
                $obj = new \stdClass;
                $obj->hora_inicio = $val->horaInicio;
                $obj->hora_fin = $val->horaFin;
                $obj->tipo = $val->tipo;
                $obj->horario = Horario::with(['curso','carga'])
                                    ->select(
                                        "horarios.*",
                                        DB::raw("CONCAT(d.nombres,' ',d.paterno) as docente")
                                        )
                                    // ->whereHas('carga', function (Builder $query) use ($estudiante) {
                                    //     $query->where('docentes_id', $estudiante);
                                    // })
                                    ->join("carga_academicas as ca","ca.id","horarios.carga_academicas_id")
                                    // ->join("grupo_aulas as ga","ga.id","ca.grupo_aulas_id")
                                    ->join("docentes as d","d.id","ca.docentes_id")
                                    ->where("plantilla_horarios_id",$val->id)
                                    ->where("ca.grupo_aulas_id",$matricula->grupo_aulas_id)
                                    ->where("ca.estado","1")
                                    ->orderBy("dia","asc")
                                    ->get();
                $plantillaHorario[] = $obj;
            }
            $objeto->disponibilidad = $plantillaHorario;
            $horario[] =  $objeto;
        }
        $response["horario"] = $horario;
        return response()->json($response);
    }
}
