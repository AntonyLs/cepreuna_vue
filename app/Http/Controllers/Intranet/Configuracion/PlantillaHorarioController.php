<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use Illuminate\Http\Request;
use App\Models\PlantillaHorario;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\VueTables\EloquentVueTables;

class PlantillaHorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.plantilla_horario");
    }
    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new PlantillaHorario,['id','hora_inicio','hora_fin','tipo','estado','turnos_id'],['turno']);
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
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'estado' => 'required',
            'turno' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new PlantillaHorario;
            $data->hora_inicio = $request->hora_inicio;
            $data->hora_fin = $request->hora_fin;
            $data->estado = $request->estado;
            $data->turnos_id = $request->turno;
            $data->tipo = "1";
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
        $response = PlantillaHorario::find($id);
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
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'estado' => 'required',
            'turno' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = PlantillaHorario::find($id);
            $data->hora_inicio = $request->hora_inicio;
            $data->hora_fin = $request->hora_fin;
            $data->estado = $request->estado;
            $data->turnos_id = $request->turno;
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
}
