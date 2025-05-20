<?php

namespace App\Http\Controllers\Intranet\AdministrarNosotros;

use App\Http\Controllers\Controller;
use App\User;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\NosotrosDescripcion;

class ObjetivosController extends Controller
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
        return view('intranet.administrarNosotros.objetivos', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new NosotrosDescripcion,['id','descripcion','activo','nosotros_tipo_dato_id'],[]);
        $data =  $data->whereIn('nosotros_tipo_dato_id', [3, 4]);
        
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
            'nosotros_tipo_dato_id' => 'required',
            'descripcion' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $objetivo = new NosotrosDescripcion;
            $objetivo->descripcion = $request->descripcion;
            $objetivo->activo = '1';
            $objetivo->nosotros_tipo_dato_id = $request->nosotros_tipo_dato_id;
            $objetivo->save();
            
            DB::commit();
            $message = "Objetivo creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear objetivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
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
        $response = NosotrosDescripcion::find($id);
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
            'nosotros_tipo_dato_id' => 'required',
            'descripcion' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);
        
        DB::beginTransaction();
        try {
            $objetivo = NosotrosDescripcion::find($id);
            $objetivo->descripcion = $request->descripcion;
            $objetivo->activo = '1';
            $objetivo->nosotros_tipo_dato_id = $request->nosotros_tipo_dato_id;
            $objetivo->save();
            
            DB::commit();
            $message = "Objetivo actualizado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al actualizar objetivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
    }

    public function desactivar ($id)
    {
        DB::beginTransaction();
        try {
            $objetivo = NosotrosDescripcion::find($id);
            if($objetivo->activo == '1'){
                $objetivo->activo = '0';
            } else {
                $objetivo->activo = '1';
            }
            $objetivo->save();

            DB::commit();
            $message = "Objetivo desactivado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al desactivar objetivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
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
