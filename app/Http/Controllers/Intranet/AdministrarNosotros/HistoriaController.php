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

class HistoriaController extends Controller
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
        return view('intranet.administrarNosotros.historia', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new NosotrosDescripcion,['id','descripcion','activo','nosotros_tipo_dato_id'],[]);
        $data =  $data->where('nosotros_tipo_dato_id', 5);
        
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
            'descripcion' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $historia = new NosotrosDescripcion;
            $historia->descripcion = $request->descripcion;
            $historia->activo = '1';
            $historia->nosotros_tipo_dato_id = 5;
            $historia->save();
            
            DB::commit();
            $message = "Item creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear item, intente nuevamente.";
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
            $historia = NosotrosDescripcion::find($id);
            $historia->descripcion = $request->descripcion;
            $historia->activo = '1';
            $historia->save();
            
            DB::commit();
            $message = "Item actualizado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al actualizar item, intente nuevamente.";
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
            $historia = NosotrosDescripcion::find($id);
            if($historia->activo == '1'){
                $historia->activo = '0';
            } else {
                $historia->activo = '1';
            }
            $historia->save();

            DB::commit();
            $message = "Item desactivado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al desactivar item, intente nuevamente.";
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
