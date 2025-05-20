<?php

namespace App\Http\Controllers\Intranet\RecursosHumanos\Pagos;

use App\Http\Controllers\Controller;
use App\Models\TPDocumento;
use App\Models\TPExpediente;
use App\Models\TPExpedienteDetalles;
use App\Models\TPTramite;
use App\Models\TPDocente;
use App\Models\TPHorasDocente;
use App\Models\User;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DocenteController extends Controller
{
    //
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
        $response['external_url'] = json_encode(env("EXTERNALURL"));
        $response['users'] = json_encode(User::select("id", "name")->get());
        return view("intranet.recursosHumanos.pagos.docentes.expedientes", $response);
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new TPExpediente, ["docente_id"], []);
        $data = $data->select(
            "expedientes.id",
            // "expedientes.estado",
            "expedientes.fecha_inicio",
            "expedientes.fecha_fin",
            "expedientes.horas",
            "expedientes.docente_id",
            "expedientes.periodo_id",
            // "tramites.id",
            "t.estado",
            "o.denominacion as oficina",
            "d.dni",
            "d.paterno",
            "d.materno",
            "d.nombres",
            DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo) as periodo"),
            "d.celular",
            "d.email",
            "t.user_id",
            "t.activo"
        );
        $data = $data->join("tramites as t", "t.expediente_id", "expedientes.id");
        $data = $data->join("oficinas as o", "o.id", "t.oficina_id");
        $data = $data->join("docentes as d", "d.id", "expedientes.docente_id");

        // $data = $data->join("documentos as do", "do.expediente_id", "expedientes.id");
        // $data = $data->join("tipo_documentos as td", "td.id", "do.tipo_documento_id");
        $data = $data->join("periodos as p", "p.id", "d.periodo_id");
        $data = $data->where("t.activo", '1');

        if (isset($request->estado)) {
            $data = $data->where("t.estado", $request->estado);
        }
        if (isset($request->periodo)) {
            $data = $data->where(DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo)"), $request->periodo);
        }
        if (isset($request->responsable)) {
            $data = $data->where("t.user_id", $request->responsable);
        }

        $response = $table->finish($data);
        return response()->json($response);
    }
    public function rptExpedientesExcel(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new TPExpediente, ["docente_id"], []);
        $data = $data->select(
            "expedientes.id as expediente_id",
            // "expedientes.estado",
            "expedientes.fecha_inicio as fecha_inicio",
            "expedientes.fecha_fin as fecha_fin",
            "expedientes.horas as cantidad_horas",
            // "expedientes.docente_id as docente_id",
            // "expedientes.periodo_id as periodo_id",
            // "tramites.id",
            "t.estado as tramite_estado",
            "o.denominacion as oficina",
            "d.dni as docente_dni",
            "d.paterno as apellido_paterno",
            "d.materno as apellido_materno",
            "d.nombres as nombres",
            DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo) as periodo"),
            "d.celular as celular",
            "d.email as email",
            // "t.user_id as responsable"
            "u.name as user_names"
        );

        $data = $data->join("tramites as t", "t.expediente_id", "expedientes.id");
        $data = $data->join("oficinas as o", "o.id", "t.oficina_id");
        $data = $data->join("docentes as d", "d.id", "expedientes.docente_id");
        $data = $data->leftJoin("tramite_pagos.users as u", "u.id", "t.user_id");

        // $data = $data->join("documentos as do", "do.expediente_id", "expedientes.id");
        // $data = $data->join("tipo_documentos as td", "td.id", "do.tipo_documento_id");
        $data = $data->join("periodos as p", "p.id", "d.periodo_id");

        if (isset($request->estado)) {
            $data = $data->where("t.estado", $request->estado);
        }
        if (isset($request->periodo)) {
            $data = $data->where(DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo)"), $request->periodo);
        }
        if (isset($request->responsable)) {
            $data = $data->where("t.user_id", $request->responsable);
        }

        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }

        return response()->json($response);
    }
    public function getDocumentosExpedienteDocente($id)
    {
        $response = DB::connection('mysql2')->table('documentos as d')
            ->select(
                "tdoc.denominacion as tipo_documento",
                "tdoc.id as tipo_documento_id",
                "d.estado as estado_documento",
                "d.observacion as estado_observacion",
                "d.path",
                "d.id"
            )
            ->join("expedientes as e", "e.id", "d.expediente_id")
            ->join("tipo_documentos as tdoc", "tdoc.id", "d.tipo_documento_id")
            ->where([["e.id", $id], ["d.activo", "1"]])
            ->get();

        return response()->json($response);
    }

    public function evaluarDocente(Request $request)
    {
        // dd($request->estado[0]);
        // dd($request);
        $estadoExpediente = "3";
        DB::beginTransaction();
        try {
            foreach ($request->estado as $key => $value) {
                if (isset($value)) {
                    // dd($key);
                    $documento = TPDocumento::where([["tipo_documento_id", $key], ["expediente_id", $request->id], ["activo", '1']])->first();
                    $documento->estado = $value;
                    $documento->observacion = isset($request->observacion[$key]) ? $request->observacion[$key] : "";
                    // $documento->activo = "1";
                    // $documento->tipo_documento_id = $idTipoDocumento;
                    // $documento->expediente_id = $expediente->id;
                    $documento->save();
                    if ($value == "2") {
                        $estadoExpediente = "2";
                    }
                }
            }
            $tramitesAnteriores = TPTramite::where("expediente_id", $request->id)
                ->update(["activo" => "0"]);

            // foreach ($tramitesAnteriores as $key => $value) {
            //     $actualizar = TPTramite::find($value->id);
            //     $actualizar->activo = '0';
            //     $actualizar->save();
            // }

            $tramite = new TPTramite;
            $tramite->estado = $estadoExpediente;
            $tramite->activo = "1";
            // $tramite->mensaje = "";
            $tramite->expediente_id = $request->id;
            $tramite->oficina_id = 1;
            $tramite->user_id = Auth::user()->id;
            $tramite->save();

            DB::commit();

            $response["status"] = true;
            $response["message"] = 'Docente evaluado correctamente';
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] =  'Error al evaluar docentes, intentelo nuevamante.';
            $response["e"] = $e->getMessage();
        }
        return $response;
    }
    public function indexHorasMes()
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

        $response['tipos'] = DB::connection('mysql2')->table('tipo_pagos')
            ->get();
        $response['permisos'] = json_encode($permissions);
        return view("intranet.recursosHumanos.pagos.docentes.horas-mes", $response);
    }
    public function listaHorasMes(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new TPDocente, ['id', 'dni', 'paterno', 'materno', 'nombres', 'condicion', 'direccion', 'celular', 'email', 'periodo_id'], ['periodo']);
        if (isset($request->periodo)) {
            $data = $data->where('docentes.periodo_id', $request->periodo);
        }
        $response = $table->finish($data);
        return response()->json($response);
    }
    public function getMesHorasPeriodo($id)
    {
        $docente = TPDocente::find($id);
        $periodo = $docente->periodo_id;
        $validateHoras = DB::connection('mysql2')->table('horas_docente as hd')
            ->select(
                "hd.*"
            )
            ->join("meses as m", "m.id", "hd.mes_id")
            ->where("hd.docente_id", $id)
            ->where("m.periodo_id", $periodo)
            ->orderBy("m.id", "asc")
            ->get();
        // dd($validateHoras);
        $data = DB::connection('mysql2')->table('meses as m')->where("m.periodo_id", $periodo)->orderBy("m.id", "asc")->get();
        if (count($validateHoras) > 0) {
            // $data = $calificacion;
            // $data = DB::connection('mysql2')->table('meses as m')
            //     ->select("m.*", "hd.cantidad")
            //     ->leftJoin('horas_docente as hd', function ($join) use ($id) {
            //         $join->on("hd.mes_id", "=", "m.id")
            //             ->where('hd.docente_id', $id);
            //     })
            //     ->where("m.periodo_id", $periodo)
            //     ->orderBy("m.id", "asc")
            //     ->get();

            // $data = Criterio::select("criterios.*", "idd.puntaje as Puntos")
            //     // ->rightJoin("inscripcion_docente_detalles as idd","idd.criterios_id","criterios.id")
            //     ->leftJoin('inscripcion_docente_detalles as idd', function ($join) use ($id) {
            //         $join->on("idd.criterios_id", "=", "criterios.id")
            //             ->where('idd.inscripcion_docentes_id', $id);
            //     })
            //     ->where("criterios.tipo", "3")->where("criterios.estado", "1")->get();
            // dd($data);
            $status = true;
            $varibles = [];
            foreach ($data as $key => $value) {
                // $varibles[$value->id] = $value->cantidad != NULL ? $value->cantidad : 0;
                $data[$key]->tipos = DB::connection('mysql2')->table('horas_docente')
                    ->where("mes_id", $value->id)
                    ->where("docente_id", $id)
                    ->orderBy("tipo_pago_id", "asc")->get();
            }
            $cantidad = $varibles;
        } else {
            // $data = DB::connection('mysql2')->table('meses as m')->where("m.periodo_id", $periodo)->orderBy("m.id", "asc")->get();
            // $data = Criterio::where("tipo", "3")->where("estado", "1")->get();
            $status = false;
            // $cantidad[] = [];
            $varibles = [];
            foreach ($data as $key => $value) {
                $varibles[$value->id] = 0;
                $data[$key]->tipos = [];
            }
            $cantidad = $varibles;
        }
        // $response = Criterio::where("tipo","3")->get();
        $response["data"] = $data;
        $response["cantidad"] = $cantidad;
        $response["status"] = $status;
        return $response;
    }
    public function storeHorasDocente(Request $request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            foreach ($request->mes as $key => $value) {
                foreach ($value as $k => $val) {
                    $data = new TPHorasDocente;
                    $data->cantidad = $request->cantidad[$key][$k];
                    $data->mes_id = $val;
                    $data->tipo_pago_id = $request->tipo[$key][$k];
                    $data->docente_id = $request->id;
                    $data->save();
                }
            }
            $docente = TPDocente::find($request->id);
            if ($request->contrato == "No") {
                $docente->contrato = '0';
            } else {
                $docente->contrato = '1';
            }
            $docente->save();

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
    // public function updateHorasDocente(Request $request, $id)
    // {

    //     DB::beginTransaction();
    //     try {
    //         $horas = TPHorasDocente::where("docente_id", $id)->get();
    //         $mesesEnviados = [];
    //         // return $id;
    //         // $puntaje = 0;
    //         // $apto = '0';
    //         foreach ($request->mes as $key => $value) {
    //             foreach ($value as $k => $val) {
    //                 $mesesEnviados[] = $val;
    //                 $data = $horas->firstWhere('mes_id', $val);
    //                 // return $data;
    //                 // $data = new TPHorasDocente;
    //                 // $data->cantidad = $request->cantidad[$key][$k];
    //                 // return $request->id;
    //                 // $data->mes_id = $val;
    //                 // $data->tipo_pago_id = $request->tipo[$key][$k];
    //                 // $data->docente_id = $request->id;
    //                 // $data->save();

    //                 if ($data) {
    //                     // Si existe, actualizamos el registro
    //                     $data->cantidad = $request->cantidad[$key][$k];
    //                     $data->tipo_pago_id = $request->tipo[$key][$k];
    //                     $data->save();
    //                 } else {
    //                     // Si no existe, creamos uno nuevo
    //                     $data = new TPHorasDocente;
    //                     $data->cantidad = $request->cantidad[$key][$k];
    //                     $data->mes_id = $val;
    //                     $data->tipo_pago_id = $request->tipo[$key][$k];
    //                     $data->docente_id = $id; // Usar el id pasado como parámetro
    //                     $data->save();
    //                 }
    //             }
    //         }
    //         foreach ($horas as $hora) {
    //             if (!in_array($hora->mes_id, $mesesEnviados)) {
    //                 $hora->delete();
    //             }
    //         }
    //         DB::commit();
    //         $response["message"] = 'Guardado';
    //         $response["status"] = true;
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         // dd($e);
    //         $response["message"] =  'Error al Guardar, intentelo nuevamante.';
    //         $response["status"] =  false;
    //     }
    //     return $response;
    // }
    public function updateHorasDocente(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Obtener los IDs de horas enviados y filtrarlos
            $idsEnviados = array_filter(array_merge(...$request->ids ?? []));
            $horasExistentes = TPHorasDocente::where("docente_id", $id)->get();

            // Eliminar las horas que no están en los IDs enviados
            foreach ($horasExistentes as $hora) {
                if (!in_array($hora->id, $idsEnviados)) {
                    TPExpedienteDetalles::where('hora_docente_id', $hora->id)->delete();
                    $hora->delete();
                }
            }

            foreach ($request->mes as $key => $mesArray) {
                foreach ($mesArray as $k => $mesId) {
                    if (isset($request->cantidad[$key][$k]) && isset($request->tipo[$key][$k])) {
                        $horaExistente = $horasExistentes->firstWhere('id', $request->ids[$key][$k] ?? null);
                        if ($horaExistente) {
                            $horaExistente->cantidad = $request->cantidad[$key][$k];
                            $horaExistente->tipo_pago_id = $request->tipo[$key][$k];
                            $horaExistente->save();
                        } else {
                            $nuevaHora = new TPHorasDocente;
                            $nuevaHora->cantidad = $request->cantidad[$key][$k];
                            $nuevaHora->mes_id = $mesId;
                            $nuevaHora->tipo_pago_id = $request->tipo[$key][$k];
                            $nuevaHora->docente_id = $id;
                            $nuevaHora->save();
                        }
                    }
                }
            }

            DB::commit();
            $response["message"] = 'Guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] = 'Error al guardar, inténtelo nuevamente.';
            $response["status"] = false;
            dd($e);
        }

        return $response;
    }
    public function getPeriodos()
    {
        $response = DB::connection('mysql2')->table('periodos')->get();
        return $response;
    }
    public function getPeriodosFilter()
    {
        $response = DB::connection('mysql2')->table('periodos as p')->select(DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo) as periodo"))->get();
        return $response;
    }
    public function getResponsables()
    {
        $response = DB::connection('mysql2')->table('expedientes as e')
            ->join("tramites as t", "t.expediente_id", "e.id")
            ->join("tramite_pagos.users as u", "u.id", "t.user_id")
            ->select("t.user_id", "u.name as user_names")
            ->distinct()
            ->get();
        return $response;
    }
}
