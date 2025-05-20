<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CargaAcademica;
use App\Models\DocenteApto;
use App\Models\Horario;
use App\Services\GWorkspace;
use App\Models\Periodo;
use DB;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.horario");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $apiGsuite = new GWorkspace();
        $rules = $request->validate([
            'grupo_aula' => 'required',
            'docente' => 'required',
            'carga' => 'required',
            'horario' => 'array|min:1',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'horario.min' => '* Seleccionar minimo un Horario.',

        ]);
        // // dd($request);
        $periodo = Periodo::where('estado','1')->first();
        $docenteApto = DocenteApto::where([['docentes_id',$request->docente],['periodos_id',$periodo->id]])->first();
        DB::beginTransaction();
        try {

            $carga = CargaAcademica::find($request->carga);
            $carga->docentes_id = $request->docente;
            $carga->estado = '1';
            $carga->save();
            // Asignar docente a curso en classroom
            // ***************
            foreach ($request->horario as $key => $value) {
                $ids = explode("-",$value);
                $horario = new Horario;
                $horario->carga_academicas_id = $request->carga;
                $horario->plantilla_horarios_id = $ids[1];
                $horario->dia = $ids[0];
                $horario->save();
            }

            $datos = json_encode(array(
                "courseId" => $carga->idclassroom,
                "userId" => $docenteApto->idgsuite,
            ));
            $apiGsuite->matricularDocente($datos);

            DB::commit();
            $response["message"] = 'Horario creado y Docente Asignadocorrectamente';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function desmastricular(Request $request){
        // $apiGsuite = new GWorkspace();
        $rules = $request->validate([
            'docente' => 'required',
            'carga' => 'required',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',

        ]);
        // // dd($request);
        DB::beginTransaction();
        try {
            $carga = CargaAcademica::find($request->carga);

            $docente = DocenteApto::where("docentes_id",$carga->docentes_id)->first();
            // dd($docente);

            $carga->docentes_id = NULL;
            $carga->link = NULL;
            $carga->save();

            $horario = Horario::where("carga_academicas_id",$request->carga)->get();
            // Asignar docente a curso en classroom
            // ***************
            foreach ($horario as $key => $value) {
                $horarios = Horario::find($value->id);
                $horarios->delete();
            }

            $apiGsuite = new GWorkspace();
            $datos = json_encode(array(
                "courseId" => $carga->idclassroom,
                "userId" => $docente->usuario,
            ));
            $eliminar = json_decode($apiGsuite->eliminarMatriculaDocente($datos));
            // dd($correoGenerado);
            $response["message"] = 'El docente fue desmatriculado correctamente del curso y Classroom. Horarios eliminados';
            if($eliminar->success==false){
                $response["message"] = 'El docente fue desmatriculado correctamente del curso, No se encontre registro en Classroom. Horarios eliminados';
            }

            DB::commit();
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function vincular(Request $request){
        // dd($request);
        $apiGsuite = new GWorkspace();
        DB::beginTransaction();
        try {
            $periodo = Periodo::where('estado','1')->first();
            $carga = CargaAcademica::find($request->carga);
            $docenteApto = DocenteApto::where([['docentes_id',$request->docente],['periodos_id',$periodo->id]])->first();
            $datos = json_encode(array(
                "courseId" => $carga->idclassroom,
                "userId" => $docenteApto->idgsuite,
            ));
            $vincular = json_decode($apiGsuite->matricularDocente($datos));
            $response["message"] = 'Docente vinculado correctamente';
            $response["status"] =  true;
            if($vincular->success==false){
                $response["message"] = 'El docente no fue vinculado.';
                $response["status"] =  false;
            }
            // $response["message"] =  'Docente vinculado correctamente.';
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
}
