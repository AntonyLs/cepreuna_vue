<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use Illuminate\Validation\Rule;
use App\Models\Sede;
use DB;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.sede");
    }

    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new Sede,['id','denominacion','estado','ubigeos_id'],['ubigeo']);
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
            'denominacion' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'departamento' => 'required',
            'provincia' => 'required',
            'ubigeo' => 'required',
           
        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new Sede;
            $data->denominacion = $request->denominacion;
            $data->direccion = $request->direccion;
            $data->estado = (string)$request->estado;
            $data->ubigeos_id = $request->ubigeo;
            $data->save();



            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
            $response["status"] =  false;
        }

            // $response = array(
            //     "message" => $message,
            //     "status" => $status,
            //     "url" => $url,
            //     "error" => $error
            // );
        // }

        // return response()->json($response);
        return $response;
        // dd($request->denominacion);
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
        $response = Sede::with("ubigeo")->find($id);
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
        // return ($request);
        $rules = $request->validate([
            'denominacion' => 'required',
            'estado' => 'required',
            'direccion' => 'required',
            'departamento' => 'required',
            'provincia' => 'required',
            'ubigeo' => 'required',
           
        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = Sede::find($id);
            $data->denominacion = $request->denominacion;
            $data->direccion = $request->direccion;
            $data->estado = (string)$request->estado;
            $data->ubigeos_id = $request->ubigeo;
            $data->save();



            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
            $response["status"] =  false;
        }

            // $response = array(
            //     "message" => $message,
            //     "status" => $status,
            //     "url" => $url,
            //     "error" => $error
            // );
        // }

        // return response()->json($response);
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
