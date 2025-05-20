<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CursosController extends Controller
{
    public function getCarga(Request $request)
    {
        $token = explode("-",Crypt::decryptString($request->_token));

        $idEstudiante = $token[0];
        $matricula = Matricula::where('estudiantes_id', $idEstudiante)->first();

        if($matricula){
            $periodo = Periodo::where('estado', '1')->first();

            // $docenteApto = DocenteApto::find($idDocenteApto);
            // // $carga = CargaAcademica::with('curso')->select('link')->where('docentes_id',$docenteApto->docentes_id)->get();

            $datos = CargaAcademica::with(["curso", "docente", "grupoAula"])
                ->where("grupo_aulas_id", $matricula->grupo_aulas_id)
                ->where("periodos_id", $periodo->id)
                ->where("estado", "1")
                ->orderBy("cursos_id")
                ->get();

            $response["status"] = true;
            $response["datos"] = $datos;
        }
        else{
            $response["status"] = false;
            $response["datos"] = [];
        }



        return response()->json($response);
    }
}
