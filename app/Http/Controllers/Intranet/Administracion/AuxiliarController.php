<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Auxiliar;
use App\Models\AuxiliarGrupo;
use App\Models\GrupoAula;
use App\Models\ModelHasRole;
use App\User;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuxiliarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('intranet.administracion.auxiliar');
    }
    public function lista(Request $request)
    {

        $table = new EloquentVueTables;
        $data = $table->get(new Auxiliar, [
            'auxiliares.id',
            'auxiliares.telefono',
            'auxiliares.users_id',
        ], ['user']);


        if (!isset($request->orderBy)) {
            $data = $data->orderBy('auxiliares.id', 'asc');
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

            // $users->syncRoles('Auxiliar');

            $rolWeb = Role::where([['name', 'Auxiliar'], ['guard_name', 'web']])->first();
            $rolSanctum = Role::where([['name', 'Auxiliar'], ['guard_name', 'sanctum']])->first();

            // $usuario = User::find($id);

            ModelHasRole::where('model_id', $users->id)->delete();

            $modelHasRole = new ModelHasRole();
            $modelHasRole->role_id = $rolWeb->id;
            $modelHasRole->model_type = 'App\User';
            $modelHasRole->model_id = $users->id;
            $modelHasRole->save();

            if (isset($rolSanctum)) {
                $modelHasRole = new ModelHasRole();
                $modelHasRole->role_id = $rolSanctum->id;
                $modelHasRole->model_type = 'App\Models\User';
                $modelHasRole->model_id = $users->id;
                $modelHasRole->save();
            }


            $auxiliar = new Auxiliar;
            $auxiliar->telefono = $request->celular;
            $auxiliar->users_id = $users->id;
            $auxiliar->save();

            DB::commit();
            $message = "Auxiliar creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear nuevo auxiliar, intentelo nuevamente.";
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
        $response = Auxiliar::with('user')->find($id);
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

        $random = Str::random(16);
        DB::beginTransaction();
        try {
            $auxiliar = Auxiliar::find($id);
            $auxiliar->telefono = $request->celular;
            $auxiliar->save();

            $users = User::find($auxiliar->users_id);
            $users->name = $request->nombres;
            $users->paterno = $request->paterno;
            $users->materno = $request->materno;
            $users->dni = $request->dni;
            $users->estado = $request->estado;
            $users->email = $request->email;

            if ($request->estado == '1') {
                if (!empty($request->password)) {
                    $users->password = Hash::make($request->password);
                }
            } else {
                $users->password = Hash::make($random);
            }
            $users->save();



            DB::commit();
            $message = 'Auxiliar actualizado correctamente';
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al actualizar auxiliar, intentelo nuevamente.';
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
    public function AsignarGrupos(Request $request, $id)
    {
        // dd($request->grupos[0]);

        DB::beginTransaction();
        try {
            AuxiliarGrupo::where("auxiliares_id", $id)->delete();
            foreach ($request->grupos as $value) {

                $grupoAuxiliar = new AuxiliarGrupo;
                $grupoAuxiliar->auxiliares_id = $id;
                $grupoAuxiliar->grupo_aulas_id = $value["id"];
                $grupoAuxiliar->save();
            }

            DB::commit();
            $message = "Grupos asignados correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al asignar grupos, intentelo nuevamente.";
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
    public function GruposAsignados($id)
    {
        $gruposAsignados = AuxiliarGrupo::select("auxiliar_grupos.grupo_aulas_id as id", "g.denominacion")
            ->join("grupo_aulas as ga", "ga.id", "auxiliar_grupos.grupo_aulas_id")
            ->join("grupos as g", "g.id", "ga.grupos_id")
            ->where("auxiliares_id", $id)->get();

        // $gruposAsignados = GrupoAula::with(["aula", "grupo"])
        //     ->select("grupo_aulas.*", "g.denominacion")
        //     ->join("grupos as g", "g.id", "grupo_aulas.grupos_id")
        //     ->join("auxiliar_grupos as ag", "ag.grupo_aulas_id", "grupo_aulas.id")
        //     ->where("ag.auxiliares_id", $id)
        //     ->orderBy("g.denominacion", "asc")
        //     ->get();

        return $gruposAsignados;
    }
}
