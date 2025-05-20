<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\CronogramaPago;
use App\Models\Periodo;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronogramaPagosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.cronograma-pagos");
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new CronogramaPago, ['id', 'inicio', 'fin', 'nro_cuota', 'estado', 'observacion'], []);
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
            'inicio' => 'required',
            'fin' => 'required',
            'nro_cuota' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'nro_cuota.required' => '* El campo número cuota es obligatorio.',
        ]);

        $periodoActual = Periodo::where('estado', '1')->first();

        DB::beginTransaction();
        try {

            $data = new CronogramaPago;
            $data->inicio = $request->inicio;
            $data->fin = $request->fin;
            $data->estado = (string)$request->estado;
            $data->nro_cuota = $request->nro_cuota;
            $data->observacion = $request->observacion;
            $data->periodos_id = $periodoActual->id;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
            $response["status"] =  false;
            $response["error"] = $e;
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
        $response = CronogramaPago::find($id);
        // $response["provincia"]
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
            'nro_cuota' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'nro_cuota.required' => '* El campo número cuota es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = CronogramaPago::find($id);
            $data->inicio = $request->inicio;
            $data->fin = $request->fin;
            $data->estado = (string)$request->estado;
            $data->nro_cuota = $request->nro_cuota;
            $data->observacion = $request->observacion;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar registro, intentelo nuevamante.';
            $response["status"] =  false;
            $response["error"] = $e;
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
