<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.tarifas");
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Tarifa, ['id', 'denominacion', 'importe', 'estado', 'tipo_estudiante', 'tipo_colegios_id', 'concepto_pagos_id'], ['tipoColegio', 'conceptoPago']);
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
        //
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
        $response = Tarifa::with("tipoColegio", "conceptoPago")->find($id);
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
            'denominacion' => 'required',
            'estado' => 'required',
            'importe' => 'required',
            'tipo_estudiante' => 'required',
            'tipo_colegio' => 'required',
            'concepto' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = Tarifa::find($id);
            $data->denominacion = $request->denominacion;
            $data->importe = $request->importe;
            $data->estado = (string)$request->estado;
            $data->tipo_estudiante = (string)$request->tipo_estudiante;
            $data->tipo_colegios_id = $request->tipo_colegio;
            $data->concepto_pagos_id = $request->concepto;
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
