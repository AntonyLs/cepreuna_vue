<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Models\Curricula;
use Illuminate\Http\Request;
use App\Models\CurriculaDetalle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\VueTables\EloquentVueTables;

class CurriculaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.curricula");
    }
    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new Curricula,['id','resolucion','estado','tipo','areas_id'],['area']);
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function detalle(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new CurriculaDetalle,['id','horas','horario_inscripcion','cursos_id','curriculas_id'],['curso','curricula']);
        if (isset($request->curricula)) {
            $data = $data->where('curricula_detalles.curriculas_id', $request->curricula);
        }
            $response = $table->finish($data);
        return response()->json($response);
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
        $rules = $request->validate([
            'resolucion' => 'required',
            'estado' => 'required',
            'tipo' => 'required',
            'area' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new Curricula;
            $data->resolucion = $request->resolucion;
            $data->estado = $request->estado;
            $data->tipo = $request->tipo;
            $data->areas_id = $request->area;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
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
        $response = Curricula::find($id);
        return $response;
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
        $rules = $request->validate([
            'resolucion' => 'required',
            'estado' => 'required',
            'tipo' => 'required',
            'area' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = Curricula::find($id);
            $data->resolucion = $request->resolucion;
            $data->estado = $request->estado;
            $data->tipo = $request->tipo;
            $data->areas_id = $request->area;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro actualizado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actualizar registro, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
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

    public function storeD(Request $request, $id){
        $rules = $request->validate([
            'horas' => 'required',
            'horario_inscripcion' => 'required',
            'curso' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new CurriculaDetalle;
            $data->horas = $request->horas;
            $data->horario_inscripcion = $request->horario_inscripcion;
            $data->cursos_id = $request->curso;
            $data->curriculas_id = $id;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }

    public function updateD(Request $request, $id){
        $rules = $request->validate([
            'horas' => 'required',
            'horario_inscripcion' => 'required',
            'curso' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = CurriculaDetalle::find($id);
            $data->horas = $request->horas;
            $data->horario_inscripcion = $request->horario_inscripcion;
            $data->cursos_id = $request->curso;
            //$data->curriculas_id = $id;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }

    public function destroyD($id){
        DB::beginTransaction();
        try {

            $data = CurriculaDetalle::find($id);
            $data->delete();

            DB::commit();
            $response["message"] = 'Registro eliminado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al eliminar registro, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }
}
