<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Periodo;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.periodo");
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Periodo(), ['*']);
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
            'inicio_ciclo' => 'required',
            'fin_ciclo' => 'required',
            'estado' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $data = new Periodo();
            $data->inicio_ciclo = $request->inicio_ciclo;
            $data->fin_ciclo = $request->fin_ciclo;
            $data->estado = $request->estado;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado con éxito.';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] = "Error al guardar registro, intentelo nuevamente.";
            $response["status"] = false;
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
        $response = Periodo::find($id);

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
            'inicio_ciclo' => 'required',
            'fin_ciclo' => 'required',
            'estado' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $data = Periodo::find($id);
            $data->inicio_ciclo = $request->inicio_ciclo;
            $data->fin_ciclo = $request->fin_ciclo;
            $data->estado = $request->estado;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro actualizado con éxito.';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] = "Error al actualizar registro, intentelo nuevamente.";
            $response["status"] = false;
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
