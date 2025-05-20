<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\DB;
use App\Models\Inscripciones;
use App\Models\TarifaEstudiante;

class TarifarioEstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.tarifario-estudiante");
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id', 'tipo_estudiante', 'modalidad', 'estudiantes_id'], ['estudiante']);
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
        $response["cuotas"] = ["Matrícula", "Cuota N° 1", "Cuota N° 2", "Cuota N° 3", "Cuota N° 4"];
        $tarifarios = TarifaEstudiante::where("estudiantes_id", $id)->orderBy("nro_cuota", "asc")->get();
        $monto = [];
        $pagado = [];
        $mora = [];
        $nro_cuota = [];
        $modalidad = [];
        $tipo_estudiante = [];
        $id = [];
        foreach ($tarifarios as $key => $value) {
            $monto[] = $value->monto;
            $pagado[] = $value->pagado;
            $mora[] = $value->mora;
            $nro_cuota[] = $value->nro_cuota;
            $modalidad[] = $value->modalidad;
            $tipo_estudiante[] = $value->tipo_estudiante;
            $id[] = $value->id;
        }
        $response["monto"] = $monto;
        $response["pagado"] = $pagado;
        $response["mora"] = $mora;
        $response["nro_cuota"] = $nro_cuota;
        $response["modalidad"] = $modalidad;
        $response["tipo_estudiante"] = $tipo_estudiante;
        $response["id"] = $id;
        $response["tarifarios"] = $tarifarios;
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
        // dd($request);
        // $rules = $request->validate([
        //     'inicio' => 'required',
        //     'fin' => 'required',
        //     'nro_cuota' => 'required',

        // ], $messages = [
        //     'required' => '* El campo :attribute es obligatorio.',
        //     'nro_cuota.required' => '* El campo número cuota es obligatorio.',
        // ]);

        // $periodoActual = Periodo::where('estado', '1')->first();

        DB::beginTransaction();
        try {
            foreach ($request->id as $key => $value) {
                $data = TarifaEstudiante::find($value);
                $data->monto = $request->monto[$key];
                $data->pagado = $request->pagado[$key];
                $data->mora = $request->mora[$key];
                // $data->nro_cuota = $nro_cuota[$key];
                $data->modalidad = $request->modalidad[$key];
                $data->tipo_estudiante = $request->tipo_estudiante[$key];
                $data->save();
            }

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
