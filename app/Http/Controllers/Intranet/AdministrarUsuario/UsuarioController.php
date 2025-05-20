<?php

namespace App\Http\Controllers\Intranet\AdministrarUsuario;

use App\Http\Controllers\Controller;
use App\Models\ModelHasRole;
use App\User;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
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
        return view('intranet.administrarUsuario.usuario', $response);
    }
    public function lista(Request $request)
    {

        $table = new EloquentVueTables;
        $data = $table->get(new User, [
            'users.id',
            'users.name',
            'users.paterno',
            'users.materno',
            'users.dni',
            'users.estado',
            'users.email',
            'r.name as rol',
            'r.id as rol_id'
        ]);
        // ->leftJoin('asistencia_docentes as ad', function ($join) use ($fecha) {
        //     $join->on('ad.carga_academicas_id', '=', 'horarios.carga_academicas_id')
        //         ->where('ad.fecha', '=', $fecha);
        // });
        if (!Auth::user()->hasRole('Super Admin')) {
            $data = $data->leftjoin('model_has_roles as mr', function ($join) {
                $join->on('mr.model_id', 'users.id')
                    ->where("mr.model_type", 'App\\User');
            })
                ->leftjoin('roles as r', function ($join) {
                    $join->on('r.id', 'mr.role_id')
                        ->where([['r.name', '!=', 'Super Admin'], ['r.guard_name', 'web']]);
                });
        } else {
            $data = $data->leftjoin('model_has_roles as mr', function ($join) {
                $join->on('mr.model_id', 'users.id')
                    ->where("mr.model_type", 'App\\User');
            })
                ->leftjoin('roles as r', function ($join) {
                    $join->on('r.id', 'mr.role_id')
                        ->where('r.guard_name', 'web');
                });
        }
        // ->where([['r.id', '!=', '1'], ['guard_name', 'web']])
        // ->orWhereNull('r.id');

        if (!isset($request->orderBy)) {
            $data = $data->orderBy('users.id', 'asc');
        }
        if (isset($request->orderBy) && $request->orderBy == "rol") {

            if ($request->ascending == '1') {

                $data = $data->orderBy('r.name', 'desc');
            } else {

                $data = $data->orderBy('r.name', 'asc');
            }
        }
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
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'dni' => 'required',
            'estado' => 'required',
            'email' => 'required',
            'password' => 'required'
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',

        ]);
        DB::beginTransaction();
        try {

            $users = new User;
            $users->name = $request->nombres;
            $users->paterno = $request->paterno;
            $users->materno = $request->materno;
            $users->dni = $request->dni;
            $users->estado = $request->estado;;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);
            $users->save();

            DB::commit();
            $message = "Usuario creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear nuevo usuario, intentelo nuevamente.";
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
        $response = User::find($id);
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
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'dni' => 'required',
            'estado' => 'required',
            'email' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',

        ]);
        // dd($request->all());
        $random = Str::random(16);
        DB::beginTransaction();
        try {
            $users = User::find($id);
            $users->name = $request->nombres;
            $users->paterno = $request->paterno;
            $users->materno = $request->materno;

            $users->dni = $request->dni;
            $users->estado = strval($request->estado);
            $users->email = $request->email;
            if (strval($request->estado) == '1') {
                if (!empty($request->password)) {
                    $users->password = Hash::make($request->password);
                }
            } else {
                $users->password = Hash::make($random);
            }
            $users->save();

            DB::commit();
            $message = 'Usuario actualizado correctamente';
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al actualizar usuario, intentelo nuevamente.';
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

    public function getRolUser($id)
    {
        $response = Role::find($id);

        return $response;
    }
    public function asignarRolStore(Request $request, $id)
    {
        $rules = $request->validate([
            'rol' => 'required',
        ], $messages = [
            'required' => '* Seleccione un rol.',

        ]);
        DB::beginTransaction();
        try {
            $rolWeb = Role::where([['name', $request->rol], ['guard_name', 'web']])->first();
            $rolSanctum = Role::where([['name', $request->rol], ['guard_name', 'sanctum']])->first();

            $usuario = User::find($id);

            ModelHasRole::where('model_id', $usuario->id)->delete();

            $modelHasRole = new ModelHasRole();
            $modelHasRole->role_id = $rolWeb->id;
            $modelHasRole->model_type = 'App\User';
            $modelHasRole->model_id = $usuario->id;
            $modelHasRole->save();

            if (isset($rolSanctum)) {
                $modelHasRole = new ModelHasRole();
                $modelHasRole->role_id = $rolSanctum->id;
                $modelHasRole->model_type = 'App\Models\User';
                $modelHasRole->model_id = $usuario->id;
                $modelHasRole->save();
            }
            // $usuario->syncRoles($request->rol);

            DB::commit();
            $message = "Rol asignado al usuario $usuario->name correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al asignar rol al usuario $usuario->name, intentelo nuevamente.";
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
}
