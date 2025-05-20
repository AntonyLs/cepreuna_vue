<?php

namespace App\Http\Controllers\Intranet\CoordinadorAuxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\CargaAcademica;
use App\Models\Horario;
use App\Models\DocenteApto;
use App\Services\GWorkspace;

class HorarioController extends Controller
{
    public function index(){
        $userId = auth()->id();
        return view("intranet.coordinadorAuxiliar.docente.horario",compact('userId'));
    }
    public function store(Request $request){
        $rules = $request->validate([
            'docente' => 'required',
        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',

        ]);
        // dd($request);
        // $id = Auth::user()->id;
        $apiGsuite = new GWorkspace();
        $carga = CargaAcademica::find($request->carga);
        $horario = Horario::where("carga_academicas_id",$request->carga)->get();
        $docenteApto = DocenteApto::where('docentes_id',$request->docente)->first();
        // dd($docenteApto);
        DB::beginTransaction();
        try {

            $data = new CargaAcademica;
            $data->idclassroom = $carga->idclassroom;
            $data->tipo = '2';
            $data->docentes_id = $request->docente;
            $data->cursos_id = $carga->cursos_id;
            $data->grupo_aulas_id = $carga->grupo_aulas_id;
            $data->periodos_id = $carga->periodos_id;
            // $data->estado = $carga->periodos_id;
            $data->save();

            foreach ($horario as $key => $value) {
                // dd($value);
                $dataHorario = new Horario;
                $dataHorario->carga_academicas_id = $data->id;
                $dataHorario->plantilla_horarios_id = $value->plantilla_horarios_id;
                $dataHorario->dia = $value->dia;
                $dataHorario->save();
            }
            ///*******Agregar Class Room****** */

            $datos = json_encode(array(
                "courseId" => $carga->idclassroom,
                "userId" => $docenteApto->idgsuite,
            ));
            $apiGsuite->matricularDocente($datos);

            DB::commit();
            $response["message"] = 'Docente suplente asignado.';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al asignar, intentelo nuevamante.';
            $response["status"] =  false;
        }

         
        return $response;
    }
    public function deshabilitar($id){
        // return $id;
        DB::beginTransaction();
        try {
            
            $data = CargaAcademica::find($id);
            $data->estado = '0';
            $data->save();
            
            if($data->tipo=="2"){
                ///*******Quitar de google class Room****** */
                $docenteApto = DocenteApto::where('docentes_id',$data->docentes_id)->first();
                // dd($docenteApto->usuario);
                $apiGsuite = new GWorkspace();
                $datos = json_encode(array(
                    "courseId" => $data->idclassroom,
                    "userId" => $docenteApto->usuario,
                ));
                $eliminar = json_decode($apiGsuite->eliminarMatriculaDocente($datos));
                // dd($correoGenerado);
                // $response["message"] = 'El docente fue desmatriculado correctamente del curso y Classroom. Horarios eliminados';
                // if($eliminar->success==false){
                //     $response["message"] = 'El docente fue desmatriculado correctamente del curso, No se encontre registro en Classroom. Horarios eliminados';
                // }
            }
            
            DB::commit();
            $response["message"] = 'Docente deshabilitado.';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al deshabilitar, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }
    public function habilitar($id){
        // return $id;
        DB::beginTransaction();
        try {
            
            $data = CargaAcademica::find($id);
            $data->estado = '1';
            $data->save();
            
            if($data->tipo=="2"){
                ///*******Habilitar de google class Room****** */
                $docenteApto = DocenteApto::where('docentes_id',$data->docentes_id)->first();
                // dd($data->idclassroom);
                $apiGsuite = new GWorkspace();
                $datos = json_encode(array(
                    "courseId" => $data->idclassroom,
                    "userId" => $docenteApto->idgsuite,
                ));
                $apiGsuite->matricularDocente($datos);
            }

            DB::commit();
            $response["message"] = 'Docente habilitado.';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al habilitar, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }
}
