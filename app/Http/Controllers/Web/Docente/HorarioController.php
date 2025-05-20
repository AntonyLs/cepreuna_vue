<?php

namespace App\Http\Controllers\Web\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\PlantillaHorario;
use App\Models\Horario;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;

class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    public function index(){
        return view("web.docente.horario");
    }
    public function getHorario(){
        // dd(Auth::user()->docentes_id);
        $docente = Auth::user()->docentes_id;
        $turno = Turno::get();
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
                                    ->select("horarios.*","g.denominacion as grupo")
                                    ->whereHas('carga', function (Builder $query) use ($docente) {
                                        $query->where('docentes_id', $docente);
                                    })
                                    ->join("carga_academicas as ca","ca.id","horarios.carga_academicas_id")
                                    ->join("grupo_aulas as ga","ga.id","ca.grupo_aulas_id")
                                    ->join("grupos as g","g.id","ga.grupos_id")
                                    // ->where("carga_academicas_id",$inscripcionDocente->id)
                                    ->where("plantilla_horarios_id",$val->id)
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
