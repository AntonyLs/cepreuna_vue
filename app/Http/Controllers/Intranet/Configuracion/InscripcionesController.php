<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionInscripciones;
use App\Models\Periodo;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InscripcionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.inscripciones");
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new ConfiguracionInscripciones(), ['id', 'inicio', 'fin', 'tipo_inscripcion', 'estado', 'tipo_usuario', 'observacion', 'periodos_id'], ['periodo']);
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
        // dd($request);
        $rules = $request->validate([
            'inicio' => 'required',
            'fin' => 'required',
            'observacion' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new ConfiguracionInscripciones;
            $data->inicio = $request->inicio;
            $data->fin = $request->fin;
            $data->tipo_inscripcion = (string)$request->tipo_inscripcion;
            $data->tipo_usuario = (string)$request->tipo_usuario;
            $data->estado = (string)$request->estado;
            $data->observacion = $request->observacion;
            $data->users_id = Auth::user()->id;
            $data->periodos_id = Periodo::select('id')->where('estado', '1')->first()->id;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar, intentelo nuevamante.';
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
        $response = ConfiguracionInscripciones::find($id);

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
            'inicio' => 'required',
            'fin' => 'required',
            'observacion' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = ConfiguracionInscripciones::find($id);
            $data->inicio = $request->inicio;
            $data->fin = $request->fin;
            $data->tipo_inscripcion = (string)$request->tipo_inscripcion;
            $data->tipo_usuario = (string)$request->tipo_usuario;
            $data->estado = (string)$request->estado;
            $data->observacion = $request->observacion;
            $data->users_id = Auth::user()->id;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro actualizado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actualizar, intentelo nuevamante.';
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
