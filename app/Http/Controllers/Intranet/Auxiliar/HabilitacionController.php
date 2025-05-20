<?php

namespace App\Http\Controllers\Intranet\Auxiliar;

use App\Http\Controllers\Controller;
use App\Models\Auxiliar;
use App\Models\AuxiliarGrupo;
use App\Models\Matricula;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Models\TarifaEstudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\DB;

class HabilitacionController extends Controller
{
    public function index(){
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
        return view("intranet.auxiliar.habilitacion", $response);
    }
    public function lista(Request $request)
    {
        // if (auth()->user()->hasRole('Super Admin')) {

        // }
        $auxiliar = Auxiliar::where("users_id",Auth::user()->id)->first();
        $grupos = [];
        if($auxiliar){
            $gruposAulas = AuxiliarGrupo::where("auxiliares_id",$auxiliar->id)->get();
            foreach ($gruposAulas as $key => $value) {
                $grupos[] = $value->grupo_aulas_id;
            }
        }
        $table = new EloquentVueTables;
        $data = $table->get(
            new Matricula,
            [
                'matriculas.id',
                'matriculas.grupo_aulas_id',
                'matriculas.turnos_id',
                'matriculas.periodos_id',
                'matriculas.estudiantes_id',
                'matriculas.habilitado',
                "a.denominacion as area",
                "g.denominacion as grupo"
            ],
            ['estudiante', 'turno'],
            ["turno"]
        );
        $data = $data->join("grupo_aulas as gu", "gu.id", "matriculas.grupo_aulas_id")
            ->join("areas as a", "a.id", "gu.areas_id")
            ->join("grupos as g", "g.id", "gu.grupos_id");

        if (!auth()->user()->hasRole('Super Admin|Administrador|Coordinador Auxiliar')){
            $data = $data->whereIn('gu.id', $grupos);
        }

        if (isset($request->area)) {
            $data = $data->where('a.id', $request->area);
        }
        if (isset($request->turno)) {
            $data = $data->where('matriculas.turnos_id', $request->turno);
        }
        if (isset($request->grupo)) {
            $data = $data->where('matriculas.grupo_aulas_id', $request->grupo);
        }
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function show($documento){
        $estu = Estudiante::select("estudiantes.*")
                    ->join("matriculas as m","m.estudiantes_id","estudiantes.id")
                    ->where("nro_documento",$documento)->first();
        if($estu){

            $inscripcion = Inscripciones::where("estudiantes_id",$estu->id)->first();
            // dd($estu,$inscripcion);
            $estudiante = $inscripcion->estudiante()->with('colegio')->first();
            $matricula = Matricula::where("estudiantes_id",$estu->id)->first();
            $area = $inscripcion->area()->first();
            $sede = $inscripcion->sede()->first();
            $periodo = $inscripcion->periodo()->first();
            $turno = $inscripcion->turno()->first();
            $pago = $inscripcion->pago()->get();
            $inscripcionPagos = $inscripcion->inscripcionPago()->with('conceptoPago')->orderBy('concepto_pagos_id')->get();
            $sumaTotalPagos = $inscripcion->inscripcionPago()->sum('monto');
            $tarifaEstudiante = TarifaEstudiante::where("estudiantes_id", $estudiante->id)->get();
            $response = array(
                "inscripcion" => $inscripcion,
                "estudiante" => $estudiante,
                "matricula" => $matricula,
                "area" => $area,
                "sede" => $sede,
                "periodo" => $periodo,
                "turno" => $turno,
                "pagos" => $pago,
                "inscripcionPagos" => $inscripcionPagos,
                "sumaTotalPagos" => $sumaTotalPagos,
                "tarifaEstudiante" => $tarifaEstudiante,
                "status" => true,
                "message" => "",
            );
        }else{
            $response = array(
                "status" => false,
                "message" => "Estudiante no encontrado",
            );
        }

        return response()->json($response);
    }
    public function habilitar($id){
        DB::beginTransaction();
        try {

            $data = Matricula::find($id);
            $data->habilitado = '1';
            $data->save();

            DB::commit();
            $response["message"] = 'Estudiante Habilitado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al habilitar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
    }
    public function deshabilitar($id){
        DB::beginTransaction();
        try {

            $data = Matricula::find($id);
            $data->habilitado = '0';
            $data->save();

            DB::commit();
            $response["message"] = 'Estudiante desabilitado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al deshabilitar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
    }
}
