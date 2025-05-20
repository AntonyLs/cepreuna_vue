<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Matricula;
use App\Models\PlantillaHorario;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class HorarioController extends Controller
{
    public function getHorario(Request $request){
        // dd(Auth::user()->docentes_id);
        $token = explode("-",Crypt::decryptString($request->_token));
        $matricula = Matricula::select("matriculas.*","g.denominacion as grupo","a.denominacion as area")
                    ->join("grupo_aulas as ga","ga.id","matriculas.grupo_aulas_id")
                    ->join("grupos as g","g.id","ga.grupos_id")
                    ->join("areas as a","a.id","ga.areas_id")
                    ->where("estudiantes_id",$token[0])
                    ->first();
        if($matricula){
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
            $response["status"] = true;

        }else{
            $response["status"] = false;
            $response["grupo"] = [];
            $response["area"] = [];
            $response["horario"] = [];
        }
        return response()->json($response);
    }
}
