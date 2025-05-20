<?php

namespace App\Http\Controllers\Intranet\administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\InscripcionCurso;

class PropuestaHorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.propuesta-horario");
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
            'docente' => 'required',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $curso = InscripcionCurso::where("curricula_detalles_id",$id)->where("inscripcion_docentes_id",$request->docente)->increment("prioridad");
            // $curso->horario_inscripcion = '0';
            // $curso->save();
            // Asignar docente a curso en classroom
            // ***************
            //  HorarioInscripciones::where("areas_id",$request->area)->where("curricula_detalles_id",$id)->delete();


            DB::commit();
            $response["message"] = 'Datos Actualizados';
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
