<?php

namespace App\Http\Controllers\Intranet\RecursosHumanos\Pagos;

use App\Http\Controllers\Controller;
use App\Models\TPDocente;
use App\Models\TPDocumento;
use App\Models\TPExpediente;
use App\Models\TPExpedienteDetalles;
use App\Models\TPTramite;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class HabilitacionDocenteController extends Controller
{
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
        return view("intranet.recursosHumanos.pagos.docentes.habilitacion", $response);
    }
    public function lista(Request $request)
    {
        // $table = new EloquentVueTables;
        // $data = $table->get(new TPDocente, ['id', 'dni', 'paterno', 'materno', 'nombres', 'condicion', 'direccion', 'celular', 'email', 'periodo_id'], ['periodo']);
        // if (isset($request->periodo)) {
        //     $data = $data->where('docentes.periodo_id', $request->periodo);
        // }
        // $response = $table->finish($data);
        // return response()->json($response);
        // $data = $table->get(new TPDocente, ['docentes.id', 'docentes.dni', 'docentes.paterno', 'docentes.materno', 'docentes.nombres', 'docentes.condicion', 'docentes.direccion', 'docentes.celular', 'docentes.email', 'docentes.periodo_id'], ['periodo']);
        $table = new EloquentVueTables;
        $data = $table->get(new TPDocente, ['docentes.id', 'dni', 'paterno', 'materno', 'nombres', 'condicion', 'docentes.periodo_id', 'contrato'], ['periodo', 'expediente']);
        // $data = $data->select(
        //     "docentes.*"
        //     // "e.estado",
        //     // DB::raw("CONCAT(p.inicio_ciclo,'-',p.fin_ciclo) as periodo")
        // );
        // $data = $data->leftJoin("expedientes as e", "e.docente_id", "docentes.id");
        // $data = $data->join("periodos as p", "p.id", "docentes.periodo_id");
        // $data = $data->where("e.estado", "0");
        // $data = $data->orWhere("e.estado", NULL);

        if (isset($request->condicion)) {
            $data = $data->where("docentes.condicion", $request->condicion);
        }
        
        if (isset($request->contrato)) {
            $data = $data->where("docentes.contrato", $request->contrato);
        }

        if (isset($request->dni)) {
            $data = $data->where("docentes.dni", $request->dni);
        }

        $response = $table->finish($data);
        return response()->json($response);
    }
    public function store(Request $request)
    {
        // $horas = DB::connection("mysql2")->table("horas_docente as hd")
        //     ->join("docentes as d", "d.id", "hd.docente_id")
        //     ->join("meses as m", "m.id", "hd.mes_id")
        //     ->where([["d.id", 2], ["m.id", 3]])
        //     ->sum("hd.cantidad");
        // dd($horas);
        $periodo = DB::connection("mysql2")->table("periodos")->first();
        
        DB::beginTransaction();
        try {

            foreach ($request->checks as $key => $idDocente) {
                
                $tpDocente = TPDocente::find($idDocente);
                $tpDocente->estado_correo = '1';
                $tpDocente->save();

                $suma = 0;

                $expediente = new TPExpediente;
                $expediente->estado = '0';
                $expediente->fecha_inicio = date("Y-m-d H:i:s");
                
                foreach ($request->meses as $key => $idMes) {
                    $horas = DB::connection("mysql2")->table("horas_docente as hd")
                        ->join("docentes as d", "d.id", "hd.docente_id")
                        ->join("meses as m", "m.id", "hd.mes_id")
                        ->where([["d.id", $idDocente], ["m.id", $idMes]])
                        ->sum("hd.cantidad");
                    $suma = $suma + $horas;
                }
                $expediente->horas = $suma;
                $expediente->docente_id = $idDocente;
                $expediente->periodo_id = $periodo->id;
                $expediente->save();

                $tramite = new TPTramite;
                $tramite->estado = "0";
                $tramite->mensaje = "";
                $tramite->expediente_id = $expediente->id;
                $tramite->oficina_id = 1;
                $tramite->mensaje = $request->mensaje;
                $tramite->user_id = Auth::user()->id;
                $tramite->save();

                foreach ($request->tipo_documentos as $key => $idTipoDocumento) {
                    $documento = new TPDocumento;
                    $documento->estado = "0";
                    $documento->activo = "1";
                    $documento->tipo_documento_id = $idTipoDocumento;
                    $documento->expediente_id = $expediente->id;
                    $documento->save();
                }

                foreach ($request->meses as $key => $idMes) {
                    $horas = DB::connection("mysql2")->table("horas_docente as hd")
                        ->join("docentes as d", "d.id", "hd.docente_id")
                        ->join("meses as m", "m.id", "hd.mes_id")
                        ->select("hd.id as hd_id", "hd.tipo_pago_id as hd_tipo_pago", "hd.mes_id as hd_mes", "hd.cantidad as hd_cantidad", "d.*", "m.*")
                        ->where([["d.id", $idDocente], ["m.id", $idMes]])
                        ->get();
                        // dd($horas);
                    foreach ($horas as $h){
                        $expedienteDetalles = new TPExpedienteDetalles;
                        $expedienteDetalles->expediente_id = $expediente->id;
                        // dd($h);
                        $expedienteDetalles->hora_docente_id = $h->hd_id;
                        $expedienteDetalles->save();
                    }
                }
            }

            DB::commit();
            $response["message"] = 'Docentes habilitados correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al habilitar docentes, intentelo nuevamante.';
            $response["error"] = $e;
            $response["status"] =  false;
        }
        return $response;
    }
    public function deshabilitar()
    {
        DB::beginTransaction();
        try {
            $expedientes = TPExpediente::all();
            foreach ($expedientes as $expediente){
                $expediente->estado = "1";
                $expediente->fecha_fin = date("Y-m-d H:i:s");
                $tramites = TPTramite::where("expediente_id",$expediente->id)->orderByDesc('created_at')->limit(1)->get();
                foreach($tramites as $tramite){
                    $tramite->activo = "0";
                    $tramite->save();
                }
                $expediente->save();
            }

            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamente.';
            $response["status"] =  false;
        }
        return $response;
    }
}
