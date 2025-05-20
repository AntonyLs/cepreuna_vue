<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HorarioInscripciones;
use App\Models\CurriculaDetalle;
use DB;

class HorarioInscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.horario-inscripcion");
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
            'area' => 'required',
            'curso' => 'required',
            'horario' => 'array|min:1',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'horario.min' => '* Seleccionar minimo un Horario.',
        ]);
        DB::beginTransaction();
        try {

            $curso = CurriculaDetalle::find($request->curso);
            $curso->horario_inscripcion = '1';
            $curso->save();
            // Asignar docente a curso en classroom
            // ***************
            foreach ($request->horario as $key => $value) {
                $ids = explode("-",$value);
                $horario = new HorarioInscripciones;
                $horario->curricula_detalles_id = $request->curso;
                $horario->plantilla_horarios_id = $ids[1];
                $horario->dia = $ids[0];
                $horario->areas_id = $request->area;
                $horario->save();
            }


            DB::commit();
            $response["message"] = 'Horario creado';
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
        // dd($request,$id);
        $rules = $request->validate([
            'area' => 'required',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $curso = CurriculaDetalle::find($id);
            $curso->horario_inscripcion = '0';
            $curso->save();
            // Asignar docente a curso en classroom
            // ***************
             HorarioInscripciones::where("areas_id",$request->area)->where("curricula_detalles_id",$id)->delete();


            DB::commit();
            $response["message"] = 'Horario eliminado';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
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
