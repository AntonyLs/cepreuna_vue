<?php

namespace App\Http\Controllers\Intranet\CoordinadorAuxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Models\AsistenciaEstudiante;
use App\Models\AsistenciaEstudianteDetalle;

class AsistenciaEstudianteController extends Controller
{
    public function index(){
        $response["fecha"] = date("Y-m-d");
        // $response["fecha"] = "2021-01-25";
        return view("intranet.coordinadorAuxiliar.asistencia.estudiante",$response);
    }
    public function store(Request $request){
        $id = Auth::user()->id;
        DB::beginTransaction();
        try {

            $data = new AsistenciaEstudiante;
            $data->fecha = $request->fecha;
            $data->grupo_aulas_id = $request->grupo;
            $data->observacion = $request->observacion;
            $data->users_id = $id;
            $data->estado = "0";
            $data->save();

            foreach ($request->estados as $key => $value) {
                $ids = explode("-",$value);
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
    public function update($id,Request $request){
        // dd($request);
        $id_users = Auth::user()->id;
        DB::beginTransaction();
        try {

            $data = AsistenciaEstudiante::find($id);
            $data->observacion = $request->observacion;
            $data->users_id = $id_users;
            $data->save();

            foreach ($request->estados as $key => $value) {
                $ids = explode("-",$value);
                // dd($ids);
                $detalle = AsistenciaEstudianteDetalle::find($ids[0]);
                $detalle->estado = $ids[1];
                // $detalle->estudiantes_id = $ids[0];
                $detalle->asistencia_estudiantes_id = $data->id;
                $detalle->save();
            }


            DB::commit();
            $response["message"] = 'Asistencia Editada';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Editar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
    }
}
