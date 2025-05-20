<?php

namespace App\Http\Controllers\Web\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\CargaAcademica;
use App\Models\Estudiante;
use App\Services\GWorkspace;
use DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:estudiante');
    }

    public function index()
    {
        $estudiante = Auth::user()->id;
        $response["matricula"] = Matricula::where("estudiantes_id", $estudiante)
            ->first();
        $response["estado"] = false;
        $response['estudiante'] = Estudiante::with('tipoDocumento', 'colegio', 'ubigeo')
            ->select('nombres', 'paterno', 'materno', 'foto', 'edit', DB::raw("DATE_FORMAT(fecha_nac,'%d/%m/%Y') as fecha_nac"), 'anio_egreso', 'nro_documento', 'ubigeos_id')
            ->where('id', $estudiante)->first();
        // dd($response);

        return view('web.estudiante.dashboard', $response);
    }
    public function getCargaAcademica()
    {
        $estudiante = Auth::user()->id;
        $idgsuite = Auth::user()->idgsuite;
        $matricula = Matricula::where("estudiantes_id", $estudiante)->first();
        $periodo = Periodo::where("estado", "1")->first();
        $response["carga"] = CargaAcademica::with(["curso"])
            ->select("carga_academicas.*", DB::raw('"" as status'), DB::raw('"" as loading'))
            ->where("grupo_aulas_id", $matricula->grupo_aulas_id)
            ->where("periodos_id", $periodo->id)->get();

        return $response;
    }
    public function checkMatricula($id)
    {
        $apiGsuite = new GWorkspace();
        $correo = Auth::user()->usuario;
        // $correo = "75259884@cepreuna.edu.pe";
        // $id = "256869880327";

        $datos = json_encode(array(
            "courseId" => $id,
            "userId" => $correo,
        ));

        $result = json_decode($apiGsuite->checkMatricula($datos));
        // $status
        // dd($result);
        if ($result->success) {
            $response["status"]  = true;
        } else {
            $response["status"]  = false;
        }
        // echo "1";
        // $response["status"] = $status;
        return $response;
        // dd($response);
    }
    public function matricular($id)
    {
        // set_time_limit(0);

        $apiGsuite = new GWorkspace();
        $idgsuite = Auth::user()->idgsuite;
        // sleep(3);
        try {

            $datos = json_encode(array(
                "courseId" => $id,
                "userId" => $idgsuite,
            ));

            $matricular = json_decode($apiGsuite->matricularEstudiante($datos));
            if ($matricular->success) {
                $status = true;
                $response["message"] =  'Curso matriculado en ClassRoom.';
            } else {
                $status = false;
                $response["message"] =  'Error al matricular cursos.';
            }

            $response["status"] = $status;
            // $response["message"] =  'Error al matricular cursos.';
            // $response["message"] = 'Curso matriculado en ClassRoom.';

        } catch (\Exception $e) {
            $response["message"] =  'Error al matricular cursos.';
            $response["status"] =  false;
        }

        return $response;
        // return 2;
    }
    public function getLocalEstudiante()
    {
        $estudiante = Auth::user()->id;
        $data = DB::table('estudiantes as e')
            ->select(
                DB::raw("CONCAT(e.paterno,' ',e.materno,' ',e.nombres) as nombres"),
                "s.denominacion as sede",
                "l.denominacion as local",
                "l.direccion",
                "l.foto",
                "a.codigo as aula",
                "g.denominacion as grupo",
                "t.denominacion as turno",
                "ar.denominacion as area"
            )
            ->join("matriculas as m", "m.estudiantes_id", "e.id")
            ->join("grupo_aulas as ga", "ga.id", "m.grupo_aulas_id")
            ->join("grupos as g", "g.id", "ga.grupos_id")
            ->join("areas as ar", "ar.id", "ga.areas_id")
            ->join("turnos as t", "t.id", "ga.turnos_id")
            ->join("aulas as a", "a.id", "ga.aulas_id")
            ->join("locales as l", "l.id", "a.locales_id")
            ->join("sedes as s", "s.id", "l.sedes_id")
            ->where("e.id", $estudiante)
            ->first();

        if (isset($data)) {
            $response["status"] = true;
            $response["result"] = $data;
        } else {
            $response["status"] = false;
            $response["result"] = "";
        }

        return $response;
    }
}
