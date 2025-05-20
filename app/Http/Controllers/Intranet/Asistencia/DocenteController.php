<?php

namespace App\Http\Controllers\Intranet\Asistencia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Models\AsistenciaDocente;
use App\Models\CalificacionDocente;
use App\Models\CalificacionDocenteDetalle;
use App\VueTables\EloquentVueTables;

class DocenteController extends Controller
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

        return view("intranet.asistencia.docente", $response);
    }
    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new AsistenciaDocente, ['id', 'estado', 'fecha','hora_inicio','hora_fin','cantidad_horas','horas_pago', 'docentes_id','carga_academicas_id'], ['docente','carga']);
        $data = $data->select("asistencia_docentes.*");
        $data = $data->join("carga_academicas as ca","asistencia_docentes.carga_academicas_id","ca.id")
            ->join("grupo_aulas as gu", "gu.id", "ca.grupo_aulas_id")
            ->join("areas as a", "a.id", "gu.areas_id")
            ->join("grupos as g", "g.id", "gu.grupos_id");

        if (isset($request->grupo)) {
            $data = $data->where('ca.grupo_aulas_id', $request->grupo);
        }
        if (isset($request->sede)) {
            $data = $data->join("aulas as au", "au.id", "gu.aulas_id")
                ->join("locales as l", "l.id", "au.locales_id")
                ->where("l.sedes_id", $request->sede);
        }
        // $data = $data->where("apto","0");
        if(isset($request->fecha)){
            $data = $data->where('asistencia_docentes.fecha', $request->fecha);
        }
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {
            $calificacion = CalificacionDocente::where("asistencia_docentes_id",$id)->first();
            if($calificacion){

                $detalle = CalificacionDocenteDetalle::where("calificacion_docentes_id",$calificacion->id)->delete();
            }
            CalificacionDocente::where("asistencia_docentes_id",$id)->delete();
            $data = AsistenciaDocente::find($id);
            $data->delete();

            DB::commit();
            $response["message"] = 'Asistencia eliminada';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al eliminar, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return response()->json($response);
    }
}
