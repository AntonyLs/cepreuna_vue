<?php

namespace App\Http\Controllers\Intranet;

use PDF;
use TCPDF;
use App\Models\Pago;
use App\Models\User;
use App\Models\Turno;
use App\Models\Horario;
use App\Models\Periodo;
use App\Models\GrupoAula;
use App\Models\Matricula;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\Models\Inscripciones;
use App\Models\CargaAcademica;
use App\Models\PlantillaHorario;
use App\Models\AsistenciaDocente;
use App\Models\InscripcionDocente;
use Illuminate\Support\Facades\DB;
use App\Models\BuDocenteEstudiante;
use App\Models\CalificacionDocente;
use App\Http\Controllers\Controller;
use App\Models\AsistenciaEstudiante;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
// -----------------reportes Spreadsheet----------------------------------
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\CalificacionDocenteDetalle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\AsistenciaEstudianteDetalle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    public function rptEstudiantesIndex()
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
        return view('intranet.reporte.estudiante', $response);
    }
    public function rptEstudianteLista(Request $request)
    {
        // dd($request);
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id', 'correlativo', 'estado', 'estudiantes_id', 'areas_id', 'sedes_id', 'periodos_id', 'turnos_id'], ['estudiante', 'area', 'sede', 'periodo', 'turno']);
        $data = $data->select(
            'inscripciones.*',
            DB::raw('(SELECT SUM(ip.monto) from inscripcion_pagos as ip where ip.inscripciones_id = inscripciones.id group by inscripciones.id) as monto'),
            DB::raw('(SELECT COUNT(*) from asistencia_estudiante_detalles as aed where aed.estudiantes_id = inscripciones.estudiantes_id and aed.estado != "3") as asistencia')
        );
        $data = $data->orderBy('id', 'desc');

        if (isset($request->sede)) {
            $data = $data->where('inscripciones.sedes_id', $request->sede);
        }
        if (isset($request->area)) {
            $data = $data->where('inscripciones.areas_id', $request->area);
        }
        if (isset($request->turno)) {
            $data = $data->where('inscripciones.turnos_id', $request->turno);
        }
        if (isset($request->tipo_descuento)) {
            $data = $data->where('inscripciones.tipo_estudiante', $request->tipo_descuento);
        }
        if (isset($request->estado)) {
            $data = $data->where('inscripciones.estado', $request->estado);
        }

        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
        // $response = $table->finish($data);
        return response()->json($response);
    }

    public function rptDocenteIndex()
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
        return view('intranet.reporte.docente', $response);
    }
    public function rptDocenteLista(Request $request)
    {
        // return view('intranet.reporte.docente');
        $table = new EloquentVueTables;
        $data = $table->get(new InscripcionDocente, ['id', 'apto', 'docentes_id'], ['docente', 'programa', 'grado']);
        $data = $data->select(
            "inscripcion_docentes.*",
            DB::raw("(SELECT SUM(ad.horas_pago) FROM asistencia_docentes AS ad WHERE ad.docentes_id = inscripcion_docentes.docentes_id AND ad.estado !='3' GROUP BY ad.docentes_id) AS horas")
        );
        $data = $data->where("apto", "1");

        if (isset($request->condicion)) {
            $data = $data->whereHas('docente', function (Builder $query) use ($request) {
                $query->where('condicion', $request->condicion);
            });
        }
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
        return response()->json($response);
    }
    public function rptAsistenciaDocenteIndex()
    {
        // $query = AsistenciaDocente::select("asistencia_docentes.carga_academicas_id","asistencia_docentes.docentes_id")->groupBy("asistencia_docentes.carga_academicas_id","asistencia_docentes.docentes_id")->get();
        // dd($query);
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
        return view('intranet.reporte.asistencia-docente', $response);
    }
    public function rptAsistenciaDocenteLista(Request $request)
    {
        // return view('intranet.reporte.docente');
        // $docente = ['docente' => function($qs){
        //     $qs->orderBy('paterno', 'DESC')}
        // ];

        $table = new EloquentVueTables;
        $data = $table->get(new AsistenciaDocente, ['id', 'docentes_id', 'carga_academicas_id'], ['carga', 'docente', 'docenteApto']);

        if (isset($request->fecha_ini)) {
            $data = $data->select(
                "asistencia_docentes.*",
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='1'
                            AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                            GROUP BY ad.docentes_id
                        ) AS horas_presente"),
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='2'
                            AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                            GROUP BY ad.docentes_id
                        ) AS horas_tarde"),
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='3'
                            AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                            GROUP BY ad.docentes_id
                        ) AS horas_falta"),
                DB::raw("(SELECT GROUP_CONCAT(CONCAT('[',ad.fecha,'] ',ad.observacion) SEPARATOR  ' | ')
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                            GROUP BY ad.docentes_id
                        ) AS observacion")
            );
            $data = $data->whereBetween('asistencia_docentes.fecha', [$request->fecha_ini, $request->fecha_fin]);
        } else {
            $data = $data->select(
                "asistencia_docentes.*",
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='1' GROUP BY ad.docentes_id
                        ) AS horas_presente"),
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='2' GROUP BY ad.docentes_id
                        ) AS horas_tarde"),
                DB::raw("(SELECT SUM(ad.horas_pago)
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            AND ad.estado ='3' GROUP BY ad.docentes_id
                        ) AS horas_falta"),
                DB::raw("(SELECT GROUP_CONCAT(CONCAT('[',ad.fecha,'] ',ad.observacion) SEPARATOR  ' | ')
                        FROM asistencia_docentes AS ad
                        WHERE
                            ad.docentes_id = asistencia_docentes.docentes_id
                            AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                            GROUP BY ad.docentes_id
                        ) AS observacion")
            );
        }
        $data = $data->join("docentes", "docentes.id", "asistencia_docentes.docentes_id");
        $data = $data->groupBy("asistencia_docentes.docentes_id", "asistencia_docentes.carga_academicas_id");
        // $data = $data->whereHas('docente', function (Builder $q) {
        //     $q->orderBy('paterno','asc');
        // });
        // $data = $data->groupBy("asistencia_docentes.docentes_id");
        $data = $data->orderBy("docentes.paterno", "asc")->orderBy("docentes.materno", "asc")->orderBy("docentes.nombres", "asc");
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }

        return response()->json($response);
    }
    public function rptAsistenciaDocentePdf(Request $request)
    {
        // dd($request);

        if (isset($request->fecha_ini)) {
            $data = AsistenciaDocente::with(['carga', 'docente', 'docenteApto'])
                ->select(
                    "asistencia_docentes.*",
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='1'
                                AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                                GROUP BY ad.docentes_id
                            ) AS horas_presente"),
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='2'
                                AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                                GROUP BY ad.docentes_id
                            ) AS horas_tarde"),
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='3'
                                AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                                GROUP BY ad.docentes_id
                            ) AS horas_falta"),
                    DB::raw("(SELECT GROUP_CONCAT(CONCAT('[',ad.fecha,'] ',ad.observacion) SEPARATOR  ' | ')
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.fecha BETWEEN '" . $request->fecha_ini . "' AND '" . $request->fecha_fin . "'
                                GROUP BY ad.docentes_id
                            ) AS observacion")
                );
            $data = $data->whereBetween(
                'asistencia_docentes.fecha',
                [$request->fecha_ini, $request->fecha_fin]
            );
        } else {
            $data = AsistenciaDocente::with(['carga', 'docente', 'docenteApto'])
                ->select(
                    "asistencia_docentes.*",
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='1' GROUP BY ad.docentes_id
                            ) AS horas_presente"),
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='2' GROUP BY ad.docentes_id
                            ) AS horas_tarde"),
                    DB::raw("(SELECT SUM(ad.horas_pago)
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                AND ad.estado ='3' GROUP BY ad.docentes_id
                            ) AS horas_falta"),
                    DB::raw("(SELECT GROUP_CONCAT(CONCAT('[',ad.fecha,'] ',ad.observacion) SEPARATOR  ' | ')
                            FROM asistencia_docentes AS ad
                            WHERE
                                ad.docentes_id = asistencia_docentes.docentes_id
                                AND ad.carga_academicas_id = asistencia_docentes.carga_academicas_id
                                GROUP BY ad.docentes_id
                            ) AS observacion")
                );
        }
        $data = $data->join("docentes", "docentes.id", "asistencia_docentes.docentes_id");
        $data = $data->groupBy("asistencia_docentes.docentes_id", "asistencia_docentes.carga_academicas_id");
        $data = $data->orderBy("docentes.paterno", "asc")->orderBy("docentes.materno", "asc")->orderBy("docentes.nombres", "asc");
        $data = $data->get();

        // dd($data[0]->docenteApto);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        $pdf = new PDF();
        $pdf::SetMargins(10, 30, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 50, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            // $pdf->Image('images/logo.png', 220, 6, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 225, 6, 22, 22, 'PNG', '', '', false, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Asistencia');
        $pdf::AddPage('L');

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 10, 'ASISTENCIA DOCENTE - ' . date("d/m/Y", strtotime($request->fecha_ini)) . ' al ' . date("d/m/Y", strtotime($request->fecha_fin)), 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="35"  align="center">#</td>
                            <td width="50"  align="center">DNI</td>
                            <td width="170" align="center">Apellidos y Nombres</td>
                            <td width="50"  align="center">Celular</td>
                            <td width="70"  align="center">Email</td>
                            <td width="60"  align="center">Area</td>
                            <td width="40"  align="center">Grupo</td>
                            <td width="60"  align="center">Curso</td>
                            <td width="45"  align="center">Horas P.</td>
                            <td width="45"  align="center">Horas T.</td>
                            <td width="45"  align="center">Horas F.</td>
                            <td width="115"  align="center">Observación</td>
                        </tr>
                    </thead>';
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';

        foreach ($data as $key => $value) {
            $tabla .= '<tr>
                        <td width="35"  align="center">' . ($key + 1) . '</td>
                        <td width="50"  align="center">' . $value->docente->nro_documento . '</td>
                        <td width="170" align="center">' . $value->docente->paterno . ' ' . $value->docente->materno . ' ' . $value->docente->nombres . '</td>
                        <td width="50"  align="center">' . $value->docente->celular . '</td>
                        <td width="70"  align="center">' . $value->docenteApto->usuario . '</td>
                        <td width="60"  align="center">' . $value->carga->area->denominacion . '</td>
                        <td width="40"  align="center">' . $value->carga->grupo->denominacion . '</td>
                        <td width="60"  align="center">' . $value->carga->curso->denominacion . '</td>
                        <td width="45"  align="center">' . $value->horas_presente . '</td>
                        <td width="45"  align="center">' . $value->horas_tarde . '</td>
                        <td width="45"  align="center">' . $value->horas_falta . '</td>
                        <td width="115"  align="center">' . $value->observacion . '</td>
                    </tr>';
        }

        $tabla .= '</tbody></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('AsistenciaEstudiante.pdf', 'I');
    }
    public function rptDocenteCalificacionIndex()
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
        return view("intranet.reporte.docente-calificacion", $response);
    }
    public function rptDocenteCalificacionLista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new CalificacionDocente,
            [
                'id',
                'participantes',
                'promedio',
                'docentes_id',
                'carga_academicas_id',
                'asistencia_docentes_id',
            ],
            ['docente', 'asistenciaDocente', 'curso']
        );
        $data = $data->orderBy('calificacion_docentes.id', 'desc');
        $response = $table->finish($data);

        return response()->json($response);
    }
    public function rptDocenteCalificacionDetalle($id)
    {

        $getCriterio = CalificacionDocenteDetalle::with("criterio")->where("calificacion_docentes_id", $id)->orderBy("criterios_id", "asc")->groupBy("criterios_id")->get();
        // return $getCriterio[0]->criterio->denominacion;
        $calificacion = CalificacionDocente::with(['docente', 'asistenciaDocente', 'curso'])->find($id);
        $listaCriterios = [];
        $criterio = 0;
        $total = 0;
        foreach ($getCriterio as $key => $value) {

            $total = 0;
            $detalles = CalificacionDocenteDetalle::where([["calificacion_docentes_id", $id], ["criterios_id", $value->criterios_id]])->get();
            foreach ($detalles as $k => $val) {
                # code...
                $total += $val->puntaje;
            }
            $objeto = new \stdClass;
            $objeto->promedio = round($total / $calificacion->participantes, 2);
            $objeto->nombre = $value->criterio->denominacion;
            $listaCriterios[] = $objeto;
        }
        $response["criterios"] = $listaCriterios;
        $response["calificacion"] = $calificacion;
        return $response;
    }
    public function rptEstudianteAsistenciaPdf($id)
    {
        // dd($id);
        $estudiantes = AsistenciaEstudianteDetalle::with("estudiante")
            // ->whereHas('estudiante', function (Builder $query) {
            //     $query->orderBy('paterno',"asc");
            // })
            ->select("asistencia_estudiante_detalles.*")
            ->join("estudiantes as e", "e.id", "asistencia_estudiante_detalles.estudiantes_id")
            ->where([
                ["asistencia_estudiantes_id", $id]
            ])
            ->orderBy('e.paterno', "asc")
            ->orderBy('e.materno', "asc")
            ->orderBy('e.nombres', "asc")
            ->get();
        $asistencia = AsistenciaEstudiante::with("grupo")->find($id);
        $usuario = User::find($asistencia->users_id);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        $fecha = date("d/m/Y", strtotime($asistencia->fecha));
        $hora = date("H:i:s", strtotime($asistencia->created_at));
        // dd($estudiantes);
        $periodo = Periodo::where("estado", "1")->first();
        $pdf = new PDF();
        $pdf::SetMargins(10, 35, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 10, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 170, 6, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Asistencia Estudiante');
        $pdf::AddPage();

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 10, 'LISTA DE ASISTENCIA - ' . $asistencia->grupo->denominacion . ' - ' . $fecha, 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="20"  align="center">#</td>
                            <td width="50"  align="center">DNI</td>
                            <td width="170" align="center">Apellidos y Nombres</td>
                            <td width="50"  align="center">Fecha</td>
                            <td width="50"  align="center">Estado</td>
                            <td width="150"  align="center">Usuario</td>
                            <td width="50"  align="center">Hora</td>
                        </tr>
                    </thead>';
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';

        foreach ($estudiantes as $key => $value) {
            $estado = "Presente";
            switch ($value->estado) {
                case '1':
                    $estado = "Presente";
                    break;
                case '2':
                    $estado = "Tarde";
                    break;
                case '3':
                    $estado = "Falta";
                    break;

                default:
                    # code...
                    break;
            }
            $tabla .= '<tr>
                            <td width="20"  align="center">' . ($key + 1) . '</td>
                            <td width="50"  align="center">' . $value->estudiante->nro_documento . '</td>
                            <td width="170" align="left">' . $value->estudiante->paterno . ' ' . $value->estudiante->materno . ' ' . $value->estudiante->nombres . '</td>
                            <td width="50"  align="center">' . $fecha . '</td>
                            <td width="50"  align="center">' . $estado . '</td>
                            <td width="150"  align="center">' . $usuario->paterno . ' ' . $usuario->materno . ' ' . $usuario->name . '</td>
                            <td width="50"  align="center">' . $hora . '</td>
                        </tr>';
        }

        $tabla .= '</tbody></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('AsistenciaEstudiante.pdf', 'I');
    }
    public function rpDocenteAsistenciaPdf($grupo, $fecha, Request $request)
    {
        // dd($request);
        $asistencia = AsistenciaDocente::with(["docente", "carga", "sesiones", "user"])
            ->where("fecha", $fecha)
            ->whereHas('carga', function (Builder $query) use ($grupo) {
                $query->where('grupo_aulas_id', $grupo);
                // ->where("estado", "1");
            })
            ->get();
        // dd($asistencia);
        // $usuario = User::find($asistencia->users_id);
        $grupoAula = GrupoAula::with("grupo")->find($grupo);
        // dd($grupoAula);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        $fecha = date("d/m/Y", strtotime($fecha));
        // $hora = date("H:i:s", strtotime($asistencia->created_at));
        // dd($estudiantes);
        $periodo = Periodo::where("estado", "1")->first();
        $pdf = new PDF();
        $pdf::SetMargins(10, 35, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 50, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            // $pdf->Image('images/logo.png', 220, 6, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 225, 6, 22, 22, 'PNG', '', '', false, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Asistencia Estudiante');
        $pdf::AddPage('L');

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 10, 'ASISTENCIA DOCENTE - ' . $grupoAula->grupo->denominacion . ' - ' . $fecha, 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="35"  align="center">#</td>
                            <td width="50"  align="center">DNI</td>
                            <td width="150" align="center">Apellidos y Nombres</td>
                            <td width="60"  align="center">Curso</td>
                            <td width="50"  align="center">Estado</td>
                            <td width="140"  align="center">Tema</td>
                            <td width="80"  align="center">Hora y Fecha</td>
                            <td width="90"  align="center">Usuario</td>
                            <td width="30"  align="center">Hrs</td>';
        if (isset($request->firma)) {
            $tabla .=       '<td width="100"  align="center">Firma</td>';
        } else {
            $tabla .=       '<td width="100"  align="center">Observación</td>';
        }
        $tabla .=        '</tr>
                    </thead>';
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';
        foreach ($asistencia as $key => $value) {
            $estado = "Presente";
            switch ($value->estado) {
                case '1':
                    $estado = "Presente";
                    break;
                case '2':
                    $estado = "Tarde";
                    break;
                case '3':
                    $estado = "Falta";
                    break;

                default:
                    # code...
                    break;
            }
            $tabla .= '<tr>
                        <td width="35"  align="center">' . ($key + 1) . '</td>
                        <td width="50"  align="center">' . $value->docente->nro_documento . '</td>
                        <td width="150" align="center">' . $value->docente->paterno . ' ' . $value->docente->materno . ' ' . $value->docente->nombres . '</td>
                        <td width="60"  align="center">' . $value->carga->curso->denominacion . '</td>
                        <td width="50"  align="center">' . $estado . '</td>
                        <td width="140"  align="center">' . (isset($value->sesiones->tema) ? $value->sesiones->tema : "") . '</td>
                        <td width="80"  align="center">' . date("d/m/Y h:i a", strtotime($value->created_at)) . '</td>
                        <td width="90"  align="center">' . $value->user->paterno . ' ' . $value->user->materno . ' ' . $value->user->name . '</td>
                        <td width="30"  align="center">' . $value->horas_pago . '</td>';
            if (isset($request->firma)) {
                $tabla .=       '<td width="100"  align="center"></td>';
            } else {
                $tabla .=       '<td width="100"  align="center">' . $value->observacion . '</td>';
            }
            $tabla .= '</tr>';
        }
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';
        $tabla .= '</tbody></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('AsistenciaDocente.pdf', 'D');
    }
    public function rpDocentePartePdf(Request $request)
    {
        // dd($request);
        $grupo = $request->grupo;
        $fecha = $request->fecha;
        $fecha = new \DateTime($fecha);
        $semana = $fecha->format("N");
        $dias = ["Lunes", "Martes", "Miercoles", "Jueves", "Viernes"];

        $dia = $dias[(int)($semana - 1)];
        $fecha = $fecha->format("d/m/Y");


        $grupoAula = GrupoAula::with(["grupo", "aula", "area", "turno"])->find($grupo);
        // dd($grupoAula);
        $response["turno"] = Turno::find($grupoAula->turnos_id);
        $plantillaHorario = [];
        $plantilla = PlantillaHorario::select(
            "id",
            DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
            DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
            "tipo"
        )
            ->where("turnos_id", $grupoAula->turnos_id)
            // ->where("dia",$semana)
            ->where("estado", "1")
            ->get();

        foreach ($plantilla as $k => $val) {
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->hora_inicio = $val->horaInicio;
            $obj->hora_fin = $val->horaFin;
            $obj->tipo = $val->tipo;
            $obj->horario = Horario::with(['curso', 'carga'])
                ->select("horarios.*")
                ->whereHas('carga', function (Builder $query) use ($grupo) {
                    $query->where('grupo_aulas_id', $grupo)
                        ->where("estado", "1");
                })
                // ->where("carga_academicas_id",$inscripcionDocente->id)
                ->where("plantilla_horarios_id", $val->id)
                ->where("dia", $semana)
                ->orderBy("dia", "asc")
                ->first();
            $plantillaHorario[] = $obj;
        }
        // $response["horario"] = $plantillaHorario;
        // dd($response);

        // $asistencia = AsistenciaDocente::with(["docente", "carga", "sesiones", "user"])
        //     ->where("fecha", $fecha)
        //     ->whereHas('carga', function (Builder $query) use ($grupo) {
        //         $query->where('grupo_aulas_id', $grupo);
        //         // ->where("estado", "1");
        //     })
        //     ->get();
        // dd($asistencia);
        // $usuario = User::find($asistencia->users_id);
        // $grupoAula = GrupoAula::with("grupo")->find($grupo);
        // dd($grupoAula);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        // $fecha = date("d/m/Y", strtotime($fecha));
        // $hora = date("H:i:s", strtotime($asistencia->created_at));
        // dd($estudiantes);
        $periodo = Periodo::where("estado", "1")->first();
        $pdf = new PDF();
        $pdf::SetMargins(10, 35, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 50, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 225, 6, 22, 22, 'PNG', '', '', false, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Asistencia Estudiante');
        $pdf::AddPage('L');

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 10, 'PARTE DOCENTES', 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        // *******************
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'GRUPO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 6, $grupoAula->grupo->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'SEDE:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 6, $grupoAula->aula->local->sede->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'AREA:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 6,  $grupoAula->area->denominacion, 0, 1, 'L', 0, '', 1);
        // *******************
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'AUXILIAR:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(60, 6, $user, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'TURNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(30, 6, $grupoAula->turno->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'LOCAL:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(50, 6, $grupoAula->aula->local->direccion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(20, 6, 'FECHA:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(40, 6,  $fecha, 0, 1, 'L', 0, '', 1);
        // *******************
        $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="3" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="45"  align="center">HORA</td>
                            <td width="60"  align="center">DNI</td>
                            <td width="170" align="center">APELLIDOS Y NOMBRES</td>
                            <td width="90"  align="center">CURSO</td>
                            <td width="140"  align="center">TEMA</td>
                            <td width="50"  align="center">HORA DE ENTRADA</td>
                            <td width="90"  align="center">FIRMA</td>
                            <td width="50"  align="center">HORA DE SALIDA</td>
                            <td width="90"  align="center">FIRMA</td>
                            </tr>
                    </thead></table>';
        // $pdf::SetFont('helvetica', '', 10);
        // dd($plantillaHorario);
        $tabla .= '<table cellspacing="0" cellpadding="6" border="1"><tbody>';
        $temp = 0;
        $hora_ini = "";
        $hora_fin = "";
        $dni = "";
        $nombre = "";
        $curso = "";
        $i = 0;
        // dd($plantillaHorario);
        foreach ($plantillaHorario as $key => $value) {
            if ($value->horario != null) {
                // echo $key."<br>";
                if ($i == 0) {
                    $hora_ini = $value->hora_inicio;
                }
                if (($key + 1) != count($plantillaHorario)) {
                    if (isset($plantillaHorario[$key + 1]->horario->carga->id) && $value->horario->carga->id == $plantillaHorario[$key + 1]->horario->carga->id) {
                        // $hora_ini = $value->hora_inicio;
                        $i = 1;
                    } else {
                        // $hora_ini = $value->hora_inicio;
                        $hora_fin = $value->hora_fin;
                        $dni = $value->horario->carga->docente->nro_documento;
                        $nombre = $value->horario->carga->docente->paterno . ' ' . $value->horario->carga->docente->materno . ' ' . $value->horario->carga->docente->nombres;
                        $curso = $value->horario->carga->curso->denominacion;
                        $tabla .= '<tr>
                                    <td width="45"  align="center">' . $hora_ini . ' ' . $hora_fin . '</td>
                                    <td width="60"  align="center">' . $dni . '</td>
                                    <td width="170" align="left">' . $nombre . '</td>
                                    <td width="90"  align="center">' . $curso . '</td>
                                    <td width="140"  align="center"></td>
                                    <td width="50"  align="center"></td>
                                    <td width="90"  align="center"></td>
                                    <td width="50"  align="center"></td>
                                    <td width="90"  align="center"></td>
                                    </tr>';
                        $i = 0;
                    }
                } else {
                    $hora_fin = $value->hora_fin;
                    $dni = $value->horario->carga->docente->nro_documento;
                    $nombre = $value->horario->carga->docente->paterno . ' ' . $value->horario->carga->docente->materno . ' ' . $value->horario->carga->docente->nombres;
                    $curso = $value->horario->carga->curso->denominacion;
                    $tabla .= '<tr>
                                <td width="45"  align="center">' . $hora_ini . ' ' . $hora_fin . '</td>
                                <td width="60"  align="center">' . $dni . '</td>
                                <td width="170" align="left">' . $nombre . '</td>
                                <td width="90"  align="center">' . $curso . '</td>
                                <td width="140"  align="center"></td>
                                <td width="50"  align="center"></td>
                                <td width="90"  align="center"></td>
                                <td width="50"  align="center"></td>
                                <td width="90"  align="center"></td>
                                </tr>';
                    $i = 0;
                }
            }
        }
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';
        $tabla .= '</tbody></table>';
        $tabla .= '<table cellpadding="4">
                    <tr><td>REEMPLAZO</td></tr>
                    </table>';
        $tabla .= '<table cellspacing="0" cellpadding="8" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="45"  align="center"></td>
                            <td width="60"  align="center"></td>
                            <td width="170" align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="140"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td width="45"  align="center"></td>
                            <td width="60"  align="center"></td>
                            <td width="170" align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="140"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                        </tr>
                        <tr style="font-weight: bold;">
                            <td width="45"  align="center"></td>
                            <td width="60"  align="center"></td>
                            <td width="170" align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="140"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                            <td width="50"  align="center"></td>
                            <td width="90"  align="center"></td>
                        </tr>
                    </thead></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        // $pdf::SetFont('helvetica', 'b', 10);
        // $pdf::Cell(20, 6, 'REEMPLAZO', 0, 1, 'L', 0, '', 1);

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output($grupoAula->aula->local->sede->denominacion . '_' . $grupoAula->grupo->denominacion . '.pdf', 'D');
    }
    public function rptPagosIndex()
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
        return view("intranet.reporte.pagos", $response);
    }

    public function rptPagosLista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id'], []);
        $data = $data->select(
            "inscripciones.id",
            "estudiantes.nro_documento",
            "estudiantes.paterno",
            "estudiantes.materno",
            "estudiantes.nombres",
            "areas.denominacion AS area",
            "turnos.denominacion AS turno",
            "grupos.denominacion AS grupo",
            DB::raw("if(inscripciones.tipo_estudiante = '1','Normal',if(inscripciones.tipo_estudiante = '2','Hijo de trabajador',if(inscripciones.tipo_estudiante = '3','Descuento Trabajador UNA',
            if(inscripciones.tipo_estudiante = '4','Hermanos',if(inscripciones.tipo_estudiante = '5','Resolución Rectoral','Servicio Militar'))))) AS descuento"),
            "tipo_colegios.denominacion AS tipo_colegio",
            "inscripciones.estado",
            DB::raw("SUM(tarifa_estudiantes.monto) as pago_total"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes0.monto,'|',tarifa_estudiantes0.pagado,'|',tarifa_estudiantes0.mora)
            FROM tarifa_estudiantes AS tarifa_estudiantes0
            WHERE tarifa_estudiantes0.nro_cuota = 0 AND tarifa_estudiantes0.estudiantes_id = estudiantes.id) AS pago_matricula"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes1.monto,'|',tarifa_estudiantes1.pagado,'|',tarifa_estudiantes1.mora)
            FROM tarifa_estudiantes AS tarifa_estudiantes1
            WHERE tarifa_estudiantes1.nro_cuota = 1 AND tarifa_estudiantes1.estudiantes_id = estudiantes.id) AS primera_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes2.monto,'|',tarifa_estudiantes2.pagado,'|',tarifa_estudiantes2.mora)
            FROM tarifa_estudiantes AS tarifa_estudiantes2
            WHERE tarifa_estudiantes2.nro_cuota = 2 AND tarifa_estudiantes2.estudiantes_id = estudiantes.id) AS segunda_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes3.monto,'|',tarifa_estudiantes3.pagado,'|',tarifa_estudiantes3.mora)
            FROM tarifa_estudiantes AS tarifa_estudiantes3
            WHERE tarifa_estudiantes3.nro_cuota = 3 AND tarifa_estudiantes3.estudiantes_id = estudiantes.id) AS tercera_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes4.monto,'|',tarifa_estudiantes4.pagado,'|',tarifa_estudiantes4.mora)
            FROM tarifa_estudiantes AS tarifa_estudiantes4
            WHERE tarifa_estudiantes4.nro_cuota = 4 AND tarifa_estudiantes4.estudiantes_id = estudiantes.id) AS cuarta_mensualidad")
        );

        $data = $data->join("estudiantes", "estudiantes.id", "inscripciones.estudiantes_id");
        $data = $data->leftJoin("tarifa_estudiantes", "tarifa_estudiantes.estudiantes_id", "estudiantes.id");
        $data = $data->join("sedes as s", "s.id", "inscripciones.sedes_id");
        $data = $data->leftJoin("matriculas", "matriculas.estudiantes_id", "estudiantes.id");
        $data = $data->leftJoin("grupo_aulas", "grupo_aulas.id", "matriculas.grupo_aulas_id");
        $data = $data->leftJoin("areas", "areas.id", "grupo_aulas.areas_id");
        $data = $data->leftJoin("grupos", "grupos.id", "grupo_aulas.grupos_id");
        $data = $data->leftJoin("turnos", "turnos.id", "grupo_aulas.turnos_id");
        $data = $data->join("colegios", "colegios.id", "estudiantes.colegios_id");
        $data = $data->join("tipo_colegios", "tipo_colegios.id", "colegios.tipo_colegios_id");

        if (isset($request->cuota1)) {
            if ($request->cuota1 == "0") {
                $data = $data->where(DB::raw("(select tw1.monto - tw1.pagado from tarifa_estudiantes as tw1 where tw1.estudiantes_id = estudiantes.id and tw1.nro_cuota = 1)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw1.monto - tw1.pagado from tarifa_estudiantes as tw1 where tw1.estudiantes_id = estudiantes.id and tw1.nro_cuota = 1)"), "!=", "0");
            }
        }
        if (isset($request->cuota2)) {
            if ($request->cuota2 == "0") {
                $data = $data->where(DB::raw("(select tw2.monto - tw2.pagado from tarifa_estudiantes as tw2 where tw2.estudiantes_id = estudiantes.id and tw2.nro_cuota = 2)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw2.monto - tw2.pagado from tarifa_estudiantes as tw2 where tw2.estudiantes_id = estudiantes.id and tw2.nro_cuota = 2)"), "!=", "0");
            }
        }
        if (isset($request->cuota3)) {
            if ($request->cuota3 == "0") {
                $data = $data->where(DB::raw("(select tw3.monto - tw3.pagado from tarifa_estudiantes as tw3 where tw3.estudiantes_id = estudiantes.id and tw3.nro_cuota = 3)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw3.monto - tw3.pagado from tarifa_estudiantes as tw3 where tw3.estudiantes_id = estudiantes.id and tw3.nro_cuota = 3)"), "!=", "0");
            }
        }
        if (isset($request->cuota4)) {
            if ($request->cuota4 == "0") {
                $data = $data->where(DB::raw("(select tw4.monto - tw4.pagado from tarifa_estudiantes as tw4 where tw4.estudiantes_id = estudiantes.id and tw4.nro_cuota = 4)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw4.monto - tw4.pagado from tarifa_estudiantes as tw4 where tw4.estudiantes_id = estudiantes.id and tw4.nro_cuota = 4)"), "!=", "0");
            }
        }

        $data = $data->groupBy("estudiantes.id");
        if (isset($request->estado)) {
            $data = $data->where('inscripciones.estado', $request->estado);
        }

        $data = $data->orderBy("estudiantes.paterno", "asc")->orderBy("estudiantes.materno", "asc")->orderBy("estudiantes.nombres", "asc");

        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
        // $response = $table->finish($data);
        return response()->json($response);
    }
    public function rptPagosListaExcel(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id'], []);
        $data = $data->select(

            "inscripciones.id",
            "estudiantes.nro_documento",
            "estudiantes.paterno",
            "estudiantes.materno",
            "estudiantes.nombres",
            "estudiantes.celular",
            "areas.denominacion AS area",
            "turnos.denominacion AS turno",
            "grupos.denominacion AS grupo",
            "s.denominacion as sede",
            DB::raw('(SELECT COUNT(*) from asistencia_estudiante_detalles as aed
                 where aed.estudiantes_id = estudiantes.id and aed.estado != "3") as asistencia'),
            DB::raw("if(inscripciones.tipo_estudiante = '1','Normal',if(inscripciones.tipo_estudiante = '2','Hijo de trabajador',if(inscripciones.tipo_estudiante = '3','Descuento Trabajador UNA',
                if(inscripciones.tipo_estudiante = '4','Hermanos',if(inscripciones.tipo_estudiante = '5','Resolución Rectoral','Servicio Militar'))))) AS descuento"),
            "tipo_colegios.denominacion AS tipo_colegio",
            "inscripciones.estado",
            DB::raw("SUM(tarifa_estudiantes.monto) as pago_total"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes0.monto,'|',tarifa_estudiantes0.pagado,'|',tarifa_estudiantes0.mora)
                FROM tarifa_estudiantes AS tarifa_estudiantes0
                WHERE tarifa_estudiantes0.nro_cuota = 0 AND tarifa_estudiantes0.estudiantes_id = estudiantes.id) AS pago_matricula"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes1.monto,'|',tarifa_estudiantes1.pagado,'|',tarifa_estudiantes1.mora)
                FROM tarifa_estudiantes AS tarifa_estudiantes1
                WHERE tarifa_estudiantes1.nro_cuota = 1 AND tarifa_estudiantes1.estudiantes_id = estudiantes.id) AS primera_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes2.monto,'|',tarifa_estudiantes2.pagado,'|',tarifa_estudiantes2.mora)
                FROM tarifa_estudiantes AS tarifa_estudiantes2
                WHERE tarifa_estudiantes2.nro_cuota = 2 AND tarifa_estudiantes2.estudiantes_id = estudiantes.id) AS segunda_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes3.monto,'|',tarifa_estudiantes3.pagado,'|',tarifa_estudiantes3.mora)
                FROM tarifa_estudiantes AS tarifa_estudiantes3
                WHERE tarifa_estudiantes3.nro_cuota = 3 AND tarifa_estudiantes3.estudiantes_id = estudiantes.id) AS tercera_mensualidad"),

            DB::raw("(SELECT CONCAT(tarifa_estudiantes4.monto,'|',tarifa_estudiantes4.pagado,'|',tarifa_estudiantes4.mora)
                FROM tarifa_estudiantes AS tarifa_estudiantes4
                WHERE tarifa_estudiantes4.nro_cuota = 4 AND tarifa_estudiantes4.estudiantes_id = estudiantes.id) AS cuarta_mensualidad")

            // DB::raw("(SELECT SUM(monto)
            //     FROM inscripcion_pagos ip
            //     WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2'
            //     GROUP BY inscripciones_id) AS mensualidad_total"),

            // DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1),
            //     SUM(monto),if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
            //     FROM inscripcion_pagos ip
            //     WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2'
            //     GROUP BY inscripciones_id) AS primera_mensualidad"),

            // DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2) ,
            //     if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1) , 0.0000,
            //     ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1))),
            //     if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
            //     FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS segunda_mensualidad"),

            // DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3) ,
            //     if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2) , 0.0000,
            //     ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2))),
            //     if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
            //     FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS tercera_mensualidad"),

            // DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*4, if(tipo_colegios.id = 1,250,200)*4) ,
            //     if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3) , 0.0000,
            //     ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3))),
            //     if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
            //     FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS cuarta_mensualidad"),
            // DB::raw("(SELECT SUM(monto) FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '3' GROUP BY inscripciones_id) AS moras")
        );

        $data = $data->join("estudiantes", "estudiantes.id", "inscripciones.estudiantes_id");
        $data = $data->leftJoin("tarifa_estudiantes", "tarifa_estudiantes.estudiantes_id", "estudiantes.id");
        $data = $data->join("sedes as s", "s.id", "inscripciones.sedes_id");
        $data = $data->leftJoin("matriculas", "matriculas.estudiantes_id", "estudiantes.id");
        $data = $data->leftJoin("grupo_aulas", "grupo_aulas.id", "matriculas.grupo_aulas_id");
        $data = $data->leftJoin("areas", "areas.id", "grupo_aulas.areas_id");
        $data = $data->leftJoin("grupos", "grupos.id", "grupo_aulas.grupos_id");
        $data = $data->leftJoin("turnos", "turnos.id", "grupo_aulas.turnos_id");
        $data = $data->join("colegios", "colegios.id", "estudiantes.colegios_id");
        $data = $data->join("tipo_colegios", "tipo_colegios.id", "colegios.tipo_colegios_id");

        // $data = $data->groupBy("asistencia_docentes.docentes_id", "asistencia_docentes.carga_academicas_id");
        // $data = $data->whereHas('docente', function (Builder $q) {
        //     $q->orderBy('paterno','asc');
        // });
        // $data = $data->groupBy("asistencia_docentes.docentes_id");
        if (isset($request->estado)) {
            $data = $data->where('inscripciones.estado', $request->estado);
        }
        if (isset($request->cuota1)) {
            if ($request->cuota1 == "0") {
                $data = $data->where(DB::raw("(select tw1.monto - tw1.pagado from tarifa_estudiantes as tw1 where tw1.estudiantes_id = estudiantes.id and tw1.nro_cuota = 1)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw1.monto - tw1.pagado from tarifa_estudiantes as tw1 where tw1.estudiantes_id = estudiantes.id and tw1.nro_cuota = 1)"), "!=", "0");
            }
        }
        if (isset($request->cuota2)) {
            if ($request->cuota2 == "0") {
                $data = $data->where(DB::raw("(select tw2.monto - tw2.pagado from tarifa_estudiantes as tw2 where tw2.estudiantes_id = estudiantes.id and tw2.nro_cuota = 2)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw2.monto - tw2.pagado from tarifa_estudiantes as tw2 where tw2.estudiantes_id = estudiantes.id and tw2.nro_cuota = 2)"), "!=", "0");
            }
        }
        if (isset($request->cuota3)) {
            if ($request->cuota3 == "0") {
                $data = $data->where(DB::raw("(select tw3.monto - tw3.pagado from tarifa_estudiantes as tw3 where tw3.estudiantes_id = estudiantes.id and tw3.nro_cuota = 3)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw3.monto - tw3.pagado from tarifa_estudiantes as tw3 where tw3.estudiantes_id = estudiantes.id and tw3.nro_cuota = 3)"), "!=", "0");
            }
        }
        if (isset($request->cuota4)) {
            if ($request->cuota4 == "0") {
                $data = $data->where(DB::raw("(select tw4.monto - tw4.pagado from tarifa_estudiantes as tw4 where tw4.estudiantes_id = estudiantes.id and tw4.nro_cuota = 4)"), "0");
            } else {
                $data = $data->where(DB::raw("(select tw4.monto - tw4.pagado from tarifa_estudiantes as tw4 where tw4.estudiantes_id = estudiantes.id and tw4.nro_cuota = 4)"), "!=", "0");
            }
        }
        // $data = $data->where(DB::raw("select tw1.monto - tw1.pagado from tarifa_estudiantes as tw1 where tw1.estudiantes_id = estudiantes.id and tw1.nro_cuota = 1"),$);

        // $data = $data->groupBy("asistencia_docentes.docentes_id", "asistencia_docentes.carga_academicas_id");
        // $data = $data->whereHas('docente', function (Builder $q) {
        //     $q->orderBy('paterno','asc');
        // });
        $data = $data->groupBy("estudiantes.id");
        // $data = $data->orderBy("estudiantes.paterno", "asc")->orderBy("estudiantes.materno", "asc")->orderBy("estudiantes.nombres", "asc");
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
        // $response = $table->finish($data);
        // dd($response);
        return response()->json($response);
    }

    public function rptPagosPdf()
    {
        $data = Inscripciones::select(
            "inscripciones.id",
            "estudiantes.nro_documento",
            "estudiantes.paterno",
            "estudiantes.materno",
            "estudiantes.nombres",
            "areas.denominacion AS area",
            "turnos.denominacion AS turno",
            "grupos.denominacion AS grupo",
            DB::raw("if(inscripciones.tipo_estudiante = '1','Normal',if(inscripciones.tipo_estudiante = '2','Hijo de trabajador',if(inscripciones.tipo_estudiante = '3','Descuento Trabajador UNA',
            if(inscripciones.tipo_estudiante = '4','Hermanos',if(inscripciones.tipo_estudiante = '5','Resolución Rectoral','Servicio Militar'))))) AS descuento"),
            "tipo_colegios.denominacion AS tipo_colegio",

            DB::raw("(SELECT SUM(monto)
                FROM inscripcion_pagos ip
                WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '1'
                GROUP BY inscripciones_id) AS pago_matricula"),

            DB::raw("(SELECT SUM(monto)
                FROM inscripcion_pagos ip
                WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2'
                GROUP BY inscripciones_id) AS mensualidad_total"),

            DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1),
                SUM(monto),if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
                FROM inscripcion_pagos ip
                WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2'
                GROUP BY inscripciones_id) AS primera_mensualidad"),

            DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2) ,
                if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1) , 0.0000,
                ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*1, if(tipo_colegios.id = 1,250,200)*1))),
                if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
                FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS segunda_mensualidad"),

            DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3) ,
                if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2) , 0.0000,
                ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*2, if(tipo_colegios.id = 1,250,200)*2))),
                if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
                FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS tercera_mensualidad"),

            DB::raw("(SELECT if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*4, if(tipo_colegios.id = 1,250,200)*4) ,
                if(SUM(monto) < if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3) , 0.0000,
                ABS(SUM(monto) - if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2)*3, if(tipo_colegios.id = 1,250,200)*3))),
                if(inscripciones.tipo_estudiante != '1',if(tipo_colegios.id = 1,250/2,200/2), if(tipo_colegios.id = 1,250,200)))
                FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '2' GROUP BY inscripciones_id) AS cuarta_mensualidad"),

            DB::raw("(SELECT SUM(monto) FROM inscripcion_pagos ip WHERE ip.inscripciones_id = inscripciones.id AND ip.concepto_pagos_id = '3' GROUP BY inscripciones_id) AS moras")
        );

        $data = $data->join("estudiantes", "estudiantes.id", "inscripciones.estudiantes_id");
        $data = $data->join("matriculas", "matriculas.estudiantes_id", "estudiantes.id");
        $data = $data->join("grupo_aulas", "grupo_aulas.id", "matriculas.grupo_aulas_id");
        $data = $data->join("areas", "areas.id", "grupo_aulas.areas_id");
        $data = $data->join("grupos", "grupos.id", "grupo_aulas.grupos_id");
        $data = $data->join("turnos", "turnos.id", "grupo_aulas.turnos_id");
        $data = $data->join("colegios", "colegios.id", "estudiantes.colegios_id");
        $data = $data->join("tipo_colegios", "tipo_colegios.id", "colegios.tipo_colegios_id");
        // $data = $data->orderBy("estudiantes.paterno", "asc")->orderBy("estudiantes.materno", "asc")->orderBy("estudiantes.nombres", "asc");


        $data = $data->get();

        // dd($data[0]->docenteApto);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        $pdf = new PDF();
        $pdf::SetMargins(10, 30, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 50, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 220, 6, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Reporte de Pagos');
        $pdf::AddPage('L');

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 10, 'REPORTE DE PAGOS', 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                    <thead>
                        <tr style="font-weight: bold;">

                            <td width="50"  align="center">DNI</td>
                            <td width="120" align="center">Apellidos y Nombres</td>
                            <td width="50"  align="center">Area</td>
                            <td width="40"  align="center">Turno</td>
                            <td width="35"  align="center">Grupo</td>
                            <td width="55"  align="center">Descuento</td>
                            <td width="50"  align="center">Tipo Colegio</td>
                            <td width="50"  align="center">Pago Matricula</td>
                            <td width="60"  align="center">Mensualidad Total</td>
                            <td width="60"  align="center">Primera Mensualidad</td>
                            <td width="60"  align="center">Segunda Mensualidad</td>
                            <td width="60"  align="center">Tercera Mensualidad</td>
                            <td width="60"  align="center">Cuarta Mensualidad</td>
                            <td width="40"  align="center">Moras</td>
                        </tr>
                    </thead>';
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';

        foreach ($data as $key => $value) {
            $tabla .= '<tr>
                        <td width="50"  align="center">' . $value->nro_documento . '</td>
                        <td width="120" align="center">' . $value->paterno . ' ' . $value->materno . ' ' . $value->nombres . '</td>
                        <td width="50"  align="center">' . $value->area . '</td>
                        <td width="40"  align="center">' . $value->turno . '</td>
                        <td width="35"  align="center">' . $value->grupo . '</td>
                        <td width="55"  align="center">' . $value->descuento . '</td>
                        <td width="50"  align="center">' . $value->tipo_colegio . '</td>
                        <td width="50"  align="center">' . $value->pago_matricula . '</td>
                        <td width="60"  align="center">' . $value->mensualidad_total . '</td>
                        <td width="60"  align="center">' . $value->primera_mensualidad . '</td>
                        <td width="60"  align="center">' . $value->segunda_mensualidad . '</td>
                        <td width="60"  align="center">' . $value->tercera_mensualidad . '</td>
                        <td width="60"  align="center">' . $value->cuarta_mensualidad . '</td>
                        <td width="40"  align="center">' . $value->moras . '</td>
                    </tr>';
        }

        $tabla .= '</tbody></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('ReportePagos.pdf', 'I');
    }
    public function rptEstudianteListaPdf($id)
    {
        // dd($id);
        $estudiantes = Matricula::with("estudiante")
            ->select("matriculas.*")
            // ->whereHas('estudiante', function (Builder $query) {
            //     $query->orderBy('paterno',"asc");
            // })
            ->join("estudiantes as e", "e.id", "matriculas.estudiantes_id")

            ->where([
                ["grupo_aulas_id", $id]
            ])
            ->orderBy('e.paterno', "asc")
            ->orderBy('e.materno', "asc")
            ->orderBy('e.nombres', "asc")
            ->get();
        $grupoAula = GrupoAula::with(["grupo", "turno", "area", "aula"])->find($id);
        // dd($grupoAula);
        // $usuario = User::find($asistencia->users_id);
        $user = Auth::user()->paterno . ' ' . Auth::user()->materno . ' ' . Auth::user()->name;

        // $fecha = date("d/m/Y", strtotime($asistencia->fecha));
        // $hora = date("H:i:s", strtotime($asistencia->created_at));
        // dd($estudiantes);
        $periodo = Periodo::where("estado", "1")->first();
        $pdf = new PDF();
        $pdf::SetMargins(10, 40, 10);
        PDF::setFooterCallback(function ($pdf) use ($user) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(170, 10, $user . ' - ' . date("d/m/Y h:i a"), "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) use ($grupoAula) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 10, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 170, 6, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, "Sede " . $grupoAula->aula->local->sede->denominacion . " - " . $grupoAula->aula->local->denominacion, 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, $grupoAula->area->denominacion . " " . $grupoAula->grupo->denominacion, 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Lista Estudiante');
        $pdf::AddPage();

        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 12);
        // $pdf::MultiCell(0, 10, 'LISTA DE ASISTENCIA - ' . "I-201" . ' - ' , 0, 'C', 0, 1, '', '', true);
        // $pdf::ln();
        $pdf::SetFont('helvetica', '', 9);
        $tabla = '<table cellspacing="0" cellpadding="7" border="1">
                    <thead>
                        <tr style="font-weight: bold;">
                            <td width="30"  align="center">#</td>
                            <td width="100"  align="center">DNI</td>
                            <td width="130" align="center">Ap. Paterno</td>
                            <td width="130"  align="center">Ap. Materno</td>
                            <td width="150"  align="center">Nombres</td>
                        </tr>
                    </thead>';
        // $pdf::SetFont('helvetica', '', 10);
        $tabla .= '<tbody>';

        foreach ($estudiantes as $key => $value) {
            $tabla .= '<tr>
                            <td width="30"  align="center">' . ($key + 1) . '</td>
                            <td width="100"  align="center">' . $value->estudiante->nro_documento . '</td>
                            <td width="130" align="left">' . $value->estudiante->paterno . '</td>
                            <td width="130" align="left">' . $value->estudiante->materno . '</td>
                            <td width="150"  align="left">' . $value->estudiante->nombres  . '</td>
                        </tr>';
        }

        $tabla .= '</tbody></table>';
        $pdf::writeHTML($tabla, true, false, true, false, 'C');

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output($grupoAula->aula->local->sede->denominacion . " " . $grupoAula->aula->local->denominacion . " " . $grupoAula->grupo->denominacion . '.pdf', 'I');
    }
    public function carnetPdf($id)
    {
        // QrCode::generate('Make me into a QrCode!');
        // return "0";
        $estudiantes = Matricula::with(["estudiante", "grupoAula"])->where("grupo_aulas_id", $id)->get();
        $grupoAula = GrupoAula::with(["grupo", "aula", "turno"])->find($id);
        $pdf = new PDF();
        $pdf::SetMargins(20, 10, 20);
        $pdf::SetTitle('Lista Estudiante');
        $pdf::AddPage();
        $pdf::SetAutoPageBreak(TRUE, 0);

        // PDF::setHeaderCallback(function ($pdf){
        //     $pdf->SetY(0);
        //     $pdf->SetFont('helvetica', 'b', 10);
        //     $pdf->Cell(190, 4, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 1, 1, 'C', 0, '', 0);
        // });
        PDF::setFooterCallback(function ($pdf) use ($grupoAula) {
            $pdf->SetY(-10);
            // $y = $pdf->SetY(-15);
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->Cell(170, 10, $grupoAula->aula->local->sede->denominacion . "  " . $grupoAula->turno->denominacion . "  " . $grupoAula->grupo->denominacion, "t", false, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(0, 10, 'Pagina ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });

        // $pdf::Image('images/carnet.jpeg', 20, 10, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
        // $pdf::Image('images/carnet.jpeg', 105+2, 10, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);

        $style = array(
            'border' => 0,
            'vpadding' => 0,
            'hpadding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );


        $y = $pdf::GetY();
        $pdf::SetY($y);
        $x = 20;
        $y = 10;
        // $pdf::Image('images/carnet2.jpg', $x, $y, 85, 54, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
        // $pdf::write2DBarcode('47520697', 'QRCODE,L', $x+60, $y+27, 19, 19, $style, 'N');
        // $pdf::Image(Storage::disk('fotos')->path("ILl920220427_015301.png"), $x+6, $y+10, 22, 29, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        // $pdf::SetFont('helvetica', 'b', 7);
        // $pdf::SetTextColor(20,40,56);
        // $pdf::MultiCell(30, 4, 'ROMERO CONDORI', 0, 'L', 0, 1, $x+30.5,$y+13, true);
        // $pdf::MultiCell(30, 4, 'JAVIER ELARD', 0, 'L', 0, 1, $x+30.5,$y+18.5, true);
        // $pdf::MultiCell(30, 4, 'SOCIALES', 0, 'L', 0, 1, $x+30.5,$y+24, true);
        // $pdf::MultiCell(30, 4, 'VIRTUAL', 0, 'L', 0, 1, $x+30.5,$y+29.5, true);
        // $pdf::MultiCell(30, 4, 'NOCHE', 0, 'L', 0, 1, $x+30.5,$y+35, true);
        // $pdf::MultiCell(30, 4, 'S-303', 0, 'L', 0, 1, $x+30.5,$y+40.5, true);
        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::MultiCell(20, 4, '47520697', 0, 'C', 0, 1, $x+60,$y+46, true);

        foreach ($estudiantes as $key => $value) {
            $dni = $value->estudiante->nro_documento;
            $apellidos = $value->estudiante->paterno . " " . $value->estudiante->materno;
            $nombres = $value->estudiante->nombres;
            $area = mb_strtoupper($value->grupoAula->area->denominacion, "utf-8");
            $sede = mb_strtoupper($value->grupoAula->aula->local->sede->denominacion, "utf-8");
            $turno = mb_strtoupper($value->grupoAula->turno->denominacion, "utf-8");
            $grupo = $value->grupoAula->grupo->denominacion;

            $pdf::Image('images/carnet_frente.jpg', $x, $y, 85, 54, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
            $pdf::write2DBarcode($dni, 'QRCODE,L', $x + 60, $y + 27, 19, 19, $style, 'N');
            $pdf::Image(Storage::disk('fotos')->path($value->estudiante->foto), $x + 6, $y + 10, 22, 29, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::SetTextColor(20, 40, 56);
            $pdf::MultiCell(50, 4, $apellidos, 0, 'L', 0, 1, $x + 30.5, $y + 13, true);
            $pdf::MultiCell(50, 4, $nombres, 0, 'L', 0, 1, $x + 30.5, $y + 18.5, true);
            $pdf::MultiCell(30, 4, $area, 0, 'L', 0, 1, $x + 30.5, $y + 24, true);
            $pdf::MultiCell(30, 4, $sede, 0, 'L', 0, 1, $x + 30.5, $y + 29.5, true);
            $pdf::MultiCell(30, 4, $turno, 0, 'L', 0, 1, $x + 30.5, $y + 35, true);
            $pdf::MultiCell(30, 4, $grupo, 0, 'L', 0, 1, $x + 30.5, $y + 40.5, true);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::MultiCell(20, 4, $dni, 0, 'C', 0, 1, $x + 60, $y + 46, true);
            // $pdf::Image('images/carnet.jpeg', $x+87, $y, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
            if (($key + 1) % 2 == 0) {
                $y = $y + 1.5;
                $y = $y + 54;
                $x = 20;
                if ($y > 270) {
                    $pdf::AddPage();
                    $y = 10;
                }
            } else {
                $x = $x + 86.5;
            }
        }

        $pdf::SetFont('helvetica', 'b', 12);

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('carnets.pdf', 'I');
    }
    public function carnetPdfReverso()
    {
        // QrCode::generate('Make me into a QrCode!');
        // return "0";


        $pdf = new PDF();
        $pdf::SetMargins(20, 10, 20);
        $pdf::SetTitle('Lista Estudiante');
        $pdf::AddPage();
        $pdf::SetAutoPageBreak(TRUE, 0);

        // $pdf::Image('images/carnet.jpeg', 20, 10, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
        // $pdf::Image('images/carnet.jpeg', 105+2, 10, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);

        $style = array(
            'border' => 0,
            'vpadding' => 0,
            'hpadding' => 0,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );


        $y = $pdf::GetY();
        $pdf::SetY($y);
        $x = 18;
        $y = 10;
        // $pdf::Image('images/carnet2.jpg', $x, $y, 85, 54, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
        // $pdf::write2DBarcode('47520697', 'QRCODE,L', $x+60, $y+27, 19, 19, $style, 'N');
        // $pdf::Image(Storage::disk('fotos')->path("ILl920220427_015301.png"), $x+6, $y+10, 22, 29, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        // $pdf::SetFont('helvetica', 'b', 7);
        // $pdf::SetTextColor(20,40,56);
        // $pdf::MultiCell(30, 4, 'ROMERO CONDORI', 0, 'L', 0, 1, $x+30.5,$y+13, true);
        // $pdf::MultiCell(30, 4, 'JAVIER ELARD', 0, 'L', 0, 1, $x+30.5,$y, true);
        // $pdf::MultiCell(30, 4, 'SOCIALES', 0, 'L', 0, 1, $x+30.5,$y+24, true);
        // $pdf::MultiCell(30, 4, 'VIRTUAL', 0, 'L', 0, 1, $x+30.5,$y+29.5, true);
        // $pdf::MultiCell(30, 4, 'NOCHE', 0, 'L', 0, 1, $x+30.5,$y+35, true);
        // $pdf::MultiCell(30, 4, 'S-303', 0, 'L', 0, 1, $x+30.5,$y+40.5, true);
        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::MultiCell(20, 4, '47520697', 0, 'C', 0, 1, $x+60,$y+46, true);
        for ($key = 0; $key < 10; $key++) {
            $pdf::Image('images/carnet_reverso.jpg', $x, $y, 85, 54, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
            $pdf::Image('images/firma.png', $x + 22, $y + 24, 40, 15, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);
            // $pdf::write2DBarcode($dni, 'QRCODE,L', $x+60, $y+27, 19, 19, $style, 'N');
            if (($key + 1) % 2 == 0) {
                $y = $y + 1.5;
                $y = $y + 54;
                $x = 18;
                // if($y>270){
                //     $pdf::AddPage();
                //     $y = 10;
                // }
            } else {
                $x = $x + 86.5;
            }
        }
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        // PRINT VARIOUS 1D BARCODES

        // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
        // $pdf::write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N');

        // foreach ($estudiantes as $key => $value) {
        //     $dni = $value->estudiante->nro_documento;
        //     $apellidos = $value->estudiante->paterno." ".$value->estudiante->materno;
        //     $nombres = $value->estudiante->nombres;
        //     $area = mb_strtoupper($value->grupoAula->area->denominacion);
        //     $sede = mb_strtoupper($value->grupoAula->aula->local->sede->denominacion);
        //     $turno = mb_strtoupper($value->grupoAula->turno->denominacion,"utf-8");
        //     $grupo = $value->grupoAula->grupo->denominacion;

        //     $pdf::Image('images/carnet_frente.jpg', $x, $y, 85, 54, 'JPG', '', '', false, 150, '', false, false, 0, false, false, false);
        //     $pdf::write2DBarcode($dni, 'QRCODE,L', $x+60, $y+27, 19, 19, $style, 'N');
        //     $pdf::Image(Storage::disk('fotos')->path("ILl920220427_015301.png"), $x+6, $y+10, 22, 29, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        //     $pdf::SetFont('helvetica', 'b', 7);
        //     $pdf::SetTextColor(20,40,56);
        //     $pdf::MultiCell(50, 4, $apellidos, 0, 'L', 0, 1, $x+30.5,$y+13, true);
        //     $pdf::MultiCell(50, 4, $nombres, 0, 'L', 0, 1, $x+30.5,$y+18.5, true);
        //     $pdf::MultiCell(30, 4, $area, 0, 'L', 0, 1, $x+30.5,$y+24, true);
        //     $pdf::MultiCell(30, 4, $sede, 0, 'L', 0, 1, $x+30.5,$y+29.5, true);
        //     $pdf::MultiCell(30, 4, $turno, 0, 'L', 0, 1, $x+30.5,$y+35, true);
        //     $pdf::MultiCell(30, 4, $grupo, 0, 'L', 0, 1, $x+30.5,$y+40.5, true);
        //     $pdf::SetFont('helvetica', 'b', 8);
        //     $pdf::MultiCell(20, 4, $dni, 0, 'C', 0, 1, $x+60,$y+46, true);
        //     // $pdf::Image('images/carnet.jpeg', $x+87, $y, 85, 54, 'JPG', '', '', true, 150, '', false, false, 0, false, false, false);
        //     if(($key+1)%2==0){
        //         $y=$y+0.5;
        //         $y=$y+54;
        //         $x=20;
        //         if($y>270){
        //             $pdf::AddPage();
        //             $y = 10;
        //         }
        //     }else{
        //         $x=$x+85.5;
        //     }


        // }

        $pdf::SetFont('helvetica', 'b', 12);

        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('carnets.pdf', 'I');
    }
    public function rptDocenteIngresantesIndex()
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
        return view("intranet.reporte.docente-ingresantes", $response);
    }
    public function rptDocenteIngresantesLista()
    {
        $table = new EloquentVueTables;
        $data = $table->get(new BuDocenteEstudiante(), ['id'], []);
        $data = $data->select(
            "bu_docente_estudiantes.d_dni",
            "bu_docente_estudiantes.d_nombres",
            "bu_docente_estudiantes.d_paterno",
            "bu_docente_estudiantes.d_materno",
            "bu_docente_estudiantes.curso",
            DB::raw("COUNT(bu_docente_estudiantes.d_dni) as contador")
        );
        $data = $data->leftJoin("ingresantes as i", "i.dni", "bu_docente_estudiantes.e_dni");
        $data = $data->whereNotNull("i.dni");
        $data = $data->groupBy("bu_docente_estudiantes.d_dni", "bu_docente_estudiantes.d_nombres", "bu_docente_estudiantes.d_paterno", "bu_docente_estudiantes.d_materno", "bu_docente_estudiantes.curso");
        $data = $data->orderBy(DB::raw("COUNT(bu_docente_estudiantes.d_dni)"), "desc");
        $response = $table->finish($data);

        return response()->json($response);
    }


    public function totalHorasModalidadDocente()
    {
        // Crear un nuevo objeto de hoja de cálculo
        $spreadsheet = new Spreadsheet();

        // Seleccionar la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        // Agregar datos al informe
        $sheet->setCellValue('A1', 'Nombre');
        $sheet->setCellValue('B1', 'Apellido');
        $sheet->setCellValue('C1', 'Edad');

        $usuarios = User::all(); // Suponiendo que tienes un modelo User

        $row = 2;

        foreach ($usuarios as $usuario) {
            $sheet->setCellValue('A' . $row, $usuario->nombre);
            $sheet->setCellValue('B' . $row, $usuario->apellido);
            $sheet->setCellValue('C' . $row, $usuario->edad);

            $row++;
        }

        // Guardar el archivo en formato XLSX
        $response = new StreamedResponse(function () use ($spreadsheet) {
            // Definir el tipo de contenido y el encabezado para la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el escritor para generar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Devolver la respuesta de transmisión (streamed response)
        return $response;
    }


    public function rptDocenteHorasTotal()
    {

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Personalizar el contenido del reporte
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('F1:G1');
        $sheet->setCellValue('F1', 'Reporte Docentes Horas Pago Total');

        // Establecer estilo y formato para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3366CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('F1:G1')->applyFromArray($headerStyle);

        $datos = ['Nro. Documento', 'Paterno', 'Materno', 'Nombres', 'Celular', 'Email', 'Área', 'Grupo', 'Sede', 'Modalidad', 'Curso', 'Horas de Pago', 'Total Horas'];

        $columnaInicial = 'A';
        $filaInicial = 3;

        foreach ($datos as $index => $valor) {
            $columna = chr(ord($columnaInicial) + $index);
            $celda = $columna . $filaInicial;
            $sheet->setCellValue($celda, $valor);
        }
        // Establecer estilo para las celdas de encabezado
        $headerCellStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $columnaInicial = 'A';
        $columnaFinal = chr(ord($columnaInicial) + count($datos) - 1);
        $rango = $columnaInicial . '3:' . $columnaFinal . '3';

        $sheet->getStyle($rango)->applyFromArray($headerCellStyle);


        // Obtener los datos de la consulta
        $resultados = DB::table('docentes AS d')
            ->select(
                'd.nro_documento',
                'd.paterno',
                'd.materno',
                'd.nombres',
                'd.celular',
                'd.email',
                'eas.denominacion as area',
                'gr.denominacion as grupo',
                'sed.denominacion as modalidad',
                'sds.denominacion as sede',
                'cu.denominacion as curso',
                'ad.horas_pago',
                DB::raw('SUM(ad.cantidad_horas) AS total_horas')
            )
            ->join('carga_academicas AS ca', 'd.id', '=', 'ca.docentes_id')
            ->join('cursos AS cu', 'cu.id', '=', 'ca.cursos_id')
            ->join('grupo_aulas AS ga', 'ga.id', '=', 'ca.grupo_aulas_id')
            ->join('grupos AS gr', 'gr.id', '=', 'ga.grupos_id')
            ->join('asistencia_docentes AS ad', 'ad.carga_academicas_id', '=', 'ca.id')
            ->join('docente_aptos AS apt', 'd.id', 'apt.docentes_id')
            ->join('inscripcion_docentes AS id', 'id.docentes_id', '=', 'd.id')
            ->join('areas AS eas', 'eas.id', '=', 'id.areas_id')
            ->join(
                DB::raw('(SELECT id, sedes_id, inscripcion_docentes_id FROM disponibilidades GROUP BY inscripcion_docentes_id) disp'),
                'disp.inscripcion_docentes_id',
                '=',
                'id.id'
            )
            ->join(DB::raw("(SELECT id,
            CASE
                WHEN denominacion <> 'Virtual' THEN 'No virtual'
                ELSE denominacion
            END AS denominacion
            FROM sedes) sed"), 'sed.id', '=', 'disp.sedes_id')
            ->join('sedes AS sds', 'sds.id', '=', 'disp.sedes_id')
            ->where('ad.estado', 1)
            ->groupBy('d.nro_documento', 'sed.denominacion')

            ->get();

        // Recorrer los resultados y agregarlos al archivo Excel
        $row = 4; // Fila inicial para los datos
        foreach ($resultados as $resultado) {
            $sheet->setCellValue('A' . $row, $resultado->nro_documento);
            $sheet->setCellValue('B' . $row, $resultado->paterno);
            $sheet->setCellValue('C' . $row, $resultado->materno);
            $sheet->setCellValue('D' . $row, $resultado->nombres);
            $sheet->setCellValue('E' . $row, $resultado->celular);
            $sheet->setCellValue('F' . $row, $resultado->email);
            $sheet->setCellValue('G' . $row, $resultado->area);
            $sheet->setCellValue('H' . $row, $resultado->grupo);
            $sheet->setCellValue('I' . $row, $resultado->sede);
            $sheet->setCellValue('J' . $row, $resultado->modalidad);
            $sheet->setCellValue('K' . $row, $resultado->curso);
            $sheet->setCellValue('L' . $row, $resultado->horas_pago);
            $sheet->setCellValue('M' . $row, $resultado->total_horas);

            $row++;
        }

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'M') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            // Definir el tipo de contenido y el encabezado para la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el escritor para generar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Devolver la respuesta de transmisión (streamed response)
        return $response;
    }


    public function rptDocenteSede(Request $request)
    {
        //  return $request->params['fecha_ini'];
        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();

        // Personalizar el contenido del reporte
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('D1:I1');
        $titulo = 'Reporte Docentes Horas por Sede: ' . $request->params['fecha_ini'] . ' -- ' . $request->params['fecha_fin'];
        $sheet->setCellValue('D1', $titulo);

        // Establecer estilo y formato para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3366CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('D1:I1')->applyFromArray($headerStyle);

        $datos = ['docentes_id', 'Nro. Documento', 'Paterno', 'Materno', 'Nombres', 'Celular', 'Email', 'Juliaca', 'Puno', 'HVirtual', 'Juli', 'Azangaro', 'Ilave'];

        $columnaInicial = 'A';
        $filaInicial = 3;

        foreach ($datos as $index => $valor) {
            $columna = chr(ord($columnaInicial) + $index);
            $celda = $columna . $filaInicial;
            $sheet->setCellValue($celda, $valor);
        }
        // Establecer estilo para las celdas de encabezado
        $headerCellStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $columnaInicial = 'A';
        $columnaFinal = chr(ord($columnaInicial) + count($datos) - 1);
        $rango = $columnaInicial . '3:' . $columnaFinal . '3';

        $sheet->getStyle($rango)->applyFromArray($headerCellStyle);

        if ($request->params['fecha_ini']) {
            // Obtener los datos de la consulta
            $resultados = DB::table(function ($query) use ($request) {
                $query->select(
                    'docentes.id AS docentes_id',
                    'docentes.nro_documento',
                    'docentes.paterno',
                    'docentes.materno',
                    'docentes.nombres',
                    'docentes.celular',
                    'docentes.email',
                    's.denominacion AS sede',
                    DB::raw("SUM(CASE WHEN ad.estado = '1' THEN ad.horas_pago ELSE 0 END) AS horas_presente"),
                    DB::raw("SUM(CASE WHEN ad.estado = '2' THEN ad.horas_pago ELSE 0 END) AS horas_tarde")
                )
                    ->from('asistencia_docentes AS ad')
                    ->join('docentes', 'docentes.id', '=', 'ad.docentes_id')
                    ->join('carga_academicas AS ca', 'ca.id', '=', 'ad.carga_academicas_id')
                    ->join('grupo_aulas AS ga', 'ga.id', '=', 'ca.grupo_aulas_id')
                    ->join('aulas AS a', 'a.id', '=', 'ga.aulas_id')
                    ->join('locales AS l', 'l.id', '=', 'a.locales_id')
                    ->join('sedes AS s', 's.id', '=', 'l.sedes_id')
                    ->whereBetween('ad.fecha', [$request->params['fecha_ini'], $request->params['fecha_fin']])
                    ->groupBy('docentes_id', 'nro_documento', 'paterno', 'materno', 'nombres', 'celular', 'email', 'sede');
            }, 'ad')
                ->select(
                    'docentes_id',
                    'nro_documento',
                    'paterno',
                    'materno',
                    'nombres',
                    'celular',
                    'email',
                    DB::raw("SUM(CASE WHEN sede = 'Juliaca' THEN horas_tarde ELSE 0 END) AS tardeJuliaca"),
                    DB::raw("SUM(CASE WHEN sede = 'Puno' THEN horas_tarde ELSE 0 END) AS tardePuno"),
                    DB::raw("SUM(CASE WHEN sede = 'Virtual' THEN horas_tarde ELSE 0 END) AS tardeHVirtual"),
                    DB::raw("SUM(CASE WHEN sede = 'Juli' THEN horas_tarde ELSE 0 END) AS tardeJuli"),
                    DB::raw("SUM(CASE WHEN sede = 'Azangaro' THEN horas_tarde ELSE 0 END) AS tardeAzangaro"),
                    DB::raw("SUM(CASE WHEN sede = 'Ilave' THEN horas_tarde ELSE 0 END) AS tardeIlave"),

                    DB::raw("SUM(CASE WHEN sede = 'Juliaca' THEN horas_presente+horas_tarde ELSE 0 END) AS Juliaca"),
                    DB::raw("SUM(CASE WHEN sede = 'Puno' THEN horas_presente+horas_tarde ELSE 0 END) AS Puno"),
                    DB::raw("SUM(CASE WHEN sede = 'Virtual' THEN horas_presente+horas_tarde ELSE 0 END) AS HVirtual"),
                    DB::raw("SUM(CASE WHEN sede = 'Juli' THEN horas_presente+horas_tarde ELSE 0 END) AS Juli"),
                    DB::raw("SUM(CASE WHEN sede = 'Azangaro' THEN horas_presente+horas_tarde ELSE 0 END) AS Azangaro"),
                    DB::raw("SUM(CASE WHEN sede = 'Ilave' THEN horas_presente+horas_tarde ELSE 0 END) AS Ilave")
                )
                ->groupBy('docentes_id', 'nro_documento', 'paterno', 'materno', 'nombres', 'celular', 'email')
                ->orderBy('paterno', 'asc')
                ->orderBy('materno', 'asc')
                ->orderBy('nombres', 'asc')
                ->get();
        } else {
            // Obtener los datos de la consulta
            $resultados = DB::table(function ($query) use ($request) {
                $query->select(
                    'docentes.id AS docentes_id',
                    'docentes.nro_documento',
                    'docentes.paterno',
                    'docentes.materno',
                    'docentes.nombres',
                    'docentes.celular',
                    'docentes.email',
                    's.denominacion AS sede',
                    DB::raw("SUM(CASE WHEN ad.estado = '1' THEN ad.horas_pago ELSE 0 END) AS horas_presente"),
                    DB::raw("SUM(CASE WHEN ad.estado = '2' THEN ad.horas_pago ELSE 0 END) AS horas_tarde")
                )
                    ->from('asistencia_docentes AS ad')
                    ->join('docentes', 'docentes.id', '=', 'ad.docentes_id')
                    ->join('carga_academicas AS ca', 'ca.id', '=', 'ad.carga_academicas_id')
                    ->join('grupo_aulas AS ga', 'ga.id', '=', 'ca.grupo_aulas_id')
                    ->join('aulas AS a', 'a.id', '=', 'ga.aulas_id')
                    ->join('locales AS l', 'l.id', '=', 'a.locales_id')
                    ->join('sedes AS s', 's.id', '=', 'l.sedes_id')
                    ->groupBy('docentes_id', 'nro_documento', 'paterno', 'materno', 'nombres', 'celular', 'email', 'sede');
            }, 'ad')
                ->select(
                    'docentes_id',
                    'nro_documento',
                    'paterno',
                    'materno',
                    'nombres',
                    'celular',
                    'email',
                    DB::raw("SUM(CASE WHEN sede = 'Juliaca' THEN horas_tarde ELSE 0 END) AS tardeJuliaca"),
                    DB::raw("SUM(CASE WHEN sede = 'Puno' THEN horas_tarde ELSE 0 END) AS tardePuno"),
                    DB::raw("SUM(CASE WHEN sede = 'Virtual' THEN horas_tarde ELSE 0 END) AS tardeHVirtual"),
                    DB::raw("SUM(CASE WHEN sede = 'Juli' THEN horas_tarde ELSE 0 END) AS tardeJuli"),
                    DB::raw("SUM(CASE WHEN sede = 'Azangaro' THEN horas_tarde ELSE 0 END) AS tardeAzangaro"),
                    DB::raw("SUM(CASE WHEN sede = 'Ilave' THEN horas_tarde ELSE 0 END) AS tardeIlave"),

                    DB::raw("SUM(CASE WHEN sede = 'Juliaca' THEN horas_presente+horas_tarde ELSE 0 END) AS Juliaca"),
                    DB::raw("SUM(CASE WHEN sede = 'Puno' THEN horas_presente+horas_tarde ELSE 0 END) AS Puno"),
                    DB::raw("SUM(CASE WHEN sede = 'Virtual' THEN horas_presente+horas_tarde ELSE 0 END) AS HVirtual"),
                    DB::raw("SUM(CASE WHEN sede = 'Juli' THEN horas_presente+horas_tarde ELSE 0 END) AS Juli"),
                    DB::raw("SUM(CASE WHEN sede = 'Azangaro' THEN horas_presente+horas_tarde ELSE 0 END) AS Azangaro"),
                    DB::raw("SUM(CASE WHEN sede = 'Ilave' THEN horas_presente+horas_tarde ELSE 0 END) AS Ilave")
                )
                ->groupBy('docentes_id', 'nro_documento', 'paterno', 'materno', 'nombres', 'celular', 'email')
                ->orderBy('paterno', 'asc')
                ->orderBy('materno', 'asc')
                ->orderBy('nombres', 'asc')
                ->get();
        }

        // Recorrer los resultados y agregarlos al archivo Excel
        $row = 4; // Fila inicial para los datos
        foreach ($resultados as $resultado) {
            $sheet->setCellValue('A' . $row, $resultado->docentes_id);
            $sheet->setCellValue('B' . $row, $resultado->nro_documento);
            $sheet->setCellValue('C' . $row, $resultado->paterno);
            $sheet->setCellValue('D' . $row, $resultado->materno);
            $sheet->setCellValue('E' . $row, $resultado->nombres);
            $sheet->setCellValue('F' . $row, $resultado->celular);
            $sheet->setCellValue('G' . $row, $resultado->email);
            $sheet->setCellValue('H' . $row, $resultado->Juliaca);
            $sheet->setCellValue('I' . $row, $resultado->Puno);
            $sheet->setCellValue('J' . $row, $resultado->HVirtual);
            $sheet->setCellValue('K' . $row, $resultado->Juli);
            $sheet->setCellValue('L' . $row, $resultado->Azangaro);
            $sheet->setCellValue('M' . $row, $resultado->Ilave);

            $sheet->getStyle('H' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardeJuliaca > 0 ? 'f4ff81' : 'ffffff');
            $sheet->getStyle('I' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardePuno > 0 ? 'f4ff81' : 'ffffff');
            $sheet->getStyle('J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardeHVirtual > 0 ? 'f4ff81' : 'ffffff');
            $sheet->getStyle('K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardeJuli > 0 ? 'f4ff81' : 'ffffff');
            $sheet->getStyle('L' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardeAzangaro > 0 ? 'f4ff81' : 'ffffff');
            $sheet->getStyle('M' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($resultado->tardeIlave > 0 ? 'f4ff81' : 'ffffff');
            $row++;
        }

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'N') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            // Definir el tipo de contenido y el encabezado para la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el escritor para generar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Devolver la respuesta de transmisión (streamed response)
        return $response;
    }


    //segunda funcion


    public function rptPagosEfectuadosDistincion()
    {

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();


        // Personalizar el contenido del reporte
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->mergeCells('F1:G1');
        $sheet->setCellValue('F1', 'Reporte Pagos Efectuados sin Mora');

        // Establecer estilo y formato para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3366CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('F1:G1')->applyFromArray($headerStyle);
        $datos = [
            'ID',
            'Nro Documento',
            'Paterno',
            'Materno',
            'Nombres',
            'Sede',
            'Area',
            'Turno',
            'Grupo',
            'Descuento',
            'Tipo Colegio',
            'Estado',
            'Pago Total',
            'Pago Matricula',
            'Primera Mensualidad',
            'Segunda Mensualidad',
            'Tercera Mensualidad',
            'Cuarta Mensualidad',
        ];
        $columnaInicial = 'A';
        $filaInicial = 3;

        foreach ($datos as $index => $valor) {
            $columna = chr(ord($columnaInicial) + $index);
            $celda = $columna . $filaInicial;
            $sheet->setCellValue($celda, $valor);
        }
        // Establecer estilo para las celdas de encabezado
        $headerCellStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $columnaInicial = 'A';
        $columnaFinal = chr(ord($columnaInicial) + count($datos) - 1);
        $rango = $columnaInicial . '3:' . $columnaFinal . '3';

        $sheet->getStyle($rango)->applyFromArray($headerCellStyle);

        // Obtener los datos de la consulta
        $resultados = DB::table('inscripciones')
            ->select(
                'inscripciones.id',
                'estudiantes.nro_documento',
                'estudiantes.paterno',
                'estudiantes.materno',
                'estudiantes.nombres',
                's.denominacion AS sedes',
                'areas.denominacion AS area',
                'turnos.denominacion AS turno',
                'grupos.denominacion AS grupo',
                'tarifa_estudiantes1.monto AS monto1',
                'tarifa_estudiantes2.monto AS monto2',
                'tarifa_estudiantes3.monto AS monto3',
                'tarifa_estudiantes4.monto AS monto4',
                DB::raw("CASE
                        WHEN inscripciones.tipo_estudiante = '1' THEN 'Normal'
                        WHEN inscripciones.tipo_estudiante = '2' THEN 'Hijo de trabajador'
                        WHEN inscripciones.tipo_estudiante = '3' THEN 'Descuento Trabajador UNA'
                        WHEN inscripciones.tipo_estudiante = '4' THEN 'Hermanos'
                        WHEN inscripciones.tipo_estudiante = '5' THEN 'Resolución Rectoral'
                        ELSE 'Servicio Militar'
                    END AS descuento"),
                'tipo_colegios.denominacion AS tipo_colegio',
                'inscripciones.estado',
                DB::raw('SUM(tarifa_estudiantes.monto) AS pago_total'),
                DB::raw("CONCAT_WS('|', tarifa_estudiantes0.monto, tarifa_estudiantes0.pagado, tarifa_estudiantes0.mora) AS pago_matricula"),
                DB::raw("tarifa_estudiantes1.monto - tarifa_estudiantes1.pagado AS primera_mensualidad"),
                DB::raw("tarifa_estudiantes2.monto - tarifa_estudiantes2.pagado AS segunda_mensualidad"),
                DB::raw("tarifa_estudiantes3.monto - tarifa_estudiantes3.pagado AS tercera_mensualidad"),
                DB::raw("tarifa_estudiantes4.monto - tarifa_estudiantes4.pagado AS cuarta_mensualidad")
            )
            ->join('estudiantes', 'estudiantes.id', '=', 'inscripciones.estudiantes_id')
            ->leftJoin('tarifa_estudiantes', 'tarifa_estudiantes.estudiantes_id', '=', 'estudiantes.id')
            ->leftJoin('tarifa_estudiantes AS tarifa_estudiantes0', function ($join) {
                $join->on('tarifa_estudiantes0.nro_cuota', '=', DB::raw('0'))
                    ->on('tarifa_estudiantes0.estudiantes_id', '=', 'estudiantes.id');
            })
            ->leftJoin('tarifa_estudiantes AS tarifa_estudiantes1', function ($join) {
                $join->on('tarifa_estudiantes1.nro_cuota', '=', DB::raw('1'))
                    ->on('tarifa_estudiantes1.estudiantes_id', '=', 'estudiantes.id');
            })
            ->leftJoin('tarifa_estudiantes AS tarifa_estudiantes2', function ($join) {
                $join->on('tarifa_estudiantes2.nro_cuota', '=', DB::raw('2'))
                    ->on('tarifa_estudiantes2.estudiantes_id', '=', 'estudiantes.id');
            })
            ->leftJoin('tarifa_estudiantes AS tarifa_estudiantes3', function ($join) {
                $join->on('tarifa_estudiantes3.nro_cuota', '=', DB::raw('3'))
                    ->on('tarifa_estudiantes3.estudiantes_id', '=', 'estudiantes.id');
            })
            ->leftJoin('tarifa_estudiantes AS tarifa_estudiantes4', function ($join) {
                $join->on('tarifa_estudiantes4.nro_cuota', '=', DB::raw('4'))
                    ->on('tarifa_estudiantes4.estudiantes_id', '=', 'estudiantes.id');
            })
            ->join('sedes AS s', 's.id', '=', 'inscripciones.sedes_id')
            ->leftJoin('matriculas', 'matriculas.estudiantes_id', '=', 'estudiantes.id')
            ->leftJoin('grupo_aulas', 'grupo_aulas.id', '=', 'matriculas.grupo_aulas_id')
            ->leftJoin('areas', 'areas.id', '=', 'grupo_aulas.areas_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'grupo_aulas.grupos_id')
            ->leftJoin('turnos', 'turnos.id', '=', 'grupo_aulas.turnos_id')
            ->join('colegios', 'colegios.id', '=', 'estudiantes.colegios_id')
            ->join('tipo_colegios', 'tipo_colegios.id', '=', 'colegios.tipo_colegios_id')
            ->groupBy('estudiantes.nro_documento')
            ->get();

        // Llenar los datos en las celdas correspondientes
        $row = 4;
        foreach ($resultados as $item) {
            $sheet->setCellValue('A' . $row, $item->id)
                ->setCellValue('B' . $row, $item->nro_documento)
                ->setCellValue('C' . $row, $item->paterno)
                ->setCellValue('D' . $row, $item->materno)
                ->setCellValue('E' . $row, $item->nombres)
                ->setCellValue('F' . $row, $item->sedes)
                ->setCellValue('G' . $row, $item->area)
                ->setCellValue('H' . $row, $item->turno)
                ->setCellValue('I' . $row, $item->grupo)
                ->setCellValue('J' . $row, $item->descuento)
                ->setCellValue('K' . $row, $item->tipo_colegio)
                ->setCellValue('L' . $row, $item->estado)
                ->setCellValue('M' . $row, $item->pago_total)
                ->setCellValue('N' . $row, $item->pago_matricula)
                ->setCellValue('O' . $row, $item->primera_mensualidad)
                ->setCellValue('P' . $row, $item->segunda_mensualidad)
                ->setCellValue('Q' . $row, $item->tercera_mensualidad)
                ->setCellValue('R' . $row, $item->cuarta_mensualidad);

            // Color las celdas
            // $greenColor = Color::COLOR_GREEN;
            // $yellowColor = Color::COLOR_YELLOW;
            $greenColor = '66bb6a';
            $yellowColor = 'f4ff81';
            $redColor = 'ef5350'; // Rojo
            // $greenColor = '00FF00'; // Verde
            // $yellowColor = 'FFFF00'; // Amarillo

            // Colorear las celdas
            $sheet->getStyle('O' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($item->primera_mensualidad == $item->monto1 ? $redColor : ($item->primera_mensualidad == 0 ? $greenColor : $yellowColor));
            $sheet->getStyle('P' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($item->segunda_mensualidad == $item->monto2 ? $redColor : ($item->segunda_mensualidad == 0 ? $greenColor : $yellowColor));
            $sheet->getStyle('Q' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($item->tercera_mensualidad == $item->monto3 ? $redColor : ($item->tercera_mensualidad == 0 ? $greenColor : $yellowColor));
            $sheet->getStyle('R' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($item->cuarta_mensualidad == $item->monto4 ? $redColor : ($item->cuarta_mensualidad == 0 ? $greenColor : $yellowColor));

            $row++;
        }
        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'N') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            // Definir el tipo de contenido y el encabezado para la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el escritor para generar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Devolver la respuesta de transmisión (streamed response)
        return $response;
    }


    public function rptDocenteVirtual(Request $request)
    {
        //  return $request->params['fecha_ini'];
        // Crear un nuevo objeto Spreadsheet

        $spreadsheet = new Spreadsheet();

        // Personalizar el contenido del reporte
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->mergeCells('C1:G1');
        $titulo = 'Reporte Docentes Horas Virtual y Presencial  del: ' . $request->params['fecha_ini'] . ' -- ' . $request->params['fecha_fin'];
        $sheet->setCellValue('C1', $titulo);

        // Establecer estilo y formato para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3366CC'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $sheet->getStyle('C1:G1')->applyFromArray($headerStyle);

        $datos = ['docentes_id', 'Nro. Documento', 'Nombres', 'Celular', 'Email', 'Horas_Presencial', 'Horas_Virtual', 'Total'];

        $columnaInicial = 'A';
        $filaInicial = 3;

        foreach ($datos as $index => $valor) {
            $columna = chr(ord($columnaInicial) + $index);
            $celda = $columna . $filaInicial;
            $sheet->setCellValue($celda, $valor);
        }
        // Establecer estilo para las celdas de encabezado
        $headerCellStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EEEEEE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $columnaInicial = 'A';
        $columnaFinal = chr(ord($columnaInicial) + count($datos) - 1);
        $rango = $columnaInicial . '3:' . $columnaFinal . '3';

        $sheet->getStyle($rango)->applyFromArray($headerCellStyle);

        if ($request->params['fecha_ini']) {
            // Obtener los datos de la consulta
            // ->whereBetween('ad.fecha', [$request->params['fecha_ini'], $request->params['fecha_fin']])
            $resultados = DB::table(function ($query) use ($request) {
                $query->select(
                    'docentes.id',
                    'docentes.nro_documento',
                    DB::raw('CONCAT(docentes.nombres, " ", docentes.paterno, " ", docentes.materno) as nombre'),
                    'docentes.celular',
                    'docentes.email',
                    DB::raw('(CASE WHEN s.denominacion <> "Virtual" THEN "No virtual" ELSE s.denominacion END) AS modalidad'),
                    DB::raw('SUM(CASE WHEN ad.estado = "1" THEN ad.horas_pago ELSE 0 END) AS horas_presente'),
                    DB::raw('SUM(CASE WHEN ad.estado = "2" THEN ad.horas_pago ELSE 0 END) AS horas_tarde'),
                    DB::raw('SUM(CASE WHEN ad.estado = "3" THEN ad.horas_pago ELSE 0 END) AS horas_falta')
                )
                    ->from('asistencia_docentes as ad')
                    ->join('docentes', 'docentes.id', '=', 'ad.docentes_id')
                    ->join('carga_academicas as ca', 'ca.id', '=', 'ad.carga_academicas_id')
                    ->join('grupo_aulas as ga', 'ga.id', '=', 'ca.grupo_aulas_id')
                    ->join('aulas as a', 'a.id', '=', 'ga.aulas_id')
                    ->join('locales as l', 'l.id', '=', 'a.locales_id')
                    ->join('sedes as s', 's.id', '=', 'l.sedes_id')
                    ->whereBetween('ad.fecha', [$request->params['fecha_ini'], $request->params['fecha_fin']])
                    ->groupBy('ad.docentes_id', 'docentes.id', 'nombre', 'modalidad');
            })
                ->select(
                    'id',
                    'nro_documento',
                    'nombre',
                    'celular',
                    'email',
                    DB::raw('SUM(CASE WHEN modalidad = "Virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Hvirtual'),
                    DB::raw('SUM(CASE WHEN modalidad = "No virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Hpresencial'),
                    DB::raw('SUM(CASE WHEN modalidad = "Virtual" THEN horas_presente + horas_tarde ELSE 0 END) + SUM(CASE WHEN modalidad = "No virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Total')
                )
                ->groupBy('id', 'nombre', 'celular', 'email')
                ->get();
        } else {
            // Obtener los datos de la consulta
            $resultados = DB::table(function ($query) use ($request) {
                $query->select(
                    'docentes.id',
                    'docentes.nro_documento',
                    DB::raw('CONCAT(docentes.nombres, " ", docentes.paterno, " ", docentes.materno) as nombre'),
                    'docentes.celular',
                    'docentes.email',
                    DB::raw('(CASE WHEN s.denominacion <> "Virtual" THEN "No virtual" ELSE s.denominacion END) AS modalidad'),
                    DB::raw('SUM(CASE WHEN ad.estado = "1" THEN ad.horas_pago ELSE 0 END) AS horas_presente'),
                    DB::raw('SUM(CASE WHEN ad.estado = "2" THEN ad.horas_pago ELSE 0 END) AS horas_tarde'),
                    DB::raw('SUM(CASE WHEN ad.estado = "3" THEN ad.horas_pago ELSE 0 END) AS horas_falta')
                )
                    ->from('asistencia_docentes as ad')
                    ->join('docentes', 'docentes.id', '=', 'ad.docentes_id')
                    ->join('carga_academicas as ca', 'ca.id', '=', 'ad.carga_academicas_id')
                    ->join('grupo_aulas as ga', 'ga.id', '=', 'ca.grupo_aulas_id')
                    ->join('aulas as a', 'a.id', '=', 'ga.aulas_id')
                    ->join('locales as l', 'l.id', '=', 'a.locales_id')
                    ->join('sedes as s', 's.id', '=', 'l.sedes_id')
                    ->groupBy('ad.docentes_id', 'docentes.id', 'nombre', 'modalidad');
            })
                ->select(
                    'id',
                    'nro_documento',
                    'nombre',
                    'celular',
                    'email',
                    DB::raw('SUM(CASE WHEN modalidad = "Virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Hvirtual'),
                    DB::raw('SUM(CASE WHEN modalidad = "No virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Hpresencial'),
                    DB::raw('SUM(CASE WHEN modalidad = "Virtual" THEN horas_presente + horas_tarde ELSE 0 END) + SUM(CASE WHEN modalidad = "No virtual" THEN horas_presente + horas_tarde ELSE 0 END) AS Total')
                )
                ->groupBy('id', 'nombre', 'celular', 'email')
                ->get();
        }

        // Recorrer los resultados y agregarlos al archivo Excel
        $row = 4; // Fila inicial para los datos
        foreach ($resultados as $resultado) {
            $sheet->setCellValue('A' . $row, $resultado->id);
            $sheet->setCellValue('B' . $row, $resultado->nro_documento);
            $sheet->setCellValue('C' . $row, $resultado->nombre);
            $sheet->setCellValue('D' . $row, $resultado->celular);
            $sheet->setCellValue('E' . $row, $resultado->email);
            $sheet->setCellValue('F' . $row, $resultado->Hvirtual);
            $sheet->setCellValue('G' . $row, $resultado->Hpresencial);
            $sheet->setCellValue('H' . $row, $resultado->Total);

            //DANDO COLORES A LAS COLUMNAS H I
            $row++;
        }

        // Ajustar el ancho de las columnas automáticamente
        foreach (range('A', 'H') as $columna) {
            $sheet->getColumnDimension($columna)->setAutoSize(true);
        }

        $response = new StreamedResponse(function () use ($spreadsheet) {
            // Definir el tipo de contenido y el encabezado para la descarga
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="reporte.xlsx"');
            header('Cache-Control: max-age=0');

            // Crear el escritor para generar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        // Devolver la respuesta de transmisión (streamed response)
        return $response;
    }

     public function rptVouchersIndex(){
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
        return view("intranet.reporte.vouchers", $response);
     }

    public function generarPDFVouchers(Request $request)
    {
        $inicio = $request->input('desde');
        $fin = $request->input('hasta');
        $tipo = $request->input('tipo');

        if (!$inicio || !$fin || !$tipo) {
            return abort(400, 'Fechas requeridas');
        }

        $pagos = Pago::whereBetween('updated_at', [$inicio, $fin])
            ->when($tipo === 'imagenes', function ($query) {
                $query->where(function ($q) {
                    $q->where('voucher', 'like', '%.jpg')
                        ->orWhere('voucher', 'like', '%.jpeg')
                        ->orWhere('voucher', 'like', '%.png');
                });
            })
            ->when($tipo === 'documentos', function ($query) {
                $query->where('voucher', 'like', '%.pdf');
            })
            ->limit(100)
            ->get();

        if ($pagos->isEmpty()) {
            return abort(404, 'No se encontraron vouchers en el rango seleccionado');
        }

        $tempPath = storage_path('app/temp');
        if (!\File::exists($tempPath)) {
            \File::makeDirectory($tempPath, 0775, true);
        }

        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $isDoc = $tipo === 'documentos';
        $voucherWidth = $isDoc ? 63.33 : 95;
        $voucherHeight = $isDoc ? 98 : 69.25;
        $maxCols = $isDoc ? 3 : 2;
        $maxRows = $isDoc ? 2 : 4;

        $col = $row = 0;

        foreach ($pagos as $pago) {
            $extension = strtolower(pathinfo($pago->voucher, PATHINFO_EXTENSION));
            $basePath = storage_path("app/public/vouchers/{$pago->voucher}");
            $x = 10 + ($col * $voucherWidth);
            $y = 10 + ($row * $voucherHeight);
            $showImage = false;

            if (in_array($extension, ['jpg', 'jpeg', 'png']) && file_exists($basePath)) {
                $imagePath = $basePath;
                $showImage = true;
            } elseif ($extension === 'pdf' && file_exists($basePath)) {
                $previewPath = $tempPath . "/voucher_{$pago->id}.jpg";
                try {
                    $imagick = new \Imagick();
                    $imagick->setResolution(150, 150);
                    $imagick->readImage($basePath . '[0]');
                    $imagick->setImageFormat('jpeg');
                    $imagick->writeImage($previewPath);
                    $imagick->clear();
                    $imagick->destroy();

                    if (file_exists($previewPath)) {
                        $imagePath = $previewPath;
                        $showImage = true;
                    }
                } catch (\Exception $e) {
                    \Log::error("Error al convertir PDF a imagen: " . $e->getMessage());
                }
            }

            if ($showImage) {
                $pdf->Image($imagePath, $x, $y, $voucherWidth, $voucherHeight, 'JPG');
            }

            $pdf->SetDrawColor(0, 0, 0);
            $pdf->Rect($x, $y, $voucherWidth, $voucherHeight, 'D');
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect($x + 2, $y + 2, $voucherWidth - 5, 6, 'F');

            $pdf->SetXY($x + 3, $y + 3);
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Cell(0, 4,
                "Secuencia: {$pago->secuencia} - DNI: {$pago->nro_documento} - Fecha: {$pago->fecha} - Monto: S/ {$pago->monto}",
                0, 1, 'L', false
            );

            if (isset($previewPath) && file_exists($previewPath)) {
                unlink($previewPath);
            }

            $col++;
            if ($col >= $maxCols) {
                $col = 0;
                $row++;
            }

            if ($row >= $maxRows) {
                $pdf->AddPage();
                $row = 0;
                $col = 0;
            }
        }

        \File::cleanDirectory($tempPath);

        $filename = "reporte_vouchers_{$inicio}_a_{$fin}.pdf";
        return response($pdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    // public function rptPersonalizado()
    // {
    //     $permissions = [];
    //     if (auth()->user()->hasRole('Super Admin')) {
    //         foreach (Permission::get() as $key => $value) {
    //             array_push($permissions, $value->name);
    //         }
    //     } else {
    //         foreach (Auth::user()->getAllPermissions() as $key => $value) {
    //             array_push($permissions, $value->name);
    //         }
    //     }
    //     $response['permisos'] = json_encode($permissions);
    //     return view("intranet.reporte.generador", $response);
    // }
}
