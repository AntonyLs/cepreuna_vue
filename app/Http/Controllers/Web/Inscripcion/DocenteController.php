<?php

namespace App\Http\Controllers\Web\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use App\Models\Docente;
use App\Models\InscripcionDocente;
use App\Models\InscripcionCurso;
use App\Models\PlantillaHorario;
use App\Models\Disponibilidades;
use App\Models\Periodo;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\DB;
use App\Models\Turno;
use App\Models\Area;
use App\Models\Curricula;
use App\Models\AdjuntoGrado;
use App\Models\AdjuntoExperiencia;
use App\Models\AdjuntoCapacitaciones;
use App\Models\AdjuntoProducciones;
// use App\Models\CurriculaDetalle;
use App\Models\ConfiguracionInscripciones;
use App\Models\Experiencia;
use DB;
use PDF;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $dateTime;
    private $dateTimePartial;
    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }

    public function index()
    {
        $response["configuracion"] = ConfiguracionInscripciones::where([['tipo_usuario', 2], ['estado', '1']])->first();

        return view('web.inscripcion.docente', $response);
    }
    public function indexExtemporaneo()
    {

        return view('web.inscripcion.docente-extemporaneo');
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

        // return $request;
        // dd();
        // var_dump($request->prioridad);
        // dd($request->tipo_trabajador);
        $rules = $request->validate([
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'tipo_documento' => 'required',
            'nro_documento' => 'required',
            'ruc' => 'required|digits:11',
            'email' => 'required|email',
            'celular' => 'required|digits:9',
            'direccion' => 'required',
            'codigo' => 'required_if:condicion,2',
            'tipo_trabajador' => 'required_if:condicion,2',
            'contrato' => 'required_if:condicion,2',
            'departamento' => 'required',
            'provincia' => 'required',
            'ubigeo' => 'required',
            'prioridad' => 'required',

            'file_titulo' => 'max:2000|required_with:programa_titulo',

            'grado_tipo.*' => 'required',
            'grado_programa.*' => 'required',
            'grado_archivo.*' => 'max:2000|required',

            // 'file_experiencia' => 'max:5000|required_with:experiencia',
            // 'file_experiencia'=> 'max:3000',

            'capacitacion_tipo.*' => 'required',
            'capacitacion_titulo.*' => 'required',
            'capacitacion_archivo.*' => 'max:2000|required',

            'produccion_tipo.*' => 'required',
            'produccion_titulo.*' => 'required',
            'produccion_archivo.*' => 'max:2000|required',

            'modalidad' => 'required',
            'area' => 'required',
            'cursos' => 'array|min:1',
            'sede' => 'array|min:1',
            'disponibilidad' => 'required|array|min:1',
            'foto' => 'required',

            /**Datos Bancarios**/
            'nombre_banco' => 'required',
            'cci' => 'required|digits:20',
            //Dicto
            'dicto' => 'required',
            // 'exp_didactica' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'digits' => 'El campo :attribute debe tener exactamente :digits dígitos.',
            'programa.required' => "* El campo Especialidad es obligatorio.",
            'email' => '* El campo :attribute no es un correo electronico.',
            'foto.required' => '* La fotografia es obligatoria.',
            'cursos.min' => '* Seleccionar minimo un Curso.',
            'sede.min' => '* Seleccionar minimo una Sede.',
            'disponibilidad.min' => '* Seleccionar minimo una Disponiblidad.',
            'codigo.required_if' => '* El campo :attribute es obligatorio.',

            'file_titulo.required_with' => '* El campo archivo título es obligatorio.',

            'file_titulo.mines' => 'Solo se acepta archivos pdf.',
            'file_titulo.max' => 'Tamaño maximo de archivo no mayor 1M.',

            'grado_tipo.*.required' => 'El campo tipo es obligatorio',
            'grado_programa.*.required' => 'El campo es obligatorio',
            'grado_archivo.*.max' => 'Tamaño maximo de archivo no mayor 1M.',
            'grado_archivo.*.required' => 'El archivo es obligatorio.',

            'file_experiencia.max' => 'Tamaño maximo de archivo no mayor 3M.',
            'file_experiencia.required_with' => '* El campo archivo es obligatorio.',

            'capacitacion_tipo.*.required' => 'El campo tipo es obligatorio',
            'capacitacion_titulo.*.required' => 'El campo título es obligatorio',
            'capacitacion_archivo.*.max' => 'Tamaño maximo de archivo no mayor 1M.',
            'capacitacion_archivo.*.required' => 'El archivo es obligatorio.',

            'produccion_tipo.*.required' => 'El campo tipo es obligatorio',
            'produccion_titulo.*.required' => 'El campo título es obligatorio',
            'produccion_archivo.*.max' => 'Tamaño maximo de archivo no mayor 1M.',
            'produccion_archivo.*.required' => 'El archivo es obligatorio.',

            // 'produccion_archivo.*.max' =>'Tamaño maximo de archivo no mayor 1M.',
            // 'grado_archivo.*.max' =>'Tamaño maximo de archivo no mayor 1M.',

        ]);
        // dd($request);
        $periodo = Periodo::where('estado', '1')->first();
        $docenteExiste = Docente::where("nro_documento", $request->nro_documento)->first();

        if ($docenteExiste != NULL) {
            $docenteInscrito = InscripcionDocente::where("docentes_id", $docenteExiste->id)->where("periodos_id", $periodo->id)->first();

            if ($docenteInscrito != NULL) {
                $response["message"] = 'Usted ya se ha inscrito en el periodo vigente.';
                $response["status"] = false;
                $response["error"] = "";
                $response["url"] = "";
                return $response;
            }
        }
        // var_dump($docenteInscrito);
        // return 0;|
        DB::beginTransaction();
        try {

            if ($docenteExiste != NULL) {
                $docente = Docente::find($docenteExiste->id);
                // $docente->nombres = $request->nombres;
                // $docente->paterno = $request->paterno;
                // $docente->materno = $request->materno;
                // $docente->nro_documento = $request->nro_documento;
                $docente->condicion = $request->condicion;
                $docente->email = $request->email;
                $docente->celular = $request->celular;
                $docente->direccion = $request->direccion;
                $docente->codigo_unap = $request->codigo;
                $docente->tipo_trabajador = $request->tipo_trabajador;
                $docente->contrato = $request->contrato;
                $docente->ubigeos_id = $request->ubigeo;
                $docente->programas_id = $request->programa;
                $docente->grado_academicos_id = $request->grado;
                // $docente->tipo_documentos_id = $request->tipo_documento;
                $docente->foto = $this->save_image($request->foto);
                $docente->ruc = $request->ruc;
                $docente->nombre_banco = $request->nombre_banco;
                $docente->cci = $request->cci;
                $docente->dicto = $request->dicto;
                $docente->exp_didactica = $request->exp_didactica;
                $docente->save();
            } else {
                $docente = new Docente;
                $docente->nombres = mb_strtoupper($request->nombres);
                $docente->paterno = mb_strtoupper($request->paterno);
                $docente->materno = mb_strtoupper($request->materno);
                $docente->nro_documento = $request->nro_documento;
                $docente->condicion = $request->condicion;
                $docente->email = $request->email;
                $docente->celular = $request->celular;
                $docente->direccion = $request->direccion;
                $docente->codigo_unap = $request->codigo;
                $docente->tipo_trabajador = $request->tipo_trabajador;
                $docente->contrato = $request->contrato;
                $docente->ubigeos_id = $request->ubigeo;
                $docente->programas_id = 1;
                $docente->grado_academicos_id = 2;
                $docente->tipo_documentos_id = $request->tipo_documento;
                $docente->foto = $this->save_image($request->foto);
                $docente->ruc = $request->ruc;
                $docente->nombre_banco = $request->nombre_banco;
                $docente->cci = $request->cci;
                $docente->dicto = $request->dicto;
                $docente->exp_didactica = $request->exp_didactica;
                $docente->save();
            }


            $inscripcion = new InscripcionDocente;
            $inscripcion->docentes_id = $docente->id;
            $inscripcion->periodos_id = $periodo->id;
            $inscripcion->areas_id = $request->area;
            $inscripcion->modalidad = $request->modalidad;
            $inscripcion->save();

            if ($request->programa_titulo != NULL && $request->file_titulo != NULL) {

                $pdf_grado = $this->save_file("grado", $request->file_titulo, $request->file('file_titulo')->getClientOriginalExtension(), 'inscripcion');
                $grado = new AdjuntoGrado;
                $grado->path = $pdf_grado;
                $grado->grado_academicos_id = 2;
                $grado->programas_id = $request->programa_titulo;
                $grado->inscripcion_docentes_id = $inscripcion->id;
                $grado->save();
            }

            //Grados
            if (isset($request->grado_tipo)) {
                foreach ($request->grado_tipo as $key => $value) {
                    $pdf_grado = $this->save_file("grado", $request->grado_archivo[$key], 'pdf', 'inscripcion');
                    $grado = new AdjuntoGrado;
                    $grado->path = $pdf_grado;
                    $grado->grado_academicos_id = $value;
                    $grado->programas_id = $request->grado_programa[$key];
                    $grado->inscripcion_docentes_id = $inscripcion->id;
                    $grado->save();
                }
            }

            //Experiencias
            if ($request->experiencia != NULL && $request->file_experiencia != NULL) {
                $pdf_experiencia = $this->save_file("experiencia", $request->file_experiencia, $request->file('file_experiencia')->getClientOriginalExtension(), 'inscripcion');
                $experiencia = new AdjuntoExperiencia;
                $experiencia->path = $pdf_experiencia;
                $experiencia->experiencias_id = $request->experiencia;
                $experiencia->inscripcion_docentes_id = $inscripcion->id;
                $experiencia->save();
            }
            //Capacitaciones
            if (isset($request->capacitacion_tipo)) {
                foreach ($request->capacitacion_tipo as $key => $value) {
                    $pdf_capacitacion = $this->save_file("capacitacion", $request->capacitacion_archivo[$key], 'pdf', 'inscripcion');
                    $capacitacion = new AdjuntoCapacitaciones;
                    $capacitacion->titulo = $request->capacitacion_titulo[$key];
                    $capacitacion->path = $pdf_capacitacion;
                    $capacitacion->capacitacion_tipos_id = $value;
                    $capacitacion->inscripcion_docentes_id = $inscripcion->id;
                    $capacitacion->save();
                }
            }
            if (isset($request->produccion_tipo)) {
                foreach ($request->produccion_tipo as $key => $value) {
                    $pdf_produccion = $this->save_file("produccion", $request->produccion_archivo[$key], 'pdf', 'inscripcion');
                    $produccion = new AdjuntoProducciones;
                    $produccion->titulo = $request->produccion_titulo[$key];
                    $produccion->path = $pdf_produccion;
                    $produccion->producciones_id = $value;
                    $produccion->inscripcion_docentes_id = $inscripcion->id;
                    $produccion->save();
                }
            }

            foreach ($request->cursos as $key => $value) {
                $inscripcionCurso =  new InscripcionCurso;
                $inscripcionCurso->inscripcion_docentes_id = $inscripcion->id;
                $inscripcionCurso->curricula_detalles_id = $value;
                $inscripcionCurso->save();
            }

            foreach ($request->disponibilidad as $key => $value) {

                $ids = explode("-", $value);

                $disponibilidad =  new Disponibilidades;

                if (((int)$ids[2]) == $request->prioridad) {
                    $disponibilidad->prioridad = "1";
                } else {
                    $disponibilidad->prioridad = "2";
                }
                $disponibilidad->dia = $ids[0];
                $disponibilidad->plantilla_horarios_id = $ids[1];
                $disponibilidad->sedes_id = $ids[2];
                $disponibilidad->inscripcion_docentes_id = $inscripcion->id;
                $disponibilidad->save();
            }

            DB::commit();
            $response["message"] = 'Inscripción realizada con éxito.';
            $response["status"] = true;
            $response["error"] = '';
            $response["url"] = url("inscripciones/docentes/" . Crypt::encryptString($inscripcion->id));
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al inscribirse, intentelo nuevamante.';
            $response["status"] =  false;
            $response["error"] =  $e;
            $response["url"] =  "";
        }

        // $response = array(
        //     "message" => $message,
        //     "status" => $status,
        //     "url" => $url,
        //     "error" => $error
        // );
        // }

        // return response()->json($response);
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $inscripcion = InscripcionDocente::where('id', $id)->first();

        $response = array(
            "status" => empty($inscripcion) ? false : true,
            "id" => Crypt::encryptString($id),
            "tipo" => empty($inscripcion) ? false : $inscripcion->estado,
        );
        return view('web.inscripcion.docente-inscrito', $response);
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
        //
    }

    public function pdf($id)
    {
        $id = Crypt::decryptString($id);
        // return $id;
        // $periodo = Periodo::where("estado","1")->first();
        // $inscripcion =  InscripcionDocente::find($id);
        $inscripcion = DB::table("inscripcion_docentes as i")
            ->select(
                "i.id",
                "i.periodos_id",
                "i.docentes_id",
                DB::raw("DATE_FORMAT(i.created_at,'%d/%m/%Y %h:%i %p') as Fecha")
                // "i.estado"
            )
            // ->join("sedes as s","s.id","i.sedes_id")
            // ->join("areas as a","a.id","i.areas_id")
            // ->join("turnos as t","t.id","i.turnos_id")
            ->where("i.id", $id)
            ->first();
        $inscripcion = InscripcionDocente::select(
            "id",
            "periodos_id",
            "docentes_id",
            DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %h:%i %p') as Fecha")
        )
            ->where("id", $id)
            ->first();
        $formacion = AdjuntoGrado::with(["gradoAcademico", "programa"])->where("inscripcion_docentes_id", $inscripcion->id)->get();
        $docente = Docente::with(["tipoDocumento", "gradoAcademico", "programa", "ubigeo"])->find($inscripcion->docentes_id);
        $periodo = Periodo::find($inscripcion->periodos_id);
        $exp = AdjuntoExperiencia::with("experiencia")->where("inscripcion_docentes_id", $id)->first();
        $sedes = Disponibilidades::with("sede")->select("sedes_id", "prioridad")->where("inscripcion_docentes_id", $id)
            ->groupBy("sedes_id", "prioridad")->get();

        // dd($docente->exp_didactica);
        $areas = DB::table("inscripcion_cursos as ic")
            ->select(
                "c.areas_id",
                "a.denominacion"
            )
            ->join("curricula_detalles as cd", "cd.id", "ic.curricula_detalles_id")
            ->join("curriculas as c", "c.id", "cd.curriculas_id")
            ->join("areas as a", "a.id", "c.areas_id")
            ->where("ic.inscripcion_docentes_id", $id)
            ->groupBy("c.areas_id")
            ->get();

        $cursos = InscripcionCurso::with("curso")
            ->where("inscripcion_docentes_id", $id)
            ->get();
        // dd($cursos);

        $cursosAreas = [];
        $curricula = Curricula::with("area")->where("estado", "1")->get();
        // $areaDisponibilidad = Area::get();
        foreach ($curricula as $key => $value) {
            $objeto = new \stdClass;
            $objeto->area = $value->area->denominacion;
            $objeto->cursos = DB::table("inscripcion_cursos as ic")
                ->select(
                    "c.denominacion"
                )
                ->leftJoin("curricula_detalles as cd", "cd.id", "ic.curricula_detalles_id")
                ->leftJoin("cursos as c", "c.id", "cd.cursos_id")
                ->where("ic.inscripcion_docentes_id", $id)
                ->where("cd.curriculas_id", $value->id)
                ->get();
            $cursosAreas[] = $objeto;
        }
        // dd($cursosAreas);
        $turno = Turno::get();
        $horario = [];
        foreach ($turno as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->turno = $value->denominacion;

            $plantillaHorario = [];
            $plantilla = PlantillaHorario::select(
                "id",
                DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
                DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
                "tipo"
            )
                ->where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $obj = new \stdClass;
                $obj->hora_inicio = $val->horaInicio;
                $obj->hora_fin = $val->horaFin;
                $obj->tipo = $val->tipo;
                $obj->disponibilidad = Disponibilidades::with(['sede'])
                    ->where("inscripcion_docentes_id", $id)
                    ->where("plantilla_horarios_id", $val->id)
                    ->orderBy("dia", "asc")
                    ->get();
                $plantillaHorario[] = $obj;
            }
            $objeto->disponibilidad = $plantillaHorario;
            $horario[] =  $objeto;
        }


        $pdf = new PDF();
        $pdf::SetMargins(10, 35, 10);
        PDF::setFooterCallback(function ($pdf) use ($periodo, $inscripcion) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 10, 'CEPREUNA ' . $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo . ' - Fecha y Hora de Registro: ' . $inscripcion->Fecha, "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 10, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf->Image('images/logo.png', 175, 6, 25, 25, 'PNG', '', '', false, 140, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Preuniversitario", 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 6, 'Jr Acora #235', 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Inscripcion');
        $pdf::AddPage();
        $pdf::Image(Storage::disk('fotos')->path($docente->foto), 156, 52, 44, 52, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);


        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 14);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 18, 'FICHA DE PREINSCRIPCIÓN DOCENTE CEPREUNA CICLO ' . $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo, 0, 'C', 0, 0, '', '', true);

        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(140, 6, 'DATOS DEL POSTULANTE', 1, 1, 'C', 0, '', 0);
        // **********

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'TIPO DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->tipoDocumento->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->nro_documento, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'APELLIDO PATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->paterno, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'APELLIDO MATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->materno, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'NOMBRES:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->nombres, 0, 1, 'L', 0, '', 1);

        // ********
        if ($docente->condicion == '2') {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, "UNAP", 0, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CODIGO UNAP:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, $docente->codigo_unap, 0, 1, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'TIPO TRABAJADOR:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, $docente->tipo_trabajador == "1" ? "ADMINISTRATIVO" : "DOCENTE", 0, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONTRATO:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, $docente->contrato == "1" ? "CONTRATO" : "NOMBRADO", 0, 1, 'L', 0, '', 1);
        } else {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, "PARTICULAR", 0, 1, 'L', 0, '', 1);
        }
        // ********
        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::Cell(30, 6, 'GRADO ACADEMICO:', 0, 0, 'L', 0, '', 1);
        // $pdf::SetFont('helvetica', '', 8);
        // $pdf::Cell(40, 6, $docente->gradoAcademico->denominacion, 0, 0, 'L', 0, '', 1);

        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::Cell(30, 6, 'ESPECIALIDAD:', 0, 0, 'L', 0, '', 1);
        // $pdf::SetFont('helvetica', '', 8);
        // $pdf::Cell(40, 6, $docente->programa->denominacion, 0, 1, 'L', 0, '', 1);

        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'EMAIL:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->email, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'CELULAR:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->celular, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DEPARTAMENTO R.:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->ubigeo->departamento, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'RUC:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->ruc, 0, 1, 'L', 0, '', 1);


        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'PROVINCIA R.:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->ubigeo->provincia, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'CCI:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->cci, 0, 1, 'L', 0, '', 1);










        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DISTRITO R.:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->ubigeo->distrito, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DIRECCIÓN R.:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->direccion, 0, 1, 'L', 0, '', 1);



        $pdf::ln();
        $pdf::ln();
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'FORMACIÓN ACADÉMICA', 1, 1, 'C', 0, '', 0);
        // ********
        foreach ($formacion as $key => $value) {
            # code..
            // if ($value->grado_academicos_id == 2) {
            // $pdf::SetFont('helvetica', 'b', 8);
            // $pdf::Cell(30, 6, $value->gradoAcademico->denominacion, 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(40, 6, 'PROFESION: ', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(70, 6, $value->programa->denominacion, 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(40, 6, '', 0, 1, 'L', 0, '', 1);
            // } else {
            // $pdf::SetFont('helvetica', '', 8);
            // $pdf::Cell(30, 6, 'GRADO ACADÉMICO:', 0, 0, 'L', 0, '', 1);
            // $pdf::SetFont('helvetica', 'b', 8);
            // $pdf::Cell(40, 6, $value->gradoAcademico->denominacion, 0, 0, 'L', 0, '', 1);

            // $pdf::SetFont('helvetica', '', 8);
            // $pdf::Cell(30, 6, 'ESPECIALIDAD:', 0, 0, 'L', 0, '', 1);
            // $pdf::SetFont('helvetica', 'b', 8);
            // $pdf::Cell(40, 6, $value->programa->denominacion, 0, 1, 'L', 0, '', 1);
            // }
        }
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(40, 6, 'Experiencia en CEPREUNA: ', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        if ($exp != null) {
            $pdf::Cell(30, 6, $exp->experiencia->denominacion, 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(40, 6, '', 0, 1, 'L', 0, '', 1);
        } else {
            $pdf::Cell(30, 6, 'ninguna', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(40, 6, '', 0, 1, 'L', 0, '', 1);
        }

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(60, 6, 'Experiencia en didáctica, metodología y/o pedagogía: ', 0, 0, 'L', 0, '', 1);
        $pdf::Cell(30, 6, $docente->exp_didactica, 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(40, 6, '', 0, 1, 'L', 0, '', 1);

        // ***********************DATOS DE POSTULACION****************
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'DATOS DE POSTULACIÓN', 1, 1, 'C', 0, '', 0);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(60, 6, 'SEDE:', 0, 0, 'L', 0, '', 1);


        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(60, 6, 'AREA:', 0, 1, 'L', 0, '', 1);

        $sedesPostulacion = "";
        foreach ($sedes as $key => $value) {
            $sedesPostulacion .= " | " . $value->sede->denominacion;
        }
        $areaPostulacion = "";
        foreach ($areas as $key => $value) {
            $areaPostulacion .= " | " . $value->denominacion;
        }
        $cursoPostulacion = "";
        foreach ($cursos as $key => $value) {
            if ($value->curso != null)
                $cursoPostulacion .= " | " . $value->curso->denominacion;
        }
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(60, 6, $sedesPostulacion, 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(60, 6, $areaPostulacion, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(190, 6, 'CURSO:', 0, 1, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::MultiCell(190, 6, $cursoPostulacion, 0, 'L', 0, 0, '', '', true);

        // ******************************************************************
        // |        FORMATO DE DISPONIBILIDAD
        // ******************************************************************

        $y = $pdf::GetY();
        // $pdf::SetY($y+16);
        $pdf::SetXY(10, $y + 20);
        $pdf::SetFont('helvetica', '', 9);
        $text = 'Nota: LA PRESENTE FICHA DE INSCRIPCIÓN TIENE VALOR LEGAL DE UNA DECLARACIÓN JURADA Esta inscripción no es completa si no ha sido CONFIRMADA por el CEPREUNA, por favor acercarse a la sede central del CEPREUNA, portando una copia de DNI.';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $pdf::AddPage();
        $y = $pdf::GetY();
        $pdf::SetY($y);
        $pdf::SetFont('helvetica', 'b', 14);
        $pdf::Cell(0, 6, 'FORMATO DE DISPONIBILIDAD DE HORARIO', 0, 1, 'C', 0, '', 0);
        $pdf::ln();
        // *******
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'APELLIDOS Y NOMBRES:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(65, 6,  $docente->paterno . " " . $docente->materno . " " . $docente->nombres, 1, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DOCUMENTO:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(65, 6,  $docente->nro_documento, 1, 1, 'L', 0, '', 1);
        // *******
        if ($docente->condicion == '2') {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 6, 'UNAP', 1, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CODIGO UNAP:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 6, $docente->codigo_unap, 1, 1, 'L', 0, '', 1);
        } else {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(160, 6, 'PARTICULAR', 1, 1, 'L', 0, '', 1);
        }
        // *******
        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::Cell(40, 6, 'APELLIDOS Y NOMBRES:', 1, 0, 'L', 0, '', 1);
        // $pdf::SetFont('helvetica', '', 8);
        // $pdf::Cell(150, 6, ' ' . $docente->paterno . " " . $docente->materno . " " . $docente->nombres, 1, 1, 'L', 0, '', 1);
        // *******
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(40, 6, 'N° CELULAR Y EMAIL:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(75, 6, $docente->celular, 1, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(75, 6, $docente->email, 1, 1, 'L', 0, '', 1);

        // // ***********************CRUSOS****************
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'CURSOS A LOS CUALES POSTULA', 'B', 1, 'L', 0, '', 0);

        $y = $pdf::GetY();
        $x = $pdf::GetX();

        $y = $y + 5;

        $temp = $y;
        $mayor = $y;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[0]->area, 1, 'C', 0, 0, '', '', true);
        $y = $temp;
        foreach ($cursosAreas[0]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            $mayor = $y;
        }

        $x = $x + 65;
        $y = $temp;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[1]->area, 1, 'C', 0, 0, '', '', true);
        foreach ($cursosAreas[1]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            if ($mayor < $y) {
                $mayor = $y;
            }
        }

        $x = $x + 65;
        $y = $temp;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[2]->area, 1, 'C', 0, 0, '', '', true);
        foreach ($cursosAreas[2]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            if ($mayor < $y) {
                $mayor = $y;
            }
        }
        $pdf::SetY($mayor);

        $pdf::ln();
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'SEDES A LOS CUALES POSTULA', 'B', 1, 'L', 0, '', 0);

        $pdf::ln();
        $pdf::SetFont('helvetica', '', 11);

        $html = '<ul>';
        foreach ($sedes as $key => $value) {
            $html .= '<li><span style="font-weight: bold;">' . $value->sede->denominacion . '</span> ' . ($value->prioridad == '1' ? 'principal' : '') . '</li>';
        }
        $html .= "</ul>";

        $pdf::writeHTML($html, true, false, true, false, '');

        $pdf::ln(2);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'DISPONIBILIDAD', 'B', 1, 'L', 0, '', 0);


        foreach ($horario as $key => $value) {
            // $pdf::ln();
            $pdf::SetFont('helvetica', 'b', 10);
            $pdf::Cell(190, 6, $value->turno, 0, 1, 'C', 0, '', 0);

            $pdf::SetFont('helvetica', '', 10);
            $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                        <thead>
                            <tr style="font-weight: bold;">
                            <td width="115"  align="center">Hora</td>
                            <td width="85"  align="center">Lunes</td>
                            <td width="85"  align="center">Martes</td>
                            <td width="85"  align="center">Miercoles</td>
                            <td width="85"  align="center">Jueves</td>
                            <td width="85"  align="center">viernes</td>
                            </tr>
                        </thead>';

            $tabla .= '<tbody>';
            foreach ($value->disponibilidad as $k => $val) {
                $tabla .= '<tr>
                            <td width="115"  align="center">' . $val->hora_inicio . ' - ' . $val->hora_fin . '</td>';
                for ($i = 1; $i <= 5; $i++) {
                    $control = false;
                    foreach ($val->disponibilidad as $v) {
                        if ($v->dia == (string)$i) {
                            $tabla .= ' <td width="85"  align="center">' . $v->sede->denominacion . '</td>';
                            $control = true;
                        }
                    }
                    if (!$control) {
                        $tabla .= ' <td width="85"  align="center"></td>';
                    }
                }

                $tabla .= '</tr>';
            }
            $tabla .= '</tbody></table>';
            // $html = '<ul><li>lista 1</li></ul>';
            $pdf::writeHTML($tabla, true, false, true, false, 'C');
        }

        $pdf::AddPage();
        $y = $pdf::GetY();
        $pdf::SetY($y);
        $pdf::SetFont('helvetica', 'b', 14);
        $pdf::Cell(0, 6, 'DECLARACIÓN JURADA DEL DOCENTE', 0, 1, 'C', 0, '', 0);
        $pdf::ln();

        $y += 16;
        $x = 20;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Yo, ' . $docente->paterno . ' ' . $docente->materno . ', ' . $docente->nombres . ' ' . ' identificado(a) con  DNI N° ' . $docente->nro_documento . ', ' . 'domiciliado(a) en ' . $docente->direccion . ', del distrito de ' . $docente->ubigeo->distrito . ', provincia de ' . $docente->ubigeo->provincia . ' y departamento de ' . $docente->ubigeo->departamento . ', mediante la presente DECLARO BAJO JURAMENTO lo siguiente:';
        $pdf::MultiCell(170, 6, $text, 0, 'J', 0, 0, '', '', true);

        // $y += 20;
        // $pdf::SetXY($x, $y);
        // $pdf::SetFont('helvetica', '', 12);
        // $text = 'Que, el material brindado por el CEPREUNA NO SERÁ PROPORCIONADO O UTILIZADO EN OTRAS ACADEMIAS y a la fecha de suscripción del presente cuento con servicio de internet a domicilio garantizando el dictado de clases virtuales durante el presente ciclo.';
        // $pdf::MultiCell(190, 6, $text, 0, 'J', 0, 0, '', '', true);

        $y += 22;
        $x += 5;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '1. Declaro que no he abandonado ni me he retirado de ciclos anteriores en el', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '   Centro de Estudios Preuniversitario de la Universidad Nacional del Altiplano', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '     (CEPREUNA).', 0, 'L', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '2. Me comprometo a cumplir con la permanencia y asistencia durante las dieciséis', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '   (16) semanas académicas del ciclo en curso, o las semanas asignadas, salvo', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '   casos debidamente justificados y aprobados por la Comisión del CEPREUNA.', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '3. Soy consciente de que el incumplimiento de este compromiso puede derivar en', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, ' sanciones administrativas, incluyendo la inhabilitación para futuras', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '     convocatorias como docente en la institución.', 0, 'L', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '4. Me comprometo a presentar los informes, requisitos y otros en la fecha', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '   establecida por la oficina de administración; en caso contrario, el trámite se', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '      postergará para el siguiente pago, sin derecho a reclamo alguno.', 0, 'L', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '5. Declaro que el material proporcionado por el CEPREUNA no será compartido ni', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '    utilizado en otras academias.', 0, 'L', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '6. En caso de tener carga horaria en la modalidad virtual, garantizo contar con', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '   servicio de internet en mi domicilio para asegurar el correcto desarrollo de las', 0, 'J', 0, 1, '', '', true);
        $pdf::SetX($x + 5);
        $pdf::MultiCell(160, 6, '     clases.', 0, 'L', 0, 1, '', '', true);
        // $pdf::SetX($x + 5);
        // $pdf::MultiCell(160, 6, '7. Afirmo que la información proporcionada en esta declaración es veraz y que cualquier falsedad será de mi entera responsabilidad, sujetándome a las disposiciones legales y', 0, 'J', 0, 0, '', '', true);
        // $pdf::SetX($x + 5);
        // $pdf::SetY($pdf::GetY() + 10);
        // $pdf::SetXY($x + 5, $pdf::GetY());
        // $pdf::MultiCell(160, 6, 'reglamentarias correspondientes.', 0, 'L', 0, 0, '', '', true);


        // $y += 30;
        // $pdf::SetXY($x, $y);
        // $pdf::SetFont('helvetica', '', 12);
        // $text = 'Experiencia de conocer y manejar las plataformas de educación y los programas de Microsoft Office de acuerdo al siguiente cuadro:';
        // $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y = $pdf::GetY();
        $pdf::SetY($y + 25);

        // $text = '<table cellspacing="0" cellpadding="2" border="1">';
        // $text .= "<thead>";
        // $text .= '<tr align="center">';
        // $text .= "<td>Plataforma</td>";
        // $text .= "<td>Marca(X)</td>";
        // $text .= "<td>Microsoft Office</td>";
        // $text .= "<td>Basico</td>";
        // $text .= "<td>Intermedio</td>";
        // $text .= "<td>Avanzado</td>";
        // $text .= "</tr>";
        // $text .= "</thead>";
        // $text .= "<tbody>";
        // $text .= '<tr>';
        // $text .= "<td>Moodle</td>";
        // $text .= "<td></td>";
        // $text .= "<td>Excel</td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "</tr>";
        // $text .= '<tr>';
        // $text .= "<td>Classroom</td>";
        // $text .= "<td></td>";
        // $text .= "<td>Word</td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "</tr>";
        // $text .= '<tr>';
        // $text .= "<td>G-suite</td>";
        // $text .= "<td></td>";
        // $text .= "<td>Power Point</td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "</tr>";
        // $text .= '<tr>';
        // $text .= "<td>Herramientas para la enseñanza virtual</td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "<td></td>";
        // $text .= "</tr>";
        // $text .= "</tbody>";
        // $text .= "</table>";

        // $pdf::writeHTML($text, true, false, true, false, 'J');

        // $y+=30;
        // $y = $pdf::GetY();
        // $pdf::SetY($y+16);
        $pdf::SetXY($x - 5, $y + 10);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'La información proporcionada es verdadera y autorizo la verificación de lo declarado. En caso de falsedad declaro haber incurrido en delito contra la Fe Pública y autorizo mi';
        $pdf::MultiCell(170, 6, $text, 0, 'J', 0, 0, '', '', true);
        $y = $pdf::GetY();
        $pdf::SetXY($x - 5, $y + 10);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'sanción administrativa.';
        $pdf::MultiCell(170, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y = $pdf::GetY();
        $y += 15;
        $pdf::SetXY($x - 5, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Es cuanto declaro en honor a la verdad, para los fines pertinentes.';
        $pdf::MultiCell(170, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y += 15;
        $pdf::SetXY($x - 5, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Puno, ……….. de……………………………202….';
        $pdf::MultiCell(170, 6, $text, 0, 'R', 0, 0, '', '', true);

        $y += 10;
        $pdf::SetXY(160, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "";
        $pdf::MultiCell(30, 30, $text, 1, 'C', 0, 0, '', '', true);

        $y += 30;
        $pdf::SetXY(85, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "............................. Firma";
        $pdf::MultiCell(40, 6, $text, 0, 'C', 0, 0, '', '', true);

        $y += 0;
        $pdf::SetXY(160, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "Huella Digital";
        $pdf::MultiCell(30, 6, $text, 1, 'C', 0, 0, '', '', true);

        // $y+=50;


        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('inscripcion.pdf', 'I');
    }

    public function save_image($base64_image)
    {

        $img = $this->getB64Image($base64_image);

        $ldate = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $img_name = $first . $ldate . '.png';

        Storage::disk('fotos')->put($img_name, $img);
        // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
        return $img_name;
    }
    public function getB64Image($base64_image)
    {

        $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
        $image = base64_decode($image_service_str);

        return $image;
    }
    public function save_file($tipo, $file, $extension, $disk)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = $tipo . '-' . $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk($disk)->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
    public function download_pdf()
    {
        $docentes = InscripcionDocente::get();
        foreach ($docentes as $value) {
            $this->pdf_temporal($value->id);
        }
        // dd($docentes);
    }
    public function pdf_temporal($id)
    {
        // $id = Crypt::decryptString($id);
        // return $id;
        // $periodo = Periodo::where("estado","1")->first();
        // $inscripcion =  InscripcionDocente::find($id);
        $inscripcion = DB::table("inscripcion_docentes as i")
            ->select(
                "i.id",
                "i.periodos_id",
                "i.docentes_id",
                DB::raw("DATE_FORMAT(i.created_at,'%d/%m/%Y %h:%i %p') as Fecha")
                // "i.estado"
            )
            // ->join("sedes as s","s.id","i.sedes_id")
            // ->join("areas as a","a.id","i.areas_id")
            // ->join("turnos as t","t.id","i.turnos_id")
            ->where("i.id", $id)
            ->first();
        $inscripcion = InscripcionDocente::select(
            "id",
            "periodos_id",
            "docentes_id",
            DB::raw("DATE_FORMAT(created_at,'%d/%m/%Y %h:%i %p') as Fecha")
        )
            ->where("id", $id)
            ->first();
        $docente = Docente::with(["tipoDocumento", "gradoAcademico", "programa"])->find($inscripcion->docentes_id);
        // dd($docente);
        $periodo = Periodo::find($inscripcion->periodos_id);
        $sedes = Disponibilidades::with("sede")->select("sedes_id", "prioridad")->where("inscripcion_docentes_id", $id)
            ->groupBy("sedes_id", "prioridad")->get();


        $areas = DB::table("inscripcion_cursos as ic")
            ->select(
                "c.areas_id",
                "a.denominacion"
            )
            ->join("curricula_detalles as cd", "cd.id", "ic.curricula_detalles_id")
            ->join("curriculas as c", "c.id", "cd.curriculas_id")
            ->join("areas as a", "a.id", "c.areas_id")
            ->where("ic.inscripcion_docentes_id", $id)
            ->groupBy("c.areas_id")
            ->get();

        $cursos = InscripcionCurso::with("curso")
            ->where("inscripcion_docentes_id", $id)
            ->get();
        // dd($cursos);

        $cursosAreas = [];
        $curricula = Curricula::with("area")->where("estado", "1")->get();
        // $areaDisponibilidad = Area::get();
        foreach ($curricula as $key => $value) {
            $objeto = new \stdClass;
            $objeto->area = $value->area->denominacion;
            $objeto->cursos = DB::table("inscripcion_cursos as ic")
                ->select(
                    "c.denominacion"
                )
                ->leftJoin("curricula_detalles as cd", "cd.id", "ic.curricula_detalles_id")
                ->leftJoin("cursos as c", "c.id", "cd.cursos_id")
                ->where("ic.inscripcion_docentes_id", $id)
                ->where("cd.curriculas_id", $value->id)
                ->get();
            $cursosAreas[] = $objeto;
        }
        // dd($cursosAreas);
        $turno = Turno::get();
        $horario = [];
        foreach ($turno as $key => $value) {
            $objeto = new \stdClass;
            $objeto->id = $value->id;
            $objeto->turno = $value->denominacion;

            $plantillaHorario = [];
            $plantilla = PlantillaHorario::select(
                "id",
                DB::raw("DATE_FORMAT(hora_inicio,'%H:%i') as horaInicio"),
                DB::raw("DATE_FORMAT(hora_fin,'%H:%i') as horaFin"),
                "tipo"
            )
                ->where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $obj = new \stdClass;
                $obj->hora_inicio = $val->horaInicio;
                $obj->hora_fin = $val->horaFin;
                $obj->tipo = $val->tipo;
                $obj->disponibilidad = Disponibilidades::with(['sede'])
                    ->where("inscripcion_docentes_id", $id)
                    ->where("plantilla_horarios_id", $val->id)
                    ->orderBy("dia", "asc")
                    ->get();
                $plantillaHorario[] = $obj;
            }
            $objeto->disponibilidad = $plantillaHorario;
            $horario[] =  $objeto;
        }


        $pdf = new PDF();
        $pdf::reset();
        $pdf::SetMargins(10, 35, 10);
        PDF::setFooterCallback(function ($pdf) use ($periodo, $inscripcion) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 10, 'CEPREUNA ' . $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo . ' - Fecha y Hora de Registro: ' . $inscripcion->Fecha, "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        PDF::setHeaderCallback(function ($pdf) {
            $pdf->SetY(10);
            $pdf->Image('images/UNAPUNO.png', 10, 6, 20, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
            $pdf::Image('images/logo.png', 175, 6, 25, 25, 'PNG', '', '', false, 140, '', false, false, 0, false, false, false);
            $pdf->SetFont('helvetica', 'b', 14);
            $pdf->Cell(0, 6, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', 'b', 12);
            $pdf->Cell(0, 6, "Centro de Estudios Preuniversitario", 0, 1, 'C', 0, '', 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(0, 6, 'Jr Acora #235', 0, 1, 'C', 0, '', 0);
        });
        $pdf::SetTitle('Inscripcion');
        $pdf::AddPage();
        $pdf::Image(Storage::disk('fotos')->path($docente->foto), 156, 52, 44, 52, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);


        $y = $pdf::GetY();
        $pdf::SetY($y);

        $pdf::SetFont('helvetica', 'b', 14);
        // $pdf::Cell(0, 6, 'FICHA DE INSCRIPCION DOCENTE CEPREUNA CICLO '.$periodo->inicio_ciclo.' - '.$periodo->fin_ciclo, 0, 1, 'C', 0, '', 0);
        $pdf::MultiCell(0, 18, 'FICHA DE INSCRIPCIÓN DOCENTE CEPREUNA CICLO ' . $periodo->inicio_ciclo . ' - ' . $periodo->fin_ciclo, 0, 'C', 0, 0, '', '', true);

        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(140, 6, 'DATOS DEL POSTULANTE', 1, 1, 'C', 0, '', 0);
        // **********

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'TIPO DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->tipoDocumento->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->nro_documento, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'APELLIDO PATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->paterno, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'APELLIDO MATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->materno, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'NOMBRES:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->nombres, 0, 1, 'L', 0, '', 1);

        // ********
        if ($docente->condicion == '2') {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, "UNAP", 0, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CODIGO UNAP:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, $docente->codigo_unap, 0, 1, 'L', 0, '', 1);
        } else {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(40, 6, "PARTICULAR", 0, 1, 'L', 0, '', 1);
        }
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'GRADO ACADÉMICO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->gradoAcademico->denominacion, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'ESPECIALIDAD:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->programa->denominacion, 0, 1, 'L', 0, '', 1);

        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'EMAIL:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->email, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'CELULAR:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->celular, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DIRECCIÓN:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 6, $docente->direccion, 0, 0, 'L', 0, '', 1);

        // ***********************DATOS DE POSTULACION****************
        $pdf::ln();
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'DATOS DE POSTULACIÓN', 1, 1, 'C', 0, '', 0);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(60, 6, 'SEDE:', 0, 0, 'L', 0, '', 1);


        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(60, 6, 'AREA:', 0, 1, 'L', 0, '', 1);

        $sedesPostulacion = "";
        foreach ($sedes as $key => $value) {
            $sedesPostulacion .= " | " . $value->sede->denominacion;
        }
        $areaPostulacion = "";
        foreach ($areas as $key => $value) {
            $areaPostulacion .= " | ";
        }
        // $cursoPostulacion = "";
        // foreach ($cursos as $key => $value) {
        //     if($value->curso!=null)
        //     $cursoPostulacion.=" | ".$value->curso->denominacion;
        // }
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(60, 6, $sedesPostulacion, 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(60, 6, $areaPostulacion, 0, 1, 'L', 0, '', 1);
        // ********
        // $pdf::SetFont('helvetica', 'b', 8);
        // $pdf::Cell(190, 6, 'CURSO:', 0, 1, 'L', 0, '', 1);
        // $pdf::SetFont('helvetica', '', 8);
        // $pdf::MultiCell(190, 6, $cursoPostulacion, 0, 'L', 0, 0, '', '', true);

        // ******************************************************************
        // |        FORMATO DE DISPONIBILIDAD
        // ******************************************************************

        $y = $pdf::GetY();
        // $pdf::SetY($y+16);
        $pdf::SetXY(10, $y + 20);
        $pdf::SetFont('helvetica', '', 9);
        $text = 'Nota: LA PRESENTE FICHA DE INSCRIPCIÓN TIENE VALOR LEGAL DE UNA DECLARACIÓN JURADA Esta inscripción no es completa si no ha sido CONFIRMADA por el CEPREUNA, por favor acercarse a la sede central del CEPREUNA, portando una copia de DNI.';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $pdf::AddPage();
        $y = $pdf::GetY();
        $pdf::SetY($y);
        $pdf::SetFont('helvetica', 'b', 14);
        $pdf::Cell(0, 6, 'FORMATO DE DISPONIBILIDAD DE HORARIO', 0, 1, 'C', 0, '', 0);
        $pdf::ln();
        // *******
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'TIPO DOCUMENTO:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(65, 6, $docente->tipoDocumento->denominacion, 1, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(30, 6, 'DOCUMENTO:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(65, 6,  $docente->nro_documento, 1, 1, 'L', 0, '', 1);
        // *******
        if ($docente->condicion == '2') {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 6, 'UNAP', 1, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDIGO UNAP:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(65, 6, $docente->codigo_unap, 1, 1, 'L', 0, '', 1);
        } else {
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 6, 'CONDICIÓN:', 1, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(160, 6, 'PARTICULAR', 1, 1, 'L', 0, '', 1);
        }
        // *******
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(40, 6, 'APELLIDOS Y NOMBRES:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(150, 6, ' ' . $docente->paterno . " " . $docente->materno . " " . $docente->nombres, 1, 1, 'L', 0, '', 1);
        // *******
        $pdf::SetFont('helvetica', 'b', 8);
        $pdf::Cell(40, 6, 'N° CELULAR Y EMAIL:', 1, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(75, 6, $docente->celular, 1, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(75, 6, $docente->email, 1, 1, 'L', 0, '', 1);

        // // ***********************CRUSOS****************
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'CURSOS A LOS CUALES POSTULA', 'B', 1, 'L', 0, '', 0);

        $y = $pdf::GetY();
        $x = $pdf::GetX();

        $y = $y + 5;

        $temp = $y;
        $mayor = $y;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[0]->area, 1, 'C', 0, 0, '', '', true);
        $y = $temp;
        foreach ($cursosAreas[0]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            $mayor = $y;
        }

        $x = $x + 65;
        $y = $temp;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[1]->area, 1, 'C', 0, 0, '', '', true);
        foreach ($cursosAreas[1]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            if ($mayor < $y) {
                $mayor = $y;
            }
        }

        $x = $x + 65;
        $y = $temp;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::MultiCell(60, 5, $cursosAreas[2]->area, 1, 'C', 0, 0, '', '', true);
        foreach ($cursosAreas[2]->cursos as $key => $value) {
            $y += 5;
            $pdf::SetXY($x, $y);
            $pdf::SetFont('helvetica', '', 9);
            $pdf::MultiCell(60, 5, $value->denominacion, 1, 'C', 0, 0, '', '', true);
            if ($mayor < $y) {
                $mayor = $y;
            }
        }
        $pdf::SetY($mayor);

        $pdf::ln();
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'SEDES A LOS CUALES POSTULA', 'B', 1, 'L', 0, '', 0);

        $pdf::ln();
        $pdf::SetFont('helvetica', '', 11);

        $html = '<ul>';
        foreach ($sedes as $key => $value) {
            $html .= '<li><span style="font-weight: bold;">' . $value->sede->denominacion . '</span> ' . ($value->prioridad == '1' ? 'principal' : '') . '</li>';
        }
        $html .= "</ul>";

        $pdf::writeHTML($html, true, false, true, false, '');

        $pdf::ln(2);
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(190, 6, 'DISPONIBILIDAD', 'B', 1, 'L', 0, '', 0);


        foreach ($horario as $key => $value) {
            // $pdf::ln();
            $pdf::SetFont('helvetica', 'b', 10);
            $pdf::Cell(190, 6, $value->turno, 0, 1, 'C', 0, '', 0);

            $pdf::SetFont('helvetica', '', 10);
            $tabla = '<table cellspacing="0" cellpadding="2" border="1">
                        <thead>
                            <tr style="font-weight: bold;">
                            <td width="115"  align="center">Hora</td>
                            <td width="85"  align="center">Lunes</td>
                            <td width="85"  align="center">Martes</td>
                            <td width="85"  align="center">Miercoles</td>
                            <td width="85"  align="center">Jueves</td>
                            <td width="85"  align="center">viernes</td>
                            </tr>
                        </thead>';

            $tabla .= '<tbody>';
            foreach ($value->disponibilidad as $k => $val) {
                $tabla .= '<tr>
                            <td width="115"  align="center">' . $val->hora_inicio . ' - ' . $val->hora_fin . '</td>';
                for ($i = 1; $i <= 5; $i++) {
                    $control = false;
                    foreach ($val->disponibilidad as $v) {
                        if ($v->dia == (string)$i) {
                            $tabla .= ' <td width="85"  align="center">' . $v->sede->denominacion . '</td>';
                            $control = true;
                        }
                    }
                    if (!$control) {
                        $tabla .= ' <td width="85"  align="center"></td>';
                    }
                }

                $tabla .= '</tr>';
            }
            $tabla .= '</tbody></table>';
            // $html = '<ul><li>lista 1</li></ul>';
            $pdf::writeHTML($tabla, true, false, true, false, 'C');
        }

        $pdf::AddPage();
        $y = $pdf::GetY();
        $pdf::SetY($y);
        $pdf::SetFont('helvetica', 'b', 14);
        $pdf::Cell(0, 6, 'DECLARACIÓN JURADA DEL DOCENTE', 0, 1, 'C', 0, '', 0);
        $pdf::ln();

        $y += 16;
        $x = 10;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Yo, ' . $docente->nombres . ' ' . $docente->paterno . ' ' . $docente->materno . ' identificado ( a ) con  DNI N° ' . $docente->nro_documento . ', celular N° ' . $docente->celular . '.';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y += 10;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', 'B', 12);
        $text = 'Declaro bajo Juramento:';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y += 10;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Que, a la fecha de suscripción de la presente declaración jurada, cuento con servicio de internet en domicilio de ................. megabyte de velocidad, poseo equipo de cómputo de las siguientes características .............................................................................................................................................................................................................................
        ';
        $pdf::MultiCell(190, 6, $text, 0, 'J', 0, 0, '', '', true);

        $y += 30;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Experiencia de conocer y manejar las plataformas de educación y los programas de Microsoft Office de acuerdo al siguiente cuadro:';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y = $pdf::GetY();
        $pdf::SetY($y + 16);

        $text = '<table cellspacing="0" cellpadding="2" border="1">';
        $text .= "<thead>";
        $text .= '<tr align="center">';
        $text .= "<td>Plataforma</td>";
        $text .= "<td>Marca(X)</td>";
        $text .= "<td>Microsoft Office</td>";
        $text .= "<td>Basico</td>";
        $text .= "<td>Intermedio</td>";
        $text .= "<td>Avanzado</td>";
        $text .= "</tr>";
        $text .= "</thead>";
        $text .= "<tbody>";
        $text .= '<tr>';
        $text .= "<td>Moodle</td>";
        $text .= "<td></td>";
        $text .= "<td>Excel</td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "</tr>";
        $text .= '<tr>';
        $text .= "<td>Classroom</td>";
        $text .= "<td></td>";
        $text .= "<td>Word</td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "</tr>";
        $text .= '<tr>';
        $text .= "<td>G-suite</td>";
        $text .= "<td></td>";
        $text .= "<td>Power Point</td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "</tr>";
        $text .= '<tr>';
        $text .= "<td>Herramientas para la enseñanza virtual</td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "<td></td>";
        $text .= "</tr>";
        $text .= "</tbody>";
        $text .= "</table>";

        $pdf::writeHTML($text, true, false, true, false, 'J');

        // $y+=30;
        $y = $pdf::GetY();
        // $pdf::SetY($y+16);
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'La información proporcionada es verdadera y autorizo la verificación de lo declarado. En caso de falsedad declaro haber incurrido en delito contra la Fe Pública y autorizo mi sanción administrativa.';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y += 15;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Es cuanto declaro en honor a la verdad, para los fines pertinentes.';
        $pdf::MultiCell(190, 6, $text, 0, 'L', 0, 0, '', '', true);

        $y += 15;
        $pdf::SetXY($x, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = 'Puno, ……….. de……………………………202….';
        $pdf::MultiCell(190, 6, $text, 0, 'R', 0, 0, '', '', true);

        $y += 10;
        $pdf::SetXY(160, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "";
        $pdf::MultiCell(40, 40, $text, 1, 'C', 0, 0, '', '', true);

        $y += 40;
        $pdf::SetXY(85, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "............................. Firma";
        $pdf::MultiCell(40, 6, $text, 0, 'C', 0, 0, '', '', true);

        $y += 0;
        $pdf::SetXY(160, $y);
        $pdf::SetFont('helvetica', '', 12);
        $text = "Huella Digital";
        $pdf::MultiCell(40, 6, $text, 1, 'C', 0, 0, '', '', true);

        // $y+=50;


        $pdf::SetAutoPageBreak(TRUE, 0);
        $nombre_archivo = $docente->nro_documento . "-" . $docente->paterno . " " . $docente->materno . " " . $docente->nombres;
        $file = $pdf::Output('', 'S');
        // Storage::disk("inscripcion")->putFileAs('fichas', $file,$nombre_archivo.'.pdf');
        Storage::disk('inscripcion')->put('fichas/' . $nombre_archivo . '.pdf', $file);
        // $pdf::Output($nombre_archivo.'.pdf', 'D');
    }
}
