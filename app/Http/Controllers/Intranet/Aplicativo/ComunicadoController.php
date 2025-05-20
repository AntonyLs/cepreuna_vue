<?php

namespace App\Http\Controllers\Intranet\Aplicativo;

use App\Http\Controllers\Controller;
use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\DB;

class ComunicadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }

        $response['permisos'] = json_encode($permissions);
        return view("intranet.aplicativo.comunicado",$response);
    }

    public function lista(Request $request)
    {
        // dd($request);
        $table = new EloquentVueTables;
        $data = $table->get(new Comunicado, ['id', 'titulo', 'mostrar', 'estado', 'users_id'], ['user']);
        $data = $data->orderBy('id', 'desc');
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
        // dd(Auth::user()->id);
        $rules = $request->validate([
            'titulo' => 'required',
            'mostrar' => 'required',
            'estado' => 'required',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {
            // if((string)$request->estado=='1'){
            //     Comunicado::select()->update(["estado"=>"0"]);
            // }
            $id_user = Auth::user()->id;
            $data = new Comunicado;
            $data->titulo = $request->titulo;
            $data->mostrar = (string)$request->mostrar;
            $data->estado = (string)$request->estado;
            $data->contenido = $request->contenido;
            $data->users_id = $id_user;
            $data->save();



            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
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
        $response = Comunicado::find($id);
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
            'titulo' => 'required',
            'mostrar' => 'required',
            'estado' => 'required',

        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {
            // if((string)$request->estado=='1'){
            //     Comunicado::select()->update(["estado"=>"0"]);
            // }
            $id_user = Auth::user()->id;
            $data = Comunicado::find($id);
            $data->titulo = $request->titulo;
            $data->mostrar = (string)$request->mostrar;
            $data->estado = (string)$request->estado;
            $data->contenido = $request->contenido;
            $data->users_id = $id_user;
            $data->save();



            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
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
    public function mostrar($mostrar){

        $query = Comunicado::where("estado","1")
                ->where(function($query) use($mostrar) {
                    $query->where("mostrar",(string)$mostrar)
                    ->orWhere("mostrar","3");
                })
                ->first();
        if($query){
            $response["message"] = '';
            $response["data"] = $query;
            $response["status"] = true;
        }else{
            $response["message"] =  'No hay comunicados activos.';
            $response["status"] =  false;
        }
        // dd($query);
        return $response;

    }
}
