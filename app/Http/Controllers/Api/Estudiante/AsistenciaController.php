<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaEstudianteDetalle;
use App\Models\Matricula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function getAsistencia(Request $request)
    {
        $token = explode("-", Crypt::decryptString($request->_token));

        $idEstudiante = $token[0];
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();
        $horasAsistencia = DB::table('carga_academicas as ca')
            ->select(
                DB::raw('MAX(ph.hora_fin) as fin'),
                DB::raw('MIN(ph.hora_inicio) as inicio')
            )
            ->join('horarios as h', 'h.carga_academicas_id', 'ca.id')
            ->join('plantilla_horarios as ph', 'ph.id', 'h.plantilla_horarios_id')
            ->where('ca.grupo_aulas_id', $matricula->grupo_aulas_id)
            ->first();

        $asistenciasEstudianteD = AsistenciaEstudianteDetalle::where('estudiantes_id', $idEstudiante)->get();


        foreach ($asistenciasEstudianteD as $k => $val) {
            $asistenciasEstudiante = AsistenciaEstudiante::find($val->asistencia_estudiantes_id);
            $obj = new \stdClass;
            $obj->start = $asistenciasEstudiante->fecha . ' ' . $horasAsistencia->inicio;
            $obj->end = $asistenciasEstudiante->fecha . ' ' . $horasAsistencia->fin;
            $obj->title = 'Asistencia';
            $obj->class = $val->estado == '1' ? 'asis bg-success' : ($val->estado == '2' ? 'asis bg-warning' : 'asis bg-danger');

            $asistencias[] = $obj;
        }
        $response["asistencias"] = isset($asistencias) ? $asistencias : [];

        return response()->json($response);
    }
}
