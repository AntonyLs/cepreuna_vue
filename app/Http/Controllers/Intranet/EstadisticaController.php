<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CalificacionDocente;
use App\Models\CalificacionDocenteDetalle;
use App\Models\CurriculaDetalle;
use App\Models\GrupoAula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use stdClass;

class EstadisticaController extends Controller
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
        $response["fecha_fin"] = date("Y-m-d");
        $response["fecha_ini"] = date("Y-m-d", strtotime(date("Y-m-d") . "- 30 days"));;
        $response['permisos'] = json_encode($permissions);
        return view('intranet.estadistica.index', $response);
    }
    public function G1Reportes1Grafico(Request $request)
    {

        $areas = Area::all();
        $labels = [];
        $data = [];
        foreach ($areas as $key => $value) {
            $labels[] = $value->denominacion;
            $query = CalificacionDocenteDetalle::select()
                ->join("calificacion_docentes", "calificacion_docentes.id", "calificacion_docente_detalles.calificacion_docentes_id")
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                ->where("grupo_aulas.areas_id", $value->id)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("calificacion_docente_detalles.estudiantes_id")
                ->get();
            $data[] = count($query);
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G1Reportes2Grafico(Request $request)
    {
        $areas = Area::all();
        $labels = [];
        $data = [];
        foreach ($areas as $key => $value) {
            $labels[] = $value->denominacion;
            $query = CalificacionDocente::select(DB::raw("AVG(calificacion_docentes.promedio) as Promedio"))
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                // ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.areas_id", $value->id)
                ->where("calificacion_docentes.promedio", "!=", 0)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("carga_academicas.cursos_id")
                ->first();
            $data[] = $query != null ? round($query->Promedio, 2) : 0.00;
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G1Reportes3Grafico(Request $request)
    {
        $areas = Area::all();
        $labels = [];
        $data = [];
        foreach ($areas as $key => $value) {
            $labels[] = $value->denominacion;
            $query = CalificacionDocente::select(DB::raw("AVG(calificacion_docentes.promedio) as Promedio"))
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                // ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.areas_id", $value->id)
                ->where("calificacion_docentes.promedio", "!=", 0)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("carga_academicas.cursos_id")
                ->first();
            $data[] = $query != null ? round($query->Promedio, 2) : 0.00;
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G2Reportes1Grafico(Request $request)
    {
        $area = $request->area;
        $cursos = CurriculaDetalle::with("curso")->select("curricula_detalles.*")
            ->join("curriculas", "curriculas.id", "curricula_detalles.curriculas_id")
            ->where("curriculas.areas_id", $area)
            ->get();
        $labels = [];
        $data = [];
        foreach ($cursos as $key => $value) {
            $labels[] = $value->curso->denominacion;
            $query = CalificacionDocenteDetalle::select()
                ->join("calificacion_docentes", "calificacion_docentes.id", "calificacion_docente_detalles.calificacion_docentes_id")
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.areas_id", $area)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("calificacion_docente_detalles.estudiantes_id")
                ->get();
            $data[] = count($query);
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G2Reportes2Grafico(Request $request)
    {
        $area = $request->area;
        $cursos = CurriculaDetalle::with("curso")->select("curricula_detalles.*")
            ->join("curriculas", "curriculas.id", "curricula_detalles.curriculas_id")
            ->where("curriculas.areas_id", $area)
            ->get();
        $labels = [];
        $data = [];
        foreach ($cursos as $key => $value) {
            $labels[] = $value->curso->denominacion;
            $query = CalificacionDocente::select()
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.areas_id", $area)
                ->where("calificacion_docentes.promedio", "!=", 0)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("calificacion_docentes.docentes_id")
                ->get();
            $data[] = count($query);
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G2Reportes3Grafico(Request $request)
    {
        $area = $request->area;
        $cursos = CurriculaDetalle::with("curso")->select("curricula_detalles.*")
            ->join("curriculas", "curriculas.id", "curricula_detalles.curriculas_id")
            ->where("curriculas.areas_id", $area)
            ->get();
        $labels = [];
        $data = [];
        foreach ($cursos as $key => $value) {
            $labels[] = $value->curso->denominacion;
            $query = CalificacionDocente::select(DB::raw("AVG(calificacion_docentes.promedio) as Promedio"))
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.areas_id", $area)
                ->where("calificacion_docentes.promedio", "!=", 0)
                ->whereBetween("calificacion_docentes.created_at", [$request->fecha_ini, $request->fecha_fin])
                ->groupBy("carga_academicas.cursos_id")
                ->first();
            $data[] = $query != null ? round($query->Promedio, 2) : 0.00;
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
    public function G3Reportes1Grafico(Request $request)
    {

        $fechas = [];
        $labels = [];
        $colors = [];
        // dd($fechas);

        $area = GrupoAula::find($request->grupo);
        // dd($area);
        $cursos = CurriculaDetalle::with("curso")->select("curricula_detalles.*")
            ->join("curriculas", "curriculas.id", "curricula_detalles.curriculas_id")
            ->where("curriculas.areas_id", $area->areas_id)
            ->get();
        // $labels = [];
        $data = [];
        foreach ($cursos as $key => $value) {

            $query = CalificacionDocente::select("calificacion_docentes.*")
                ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                ->where("carga_academicas.cursos_id", $value->curso->id)
                ->where("grupo_aulas.id", $request->grupo)
                ->where("calificacion_docentes.promedio", "!=", 0)
                ->whereRaw("DATE_FORMAT(calificacion_docentes.created_at,'%Y-%m-%d') = ?", [$request->fecha_fin])
                // ->whereBetween("calificacion_docentes.created_at",[$val,$val])
                // ->groupBy("carga_academicas.cursos_id")
                ->first();
            $data[] = $query != null ? $query->promedio : 0.00;
            if ($query) {
                if ($query->promedio > 3) {
                    $colors[] = "#00B500";
                } else if ($query->promedio < 3) {
                    $colors[] = "#FF2600";
                } else {
                    $colors[] = "#F69F22";
                }
            } else {
                $colors[] = "#fff";
            }

            $labels[] = $value->curso->denominacion;
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        $response["colors"] = $colors;
        return $response;
    }
    public function G4Reportes1Grafico(Request $request)
    {
        $period = new \DatePeriod(
            new \DateTime($request->fecha_ini),
            new \DateInterval('P1D'),
            new \DateTime($request->fecha_fin)
        );
        $fechas = [];
        $labels = [];
        foreach ($period as $key => $value) {
            $fechas[] = $value->format('Y-m-d');
            $labels[] = $value->format('d/m/Y');
        }
        // dd($fechas);

        $area = GrupoAula::find($request->grupo);
        // dd($area);
        $cursos = CurriculaDetalle::with("curso")->select("curricula_detalles.*")
            ->join("curriculas", "curriculas.id", "curricula_detalles.curriculas_id")
            ->where("curriculas.areas_id", $area->areas_id)
            ->get();
        // $labels = [];
        $data = [];
        foreach ($cursos as $key => $value) {
            // $labels[] = $value->curso->denominacion;
            $promedio = [];
            $temp = Null;
            foreach ($fechas as $k => $val) {

                $query = CalificacionDocente::select("calificacion_docentes.*")
                    ->join("carga_academicas", "carga_academicas.id", "calificacion_docentes.carga_academicas_id")
                    ->join("grupo_aulas", "grupo_aulas.id", "carga_academicas.grupo_aulas_id")
                    ->where("carga_academicas.cursos_id", $value->curso->id)
                    ->where("grupo_aulas.id", $request->grupo)
                    ->where("calificacion_docentes.promedio", "!=", 0)
                    ->whereRaw("DATE_FORMAT(calificacion_docentes.created_at,'%Y-%m-%d') = ?", [$val])
                    // ->whereBetween("calificacion_docentes.created_at",[$val,$val])
                    // ->groupBy("carga_academicas.cursos_id")
                    ->first();
                // var_dump($query);
                if ($query) {
                    $temp = $query->promedio;
                    $promedio[] = $query->promedio;
                } else {
                    $promedio[] = $temp;
                }
                // $promedio[] = $query!=null?$query->promedio:0;
            }
            $objeto = new \stdClass;
            $objeto->label = $value->curso->denominacion;
            $objeto->borderColor = $value->curso->color;
            $objeto->borderCapStyle = 'transparent';
            $objeto->lineTension = 0.5;
            $objeto->fill = false;
            $objeto->backgroundColor = 'transparent';
            // $objeto->borderDash = [5, 5];
            $objeto->pointBorderColor = $value->curso->color;
            $objeto->pointBackgroundColor = 'rgba(255,150,0,0.5)';
            $objeto->pointRadius = 2;
            $objeto->pointHoverRadius = 5;
            $objeto->pointHitRadius = 30;
            // $objeto->pointBorderWidth = 2;
            // $objeto->pointStyle = 'rectRounded';
            $objeto->data = $promedio;

            $data[] = $objeto;
        }
        // dd($labels,$data);
        $response["labels"] = $labels;
        $response["data"] = $data;
        return $response;
    }
}
