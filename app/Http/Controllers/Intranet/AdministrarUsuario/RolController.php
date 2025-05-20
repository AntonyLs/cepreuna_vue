<?php

namespace App\Http\Controllers\Intranet\AdministrarUsuario;

use App\Http\Controllers\Controller;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
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
        return view('intranet.administrarUsuario.roles', $response);
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new Role(),
            [
                'roles.id',
                'roles.name',
            ],
            ['permissions']
        );
        $data = $data->where('roles.id', '!=', 1);
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
            'name.required' => '* El campo nombre es obligatorio.',
        ]);

        DB::beginTransaction();
        try {

            $role = new Role;
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permisos);

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Rol agregado correctamente";
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] = "Error al agregar rol, intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema";
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
        // $role = Role::find(2);
        // $role->syncPermissions([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58]);
        $this->validate($request, [
            "name" => "required|unique:permissions,name,$id",
        ], $messages = [
            'name.required' => '* El campo nombre es obligatorio.',
        ]);

        DB::beginTransaction();
        try {

            $role = Role::find($id);
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->permisos);

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Rol actualizado correctamente";
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] = "Error al actualizar rol, intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema";
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
