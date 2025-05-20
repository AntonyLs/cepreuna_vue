<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionVacante;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VacanteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.vacantes");
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new ConfiguracionVacante(), ['*'], ['turno', 'area', 'sede']);
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
            'sede' => 'required',
            'area' => 'required',
            'turno' => 'required',
            'cantidad' => 'required',
            'estado' => 'required',

        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $data = new ConfiguracionVacante();
            $data->sedes_id = $request->sede;
            $data->areas_id = $request->area;
            $data->turnos_id = $request->turno;
            $data->cantidad = $request->cantidad;
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
        $response = ConfiguracionVacante::with('sede', 'area', 'turno')->find($id);

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
            'sede' => 'required',
            'area' => 'required',
            'turno' => 'required',
            'cantidad' => 'required',
            'estado' => 'required',

        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $data = ConfiguracionVacante::find($id);
            $data->sedes_id = $request->sede;
            $data->areas_id = $request->area;
            $data->turnos_id = $request->turno;
            $data->cantidad = $request->cantidad;
            $data->estado = strval($request->estado);
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
