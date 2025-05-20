<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use App\Models\Inscripciones;
use App\Models\Periodo;
use App\Models\Curricula;
use App\Models\Matricula;
use App\Models\CargaAcademica;
use App\Models\Estudiante;
use App\Models\MatriculaDetalle;
use App\Services\GWorkspace;
use DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class MatriculaController extends Controller
{
    public function pendiente()
    {
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }

        $response['permisos'] = json_encode($permissions);
        return view("intranet.matricula.pendiente", $response);
    }
    public function lista(Request $request)
    {
        // dd($request);
        // $data = new \stdClass();
        // $data->where("estado","1");
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id', 'correlativo', 'estado', 'cantidad_inscrito', 'estudiantes_id', 'areas_id', 'sedes_id', 'periodos_id', 'turnos_id'], ['estudiante', 'area', 'sede', 'periodo', 'turno'], ["sede", "area", "turno"]);
        $data = $data->where("estado", "1");
        $data = $data->where("matricula", "0");
        $data = $data->orderBy("cantidad_inscrito", "desc");
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function store(Request $request)
    {
        $rules = $request->validate([
            'local' => 'required',
            'area' => 'required',
            'turno' => 'required',
            'grupo' => 'required',
            'checks' => 'array|min:1',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'checks.min' => '* Seleccionar minimo un estudiante.',
        ]);

        DB::beginTransaction();
        try {

            $periodo = Periodo::where("estado", '1')->first();
            $grupoAula = CargaAcademica::where("grupo_aulas_id", $request->grupo)->get();

            foreach ($request->checks as $key => $value) {
                $inscripcion = Inscripciones::find($value);
                $inscripcion->matricula = "1";
                $inscripcion->save();
                $curricula = Curricula::where("areas_id", $inscripcion->areas_id)->where("estado", "1")->first();

                $data = new Matricula;
                $data->estudiantes_id = $inscripcion->estudiantes_id;
                $data->curriculas_id = $curricula->id;
                $data->grupo_aulas_id = $request->grupo;
                $data->turnos_id = $request->turno;
                $data->periodos_id = $periodo->id;
                $data->save();
                //inscripcion a cursos de class room
                foreach ($grupoAula as $val) {
                    $detalle = new MatriculaDetalle;
                    $detalle->estado = "0";
                    $detalle->matriculas_id = $data->id;
                    $detalle->carga_academicas_id = $val->id;
                    $detalle->save();
                }
            }

            DB::commit();
            $response["message"] = 'Estudiantes Matriculados Correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Matricular, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function matriculado()
    {
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }

        $response['permisos'] = json_encode($permissions);
        return view("intranet.matricula.matriculado", $response);
    }
    public function lista_matriculado(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new Matricula,
            [
                'matriculas.id',
                'matriculas.grupo_aulas_id',
                'matriculas.turnos_id',
                'matriculas.periodos_id',
                'matriculas.estudiantes_id',
                "a.denominacion as area",
                "g.denominacion as grupo"
            ],
            ['estudiante', 'turno'],
            ["turno"]
        );
        $data = $data->join("grupo_aulas as gu", "gu.id", "matriculas.grupo_aulas_id")
            ->join("areas as a", "a.id", "gu.areas_id")
            ->join("grupos as g", "g.id", "gu.grupos_id");

        if (isset($request->area)) {
            $data = $data->where('a.id', $request->area);
        }
        if (isset($request->turno)) {
            $data = $data->where('matriculas.turnos_id', $request->turno);
        }
        if (isset($request->grupo)) {
            $data = $data->where('matriculas.grupo_aulas_id', $request->grupo);
        }
        if (isset($request->sede)) {
            $data = $data->join("aulas as au", "au.id", "gu.aulas_id")
                ->join("locales as l", "l.id", "au.locales_id")
                ->where("l.sedes_id", $request->sede);
        }
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function delete($id)
    {
        // dd($id);
        $apiGsuite = new GWorkspace();
        DB::beginTransaction();
        try {
            MatriculaDetalle::where("matriculas_id", $id)->delete();

            $matricula = Matricula::find($id);
            $estudiante = Estudiante::find($matricula->estudiantes_id);
            $inscripcion = Inscripciones::where("estudiantes_id", $matricula->estudiantes_id)->first();
            $inscripcion->matricula = "0";
            $inscripcion->save();

            $carga = CargaAcademica::where("grupo_aulas_id", $matricula->grupo_aulas_id)->get();

            foreach ($carga as $key => $value) {
                $datos = json_encode(array(
                    "courseId" => $value->idclassroom,
                    "userId" => $estudiante->usuario
                ));
                $api = json_decode($apiGsuite->eliminarMatriculaEstudiante($datos));
            }
            // return ($inscripcion);

            $matricula->delete();
            DB::commit();
            $response["message"] = 'Estudiantes Desmatriculado Correctamente';
            $response["status"] = true;
            $response["error"] = "";
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al desmatricular, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function vincular($id)
    {
        // dd($id);
        $apiGsuite = new GWorkspace();
        DB::beginTransaction();
        try {
            $matriculaDetalle = MatriculaDetalle::find($id);
            $estudiante = Matricula::with('estudiante')->find($matriculaDetalle->matriculas_id);
            $curso = CargaAcademica::find($matriculaDetalle->carga_academicas_id);

            try {

                $datos = json_encode(array(
                    "courseId" => $curso->idclassroom,
                    "userId" => $estudiante->estudiante->idgsuite,
                ));

                $matricular = json_decode($apiGsuite->matricularEstudiante($datos));
                if ($matricular->success) {
                    $response["status"] = true;
                    $response["message"] =  'Acción realizada correctamente';


                    $matricula = MatriculaDetalle::find($id);
                    $matricula->estado = '1';
                    $matricula->save();
                } else {
                    $response["status"] = false;
                    $response["message"] =  'Error al vincular curso.';
                }
            } catch (\Exception $e) {
                $matricula = MatriculaDetalle::find($id);
                $matricula->estado = '2';
                $matricula->save();

                $response["message"] =  'Error al vincular curso.';
                $response["status"] =  false;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al ejecutar la accion, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function bloquearPanel($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $estudiante->panel = '0';
            $estudiante->save();

            $response["message"] =  'Actulizado, panel bloqueado.';
            $response["status"] =  true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al bloquear panel, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function desbloquearPanel($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $estudiante->panel = '1';
            $estudiante->save();

            $response["message"] =  'Actulizado, panel debloqueado.';
            $response["status"] =  true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al desbloquear panel, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function habilitar()
    {
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }

        $response['permisos'] = json_encode($permissions);
        return view("intranet.matricula.habilitar", $response);
    }
    public function lista_habilitar(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new Matricula,
            [
                'matriculas.id',
                'matriculas.grupo_aulas_id',
                'matriculas.turnos_id',
                'matriculas.periodos_id',
                'matriculas.estudiantes_id',
                "a.denominacion as area",
                "g.denominacion as grupo"
            ],
            ['estudiante', 'turno'],
            ["turno"]
        );
        $data = $data->join("grupo_aulas as gu", "gu.id", "matriculas.grupo_aulas_id")
            ->join("areas as a", "a.id", "gu.areas_id")
            ->join("grupos as g", "g.id", "gu.grupos_id");

        if (isset($request->area)) {
            $data = $data->where('a.id', $request->area);
        }
        if (isset($request->turno)) {
            $data = $data->where('matriculas.turnos_id', $request->turno);
        }
        if (isset($request->grupo)) {
            $data = $data->where('matriculas.grupo_aulas_id', $request->grupo);
        }
        if (isset($request->sede)) {
            $data = $data->join("aulas as au", "au.id", "gu.aulas_id")
                ->join("locales as l", "l.id", "au.locales_id")
                ->where("l.sedes_id", $request->sede);
        }
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function habilitarEdicion($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $estudiante->edit = '0';
            $estudiante->save();

            $response["message"] =  'Actulizado, edicion habilitado.';
            $response["status"] =  true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al habilitar edicion, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function deshabilitarEdicion($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $estudiante->edit = '1';
            $estudiante->save();

            $response["message"] =  'Actulizado, edicion deshabilitado.';
            $response["status"] =  true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al deshabilitar edicion, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
}
