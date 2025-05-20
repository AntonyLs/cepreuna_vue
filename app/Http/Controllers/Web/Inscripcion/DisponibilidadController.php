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
use App\Models\DocentesDisponibilidad;
use App\Models\ConfiguracionInscripciones;
use DB;
use PDF;

class DisponibilidadController extends Controller
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
        $response["configuracion"] = ConfiguracionInscripciones::where([['tipo_usuario', 1], ['estado', '1']])->first();
        return view('web.inscripcion.estudiante-simulacro', $response);
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

    public function showLogin()
    {
        return view('web.docente.validacion');
    }
    public function login(Request $request)
    {


        $credentials = $request->only('email', 'password');

        //$docente = Docente::where('id',2406)->first();
        //$response['docente'] = json_encode($docente);  
        $docente = Docente::join('docente_aptos', 'docentes.id', '=', 'docente_aptos.docentes_id')
        ->select(
            'docentes.*',
            'docente_aptos.usuario',
        )
        ->where('docente_aptos.usuario', $credentials['email'])->first();
        //return view('web.docente.disponibilidad',$response);
        if ($docente) {

            // Verificar si se encontró el estudiante y la contraseña coincide
            if ($docente->usuario == $credentials['email'] && $docente->nro_documento == $credentials['password']) {
                
                //validacion
                $Existe = DocentesDisponibilidad::where('docentes_id', $docente->id)->first();
                if (!$Existe) {
                    return redirect()->back()->with('error', 'No se Encuenta Habilitado para corregir su Disponibilidad.');
                }
                if($Existe->edit == '1'){
                    return redirect()->back()->with('error', 'Usted actualizó su disponibilidad horaria.');
                }

                // para el formulario
                $response["configuracion"] = ConfiguracionInscripciones::where([['tipo_usuario', 1], ['estado', '1'], ['observacion', 'simulacro']])->first();
                $response['docente'] = json_encode($docente);
                return view('web.docente.disponibilidad',$response);
            }
        }

       return redirect()->back()->with('error', 'El correo o la contraseña ingresados son incorrectos. Por favor, verifica tus credenciales y vuelve a intentarlo.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //return $request;
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
            'prioridad' => 'required',

            'modalidad' => 'required',
            'area' => 'required',
            'cursos' => 'array|min:1',
            'sede' => 'array|min:1',
            'disponibilidad' => 'required|array|min:1',

            /**Datos Bancarios**/
            'nombre_banco' => 'required',
            'cci' => 'required|digits:20',
            //Dicto
            'dicto' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'digits' => 'El campo :attribute debe tener exactamente :digits dígitos.',
            'programa.required' => "* El campo Especialidad es obligatorio.",
            'email' => '* El campo :attribute no es un correo electronico.',
            'cursos.min' => '* Seleccionar minimo un Curso.',
            'sede.min' => '* Seleccionar minimo una Sede.',
            'disponibilidad.min' => '* Seleccionar minimo una Disponiblidad.',
            'codigo.required_if' => '* El campo :attribute es obligatorio.'

            // 'produccion_archivo.*.max' =>'Tamaño maximo de archivo no mayor 1M.',
            // 'grado_archivo.*.max' =>'Tamaño maximo de archivo no mayor 1M.',

        ]);
        //dd($request);
        $periodo = Periodo::where('estado', '1')->first();
        $docenteExiste = Docente::where("nro_documento", $request->nro_documento)->first();
       
        // error
        DB::beginTransaction();
        try {
            $docente = Docente::find($docenteExiste->id);
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
            //$docente->programas_id = 1;
            $docente->tipo_documentos_id = $request->tipo_documento;
            $docente->ruc = $request->ruc;
            $docente->nombre_banco = $request->nombre_banco;
            $docente->cci = $request->cci;
            $docente->dicto = $request->dicto;
            $docente->save();
            

             // Actualizar o crear la inscripción del docente en el periodo actual
             $inscripcion = InscripcionDocente::updateOrCreate(
                [
                    'docentes_id' => $docente->id,
                    'periodos_id' => $periodo->id
                ]
            );

            // Actualizar cursos asociados
            InscripcionCurso::where('inscripcion_docentes_id', $docenteExiste->id)->delete();
            foreach ($request->cursos as $curso) {
                InscripcionCurso::create([
                    'inscripcion_docentes_id' => $docenteExiste->id,
                    'curricula_detalles_id' => $curso,
                ]);
            }

            // Actualizar disponibilidades asociadas
            Disponibilidades::where('inscripcion_docentes_id', $docenteExiste->id)->delete();
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

            $docenteDisponibilidad = DocentesDisponibilidad::where('docentes_id', $docenteExiste->id)->first();
            $docenteDisponibilidad->edit = '1';
            $docenteDisponibilidad->save();

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
        $inscripcion = InscripcionSimulacro::where('id', $id)->first();
        $response = array(
            "status" => true,
            "id" => Crypt::encryptString($id),
            //"id" => 5239,
            "tipo" => empty($inscripcion) ? false : 1,
        );
        return view('web.inscripcion.simulacro-inscrito', $response);
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

    public function pdf($id_encrypt)
    {
        $id_simulacro = Crypt::decryptString($id_encrypt);
        $InscripcionSimulacro = DB::table('inscripcion_simulacros as i')
            ->select(
                "i.*",
                DB::raw("DATE_FORMAT(i.created_at,'%d/%m/%Y %h:%i %p') as Fecha")
            )
            ->where('id', $id_simulacro)->first();
        //$id = 5239;
        // $periodo = Periodo::where("estado","1")->first();
        if (!isset($InscripcionSimulacro)) {
            abort(401, "no existe la inscripcion");
        }

        $estudiante = DB::table("estudiantes as e")
            ->select(
                "e.*",
                "td.denominacion as TipoDocumento",
                "c.denominacion as Colegio",
                "p.denominacion as Pais",
                "u.departamento",
                "u.distrito",
                "u.provincia",
                "ub.departamento as departamenton",
                "ub.distrito as distriton",
                "ub.provincia as provincian",
                "a.paterno as Apaterno",
                "a.materno as Amaterno",
                "a.nombres as Anombres",
                "a.celular as Acelular",
                "pa.denominacion as Parentesco"
            )
            ->join("tipo_documentos as td", "td.id", "e.tipo_documentos_id")
            ->join("colegios as c", "c.id", "e.colegios_id")
            ->join("pais as p", "p.id", "e.pais_id")
            ->leftJoin("estudiante_apoderados as ea", "ea.estudiantes_id", "e.id")
            ->leftJoin("apoderados as a", "a.id", "ea.apoderados_id")
            ->leftJoin("parentescos as pa", "pa.id", "a.parentescos_id")
            ->leftJoin("ubigeos as u", "u.id", "e.ubigeos_id")
            ->leftJoin("ubigeos as ub", "ub.id", "e.ubigeos_nacimiento")
            ->where("e.id", $InscripcionSimulacro->estudiantes_id)
            ->first();


        $pagos = DB::table("pagos as p")
            ->select(
                "p.*"
            )
            ->join("inscripcion_simulacros as ip", "ip.pagos_id", "p.id")
            ->where("ip.estudiantes_id",  $InscripcionSimulacro->estudiantes_id)
            ->groupBy("p.id")
            ->get();

        $inscripcion = DB::table("inscripciones as ins")
            ->select(
                "ins.*",
                "s.denominacion as Sede",
                "es.denominacion as Escuela",
                "ar.denominacion as Area"
            )
            ->leftjoin("sedes as s", "ins.sedes_id", "s.id")
            ->leftjoin("escuelas as es", "ins.escuela_id", "es.id")
            ->leftjoin("areas as ar", "ins.areas_id", "ar.id")
            ->where("ins.estudiantes_id", $InscripcionSimulacro->estudiantes_id)
            ->first();
        //return $inscripcion;
        //$inscripcionPagos = InscripcionPago::where('inscripciones_id', $id)->orderBy('concepto_pagos_id')->get();
        // $tipo_documento = TipoDocumento::find($inscripcion->estudiantes_id);
        // dd($estudiante);
        $pdf = new PDF();
        PDF::setFooterCallback(function ($pdf) use ($InscripcionSimulacro) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 10, 'CEPREUNA ABRIL 2024 - JULIO 2024 - Fecha y Hora de Registro: ' . $InscripcionSimulacro->Fecha, "t", false, 'L', 0, '', 0, false, 'T', 'M');
        });
        $pdf::SetTitle('Solicitud');
        $pdf::AddPage();
        $pdf::SetMargins(0, 0, 0);
        $pdf::SetAutoPageBreak(true, 0);


        // $pdf::Image('images/' . $image, 0, 0, 210, "", 'PNG');
        $pdf::SetMargins(20, 40, 20, true);
        $pdf::setCellHeightRatio(1.5);

        // $pdf::Image(Storage::disk('fotos')->path($estudiante->foto), 156, 49, 44, 52, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);

        // $pdf::SetFont('helvetica', 'b', 12);

        $pdf::SetFont('helvetica', 'b', 14);
        $pdf::Cell(0, 5, 'UNIVERSIDAD NACIONAL DEL ALTIPLANO PUNO', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', 'b', 12);
        $pdf::Cell(0, 5, "Centro de Estudios Pre Universitario", 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', '', 9);
        $pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN : DOMINGO 21 DE JULIO DEL 2024', 0, 1, 'C', 0, '', 0);


        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 14);

        $pdf::Cell(0, 5, 'FICHA DE ' .  'INSCRIPCIÓN', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', 'b', 14);

        $pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN PRESENCIAL ABRIL - JULIO 2024 ', 0, 1, 'C', 0, '', 0);
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(130, 6, 'DATOS DEL POSTULANTE', 1, 1, 'C', 0, '', 0);
        // $pdf::SetFont('helvetica', 'b', 8);
        // **********

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'TIPO DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->TipoDocumento, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'NÚMERO DE DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->nro_documento, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'APELLIDO PATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->paterno, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'APELLIDO MATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->materno, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'NOMBRES:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->nombres, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'CELULAR:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->celular, 0, 0, 'L', 0, '', 1);

        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'EMAIL:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $estudiante->email, 0, 0, 'L', 0, '', 1);


        // ********
        // ***********************DATOS ADICIONALES****************
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(130, 6, 'DATOS ADICIONALES', 1, 1, 'C', 0, '', 0);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'SEDE:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $inscripcion->Sede, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'AREA:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $inscripcion->Area, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'PROGRAMA DE ESTUDIOS:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $inscripcion->Escuela, 0, 0, 'L', 0, '', 1);

        $pdf::Image('images/UNAPUNO.png', 10, 5, 24, 24, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf::Image('images/logo-simulacro.png', 170, 5, 31, 27, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $style = array(
            'border' => true,
            'padding' => 1,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false
        );

        $pdf::write2DBarcode($estudiante->nro_documento, 'QRCODE,L', 156, 60, 44, 52, $style, 'N');
        // $pdf::Text(20, 25, 'QRCODE L');


        if ($InscripcionSimulacro) {
            $pdf::ln();
            $pdf::SetFont('helvetica', 'b', 10);

            $pdf::Cell(170, 6, 'DETALLES DE VOUCHER', 1, 1, 'C', 0, '', 0);

            $total = 0;
            $comision = 0;
            foreach ($pagos as $pago) {
                $comision = $comision + 1;
                $pdf::SetFont('helvetica', 'b', 7);
                $pdf::Cell(30, 5, 'SECUENCIA:', 0, 0, 'L', 0, '', 1);
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(40, 5, $pago->secuencia, 0, 0, 'L', 0, '', 1);

                $pdf::SetFont('helvetica', 'b', 7);
                $pdf::Cell(30, 5, 'FECHA:', 0, 0, 'L', 0, '', 1);
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(30, 5, $pago->fecha, 0, 0, 'L', 0, '', 1);

                $pdf::SetFont('helvetica', 'b', 7);
                $pdf::Cell(25, 5, 'MONTO:', 0, 0, 'L', 0, '', 1);
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(30, 5, "S/ " . number_format((float)$pago->monto, 2, '.', ''), 0, 1, 'L', 0, '', 1);

                $total += $pago->monto;
            }
            $pdf::Cell(130, 5, '', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(25, 5, 'TOTAL:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);

            $pdf::Cell(30, 5, "S/ " . number_format((float)$total, 2, '.', ''), 0, 1, 'L', 0, '', 1);

            $pdf::ln();
            $pdf::SetFont('helvetica', 'b', 10);
            $pdf::Cell(170, 6, 'DETALLES DE PAGO', 1, 1, 'C', 0, '', 0);

            $total = 0;
            foreach ($pagos as $inscripcionPago) {
                $pdf::SetFont('helvetica', 'b', 7);
                $pdf::Cell(30, 5, 'CONCEPTO DE PAGO:', 0, 0, 'L', 0, '', 1);
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(95, 5, 'SIMULACRO', 0, 0, 'L', 0, '', 1);

                $pdf::SetFont('helvetica', 'b', 7);
                $pdf::Cell(30, 5, 'MONTO:', 0, 0, 'L', 0, '', 1);
                $pdf::SetFont('helvetica', '', 8);
                $pdf::Cell(30, 5, "S/ " . number_format((float)($inscripcionPago->monto - 1), 2, '.', ''), 0, 1, 'L', 0, '', 1);

                $total += $inscripcionPago->monto;
            }
            $pdf::Cell(125, 5, '', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(30, 5, 'COMISIÓN BANCO:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(30, 5, "S/ " . number_format((float)$comision, 2, '.', ''), 0, 1, 'L', 0, '', 1);

            $pdf::Cell(125, 5, '', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(30, 5, 'TOTAL:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);

            $pdf::Cell(30, 5, "S/ " . number_format((float)($total - 1) + $comision, 2, '.', ''), 0, 1, 'L', 0, '', 1);
        }

        $pdf::SetFont('helvetica', '', 10);
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::SetXY(20, 141);
        // $pdf::Cell(170, 6, 'DECLARACIÓN JURADA ELECTRÓNICA', 1, 1, 'C', 0, '', 0);

        $pdf::SetFont('helvetica', '', 10);
        $html = ' 
        
        <table border="0.5">
        <tr style="text-align:center; font-weight:bold;">
            <th>HORARIO DE INGRESO - INICIO DEL EXAMEN</th>
        </tr>
        </table>
        <table>
        <tr >
            <th>
                <ul>
                    <li>
                    Hora de Ingreso: <span style="font-weight:bold; font-size:11px;">&nbsp;&nbsp;&nbsp;07:00 a.m. a 09:00 a.m.</span> <br> * Transcurrido este horario NADIE podrá ingresar por ningún motivo.
                    </li>
                    <li>
                    Inicio del Examen: <span style="font-weight:bold; font-size:11px;">10:00 a.m.</span>
                    </li>
                    <li>
                    Fin del Examen: <span style="font-weight:bold; font-size:11px;">&nbsp;&nbsp;&nbsp;12:00 p.m.</span>  
                    </li>
                </ul>
            </th>
        </tr>
        </table>


        <table border="0.5">
        <tr style="text-align:center; font-weight:bold;">
            <th>DOCUMENTOS OBLIGATORIOS</th>
        </tr>
        </table>
        <table>
        <tr >
            <th>
                <ul>
                    <li>
                    Presentar su Documento Nacional de Identidad (D.N.I) en fisico.
                    </li>
                    <li>
                    Presentar impreso su ficha de inscripción para agilizar el proceso de registro. 
                    </li>
                </ul>
            </th>
        </tr>
        </table>

        <table border="0.5">
        <tr style="text-align:center; font-weight:bold;">
            <th>MATERIALES NECESARIOS</th>
        </tr>
        </table>
        <table>
        <tr >
            <th>
            <ul>
                <li>
                    Tablero
                </li>
                <li>
                    Lápiz 2B
                </li>
            </ul>
            </th>
            <th>
            <ul>
                <li>
                    Borrador
                </li>
                <li>
                    Tajador
                </li>
            </ul>
            </th>
        </tr>
        </table>

        <table border="0.5">
        <tr style="text-align:center; font-weight:bold;">
            <th>IMPORTANTE</th>
        </tr>
        </table>
        <table>
        <tr >
            <th style="text-align:justify;">El presente simulacro de examen NO OTORGA vacante o puntaje en los Procesos de Admisión a la Universidad Nacional del Altiplano.</th>
        </tr>
        </table>
        <table border="0.5">
        <tr style="text-align:center; font-weight:bold;">
            <th>DECLARACION JURADA</th>
        </tr>
        </table>
        <table>
        <tr>
            <th style="text-align:justify;">El que suscribe declara bajo juramento que la información proporcionada durante el proceso de inscripción y registro para participar del "Simulacro de Examen CEPREUNA ciclo ABRIL - JULIO 2024" es precisa, completa y veraz. Asumo plena responsabilidad por los datos proporcionados y soy consciente de que cualquier falsedad, omisión o información incorrecta, excluye mi participación en el simulacro; en consecuencia, ratifico la veracidad de los datos en la presente declaración jurada. </th>
        </tr>
        </table>
        ';

        $pdf::writeHTML($html, true, false, true, false, '');

        $simulacro_pdf = InscripcionSimulacro::find($id_simulacro);
        $simulacro_pdf->path = 'inscripciones/simulacro/' . $id_encrypt;
        $simulacro_pdf->save();
        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('inscripcion.pdf', 'I');
    }

    public function tomarAsistencia(Request $request)
    {
        if ($request->header("Authorization") == "cepreuna_v2_api_XHR2InL71HMCVE3NvNSNacGvy") {
            $dni = $request->input("dni");
            if ($request->filled("dni")) {
                $result = InscripcionSimulacro::where("nro_documento", $dni)->get();
                $estudiante = DB::table('estudiantes')
                    ->join('inscripcion_simulacros', 'estudiantes.nro_documento', '=', 'inscripcion_simulacros.nro_documento')
                    ->join('inscripciones', 'inscripciones.estudiantes_id', '=', 'estudiantes.id')
                    ->leftJoin('areas', 'areas.id', '=', 'inscripciones.areas_id')
                    ->where('estudiantes.nro_documento', '=', $dni)
                    ->select('estudiantes.nombres', 'estudiantes.paterno', 'estudiantes.materno', 'areas.denominacion', 'inscripcion_simulacros.path')
                    ->first();
                if ($result->count() >= 1) {
                    if ($result->where("asistencia", true)->count() >= 1) {
                        $nombres = $estudiante->paterno . " " . $estudiante->materno . ", " . $estudiante->nombres;
                        $area = $estudiante->denominacion;
                        $path = $estudiante->path;
                        // $message = "El estudiante: " . $estudiante->paterno . " " . $estudiante->materno . ", " . $estudiante->nombres . "  con DNI " . $dni  . " ya registro su asistencia.";
                        return response()->json([
                            "nombres" => $nombres,
                            "dni" => $dni,
                            "area" => $area,
                            "path" => $path,
                        ], 403);
                    }
                    InscripcionSimulacro::where("nro_documento", $dni)->update([
                        'asistencia' => true
                    ]);
                    $nombres = $estudiante->paterno . " " . $estudiante->materno . ", " . $estudiante->nombres;
                    $area = $estudiante->denominacion;
                    $path = $estudiante->path;
                    // return     response()->json(["message" => "El estudiante " . $estudiante->paterno . " " . $estudiante->materno . ", " . $estudiante->nombres . " con número de documento " . $dni . ", ha sido registrado exitosamente."], 200);
                    return     response()->json([
                        "nombres" => $nombres,
                        "dni" => $dni,
                        "area" => $area,
                        "path" => $path,
                    ], 200);
                } else {
                    return  response()->json(["message" => "El número de documento " . $dni . " no existe o es incorrecto."], 401);
                }
            } else {
                return response()->json(["message" => "No existe el número de documento consultado"], 401);
            }
        }
        return response()->json(["message" => "No autorizado"], 404);
    }
}
