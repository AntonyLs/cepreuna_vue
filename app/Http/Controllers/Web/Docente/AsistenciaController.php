<?php

namespace App\Http\Controllers\Web\Docente;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaDocente;
use App\Models\CargaAcademica;
use App\Models\DocenteApto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    public function index(){
        return view("web.docente.asistencia");
    }
    public function getAsistencia(){
        $idDocenteApto = Auth::user()->id;
        $docenteApto = DocenteApto::find($idDocenteApto);
        $asistenciasDocente = AsistenciaDocente::select('asistencia_docentes.*',DB::raw('DATE_FORMAT(fecha,"%d-%m-%Y") as fecha_asistencia'))->where('docentes_id',$docenteApto->docentes_id)->get();


        foreach ($asistenciasDocente as $k => $val) {
            $cargaAcademica = CargaAcademica::with('curso')->find($val->carga_academicas_id);
            $grupo = DB::table('grupo_aulas as ga')
                        ->select(
                            'g.denominacion'
                        )
                        ->join('grupos as g','g.id','ga.grupos_id')
                        ->where('ga.id',$cargaAcademica->grupo_aulas_id)
                        ->first();

            $obj = new \stdClass;
            $obj->start = $val->fecha.' '.$val->hora_inicio;
            $obj->end = $val->fecha.' '.$val->hora_fin;
            $obj->title = $cargaAcademica->curso->denominacion. " (". $grupo->denominacion .")";
            $obj->class = $val->estado == '1' ? 'asis bg-success' : ($val->estado == '2' ? 'asis bg-warning' : 'asis bg-danger');
            $obj->obs = $val->observacion;
            $obj->estado = $val->estado;
            $obj->tipo = $cargaAcademica->tipo;
            $obj->fecha_asistencia = $val->fecha_asistencia;
            $obj->hora_inicio = $val->hora_inicio;
            $obj->hora_fin = $val->hora_fin;

            $asistencias[] = $obj;
        }
        $response["asistencias"] = $asistencias;

        return response()->json($response);

    }
}
