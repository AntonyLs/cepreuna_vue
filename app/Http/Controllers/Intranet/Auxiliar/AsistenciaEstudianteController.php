<?php

namespace App\Http\Controllers\Intranet\Auxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use DB;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaEstudianteDetalle;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class AsistenciaEstudianteController extends Controller
{
    public function index()
    {
        $response["fecha"] = date("Y-m-d");
        $response["fecha"] = date("Y-m-d");
        // $response["fecha"] = "2021-01-25";
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
        // $response["fecha"] = "2021-01-25";
        return view("intranet.auxiliar.asistencia.estudiante", $response);
    }
    public function store(Request $request)
    {
        // dd($request);
        $id = Auth::user()->id;
        // $fecha = new \DateTime($request->fecha);
        // $semana = $fecha->format("N");
        // $horario = Horario::select("horarios.*","ph.hora_inicio","ph.hora_fin")
        //                     ->where([
        //                         ["carga_academicas_id",$request->carga],
        //                         ["dia",$semana]
        //                     ])
        //                     ->join("plantilla_horarios as ph","ph.id","horarios.plantilla_horarios_id")
        //                     ->orderBy("hora_inicio","asc")
        //                     ->get();
        // dd(reset($horario));
        // var_dump($horario);
        // $cantidadHoras = count($horario);
        DB::beginTransaction();
        try {

            $data = new AsistenciaEstudiante;
            $data->fecha = $request->fecha;
            $data->grupo_aulas_id = $request->grupo;
            $data->observacion = $request->observacion;
            $data->estado = "0";
            $data->users_id = $id;
            $data->save();

            foreach ($request->estados as $key => $value) {
                $ids = explode("-", $value);
                // dd($ids);
                $detalle = new AsistenciaEstudianteDetalle;
                $detalle->estado = $ids[1];
                $detalle->estudiantes_id = $ids[0];
                $detalle->asistencia_estudiantes_id = $data->id;
                $detalle->save();
            }


            DB::commit();
            $response["message"] = 'Asistencia Registrada';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al validar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
    }
}
