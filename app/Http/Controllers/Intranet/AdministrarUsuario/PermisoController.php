<?php

namespace App\Http\Controllers\Intranet\AdministrarUsuario;

use App\Http\Controllers\Controller;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermisoController extends Controller
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
        return view('intranet.administrarUsuario.permisos', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new Permission(),
            [
                'permissions.id',
                'permissions.name',
            ],
            []
        );
        $data = $data->orderBy('id', 'asc');

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
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
        ], $messages = [
            // 'required' => '* El campo :attribute es obligatorio.',
            'name.required' => '* El campo nombre es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $permiso = new Permission;
            $permiso->name = $request->name;
            $permiso->save();

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Permiso agregado correctamente";
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] = "Error al agregar permiso, intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema";
            $response["e"] = $e->getMessage();
        }
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
        $response = Permission::find($id);
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
        $this->validate($request, [
            "name" => "required|unique:permissions,name,$id",
        ], $messages = [
            "name.required" => "* El campo nombre es obligatorio.",
        ]);

        DB::beginTransaction();
        try {
            $permiso = Permission::find($id);
            $permiso->name = $request->name;
            $permiso->save();

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Permiso actualizado correctamente";
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] = "Error al actualizar permiso, intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema";
            $response["e"] = $e->getMessage();
        }
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
