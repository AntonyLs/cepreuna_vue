<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\BancoPago;
use App\Models\CronogramaPago;
use App\Models\InscripcionCursoTaller;
use App\Models\InscripcionPago;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\GWorkspace;
use Illuminate\Validation\Rule;
use PDF;


class InscripcionCursoTallerController extends Controller
{
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
        //return $request;
        // 🛑 **VALIDACIÓN MANUAL** (porque `$request->validate()` no funciona en API)
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'paterno' => 'required|string',
            'materno' => 'required|string',
            //'tipo_documento' => 'required',
            'documento' => 'required|string|min:5',
            'correo' => 'required|email',
            'celular' => 'required|numeric',
            'area' => 'required',
            //'escuela' => 'required',
            'tokens' => 'required|array|min:1',
            'condicion' => 'required'
        ], [
            'required' => '* El campo :attribute es obligatorio.',
            'email.email' => '* El formato del correo no es el correcto',
            'tokens.array' => '* Debe proporcionar al menos un token válido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Error de validación",
                "status" => false,
                "errors" => $validator->errors()
            ], 422);
        }

        // Inicializar variables
        $tokens = $request->tokens;
        $pagoExistente = true;
        $documentoValidado = true;
        $sumaPagoDB = 0;

        foreach ($tokens as $token) {
            $validarPago = Pago::where('token', $token)->first();

            if (!$validarPago || $validarPago->estado != '1') {
                $pagoExistente = false;
                break;
            }

            $sumaPagoDB += $validarPago->monto;

            // Validar el documento con la tabla BancoPago
            $validarDocumento = BancoPago::where([
                ["secuencia", $validarPago->secuencia],
                ["imp_pag", $validarPago->monto],
                ["fch_pag", $validarPago->fecha],
                ["num_doc", str_pad($request->documento, 15, '0', STR_PAD_LEFT)],
            ])->first();

            if (!$validarDocumento) {
                $documentoValidado = false;
                break;
            }
        }

        // Verificar si el pago es suficiente
        $totalPagar = 61.00; // Monto esperado

        if (!$pagoExistente) {
            return response()->json([
                "message" => "* No se encontraron pagos válidos para procesar la inscripción.",
                "status" => false,
            ], 400);
        }

        if (!$documentoValidado) {
            return response()->json([
                "message" => "* Error: el pago no está vinculado a este documento.",
                "status" => false,
            ], 400);
        }

        if (round($sumaPagoDB, 2) < $totalPagar) {
            return response()->json([
                "message" => "* El monto total de pago es insuficiente.",
                "status" => false,
            ], 400);
        }

        // **Proceso de Inscripción**
        try {
            DB::beginTransaction();

            foreach ($tokens as $token) {
                $pago = Pago::where('token', $token)->first();
                if ($pago) {
                    $pago->estado = '2';
                    $pago->save();
                }
            }

            // Verificar si el estudiante ya está registrado
            $controlEstudiante = InscripcionCursoTaller::where('nro_documento', $request->documento)->first();

            if ($controlEstudiante) {
                return response()->json([
                    "message" => "* Ya se encuentra registrado.",
                    "status" => false,
                ], 400);
            }

            // Guardar nueva inscripción
            $inscripcion = new InscripcionCursoTaller();
            $inscripcion->nombres = $request->nombre;
            $inscripcion->paterno = $request->paterno;
            $inscripcion->materno = $request->materno;
            $inscripcion->tipo_documentos_id = 1;
            $inscripcion->nro_documento = $request->documento;
            $inscripcion->celular = $request->celular;
            $inscripcion->email = $request->correo;
            $inscripcion->area = $request->area;
            $inscripcion->condicion = $request->condicion;
            $inscripcion->monto = $sumaPagoDB;
            $inscripcion->secuencia = $pago->secuencia ?? null;
            $inscripcion->codigoqr = $request->documento;
            $inscripcion->pagos_id = $pago->id ?? null;
            $inscripcion->save();

            DB::commit();

            return response()->json([
                "message" => "Inscripción realizada con éxito.",
                "status" => true,
                "url" => url("api/inscripciones/curso/" . Crypt::encryptString($inscripcion->id)),
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error en la inscripción: " . $e->getMessage());

            return response()->json([
                "message" => "Error al inscribirse, inténtelo nuevamente.",
                "status" => false,
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InscripcionCursoTaller  $inscripcionCursoTaller
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $inscripcion = InscripcionCursoTaller::where('id', $id)->first();
        $response = array(
            "status" => true,
            "id" => Crypt::encryptString($id),
            //"id" => 5239,
            "tipo" => empty($inscripcion) ? false : 1,
        );
        return view('web.inscripcion.curso-inscrito', $response);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InscripcionCursoTaller  $inscripcionCursoTaller
     * @return \Illuminate\Http\Response
     */
    public function edit(InscripcionCursoTaller $inscripcionCursoTaller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InscripcionCursoTaller  $inscripcionCursoTaller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InscripcionCursoTaller $inscripcionCursoTaller)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InscripcionCursoTaller  $inscripcionCursoTaller
     * @return \Illuminate\Http\Response
     */
    public function destroy(InscripcionCursoTaller $inscripcionCursoTaller)
    {
        //
    }

    public function getFicha(Request $request)
    {
        if (request()->header('Authorization') !== "cepreuna_v1_api") {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $credentials = $request->only('nro_documento',);

        if ($participante = InscripcionCursoTaller::where('nro_documento', $credentials['nro_documento'])->first()) {

            // Verificar si se encontró el participante y la contraseña coincide
            if ($participante->nro_documento == $credentials['nro_documento']) {

                //validacion
                $Existe = InscripcionCursoTaller::where('id', $participante->id)->first();
                //return $Existe;
                if ($Existe) {
                    $url = "api/inscripciones/curso/".Crypt::encryptString($Existe->id);
                    $response["status"] = true;
                    $response["datos"] = $url;
                    $response["mensajes"] = "Acceso correcto";
                }else{
                    $response["status"] = false;
                    $response["datos"] = [];
                    $response["mensajes"] = "El participante no se encuentra registrado para el Curso Taller";
                }
            } else{
                $response["status"] = false;
                $response["datos"] = [];
                $response["mensajes"] = "Nro de Documento Invalido";
            }
        }else{
            $response["status"] = false;
            $response["datos"] = [];
            $response["mensajes"] = "Usted no se encuentra registrado";
        }
        return response()->json($response);
    }
    public function tomarAsistencia(Request $request)
    {
        // Verificar la autorización
        if ($request->header("Authorization") !== "cepreuna_v1_api") {
            return response()->json(["message" => "No autorizado"], 403);
        }

        // Validar que el DNI esté presente en la solicitud
        $dni = $request->input("dni");
        if (!$dni) {
            return response()->json(["message" => "Debe proporcionar un número de documento."], 400);
        }

        // Buscar la inscripción del participante
        $result = InscripcionCursoTaller::where("nro_documento", $dni)->first();

        if (!$result) {
            return response()->json(["message" => "El número de documento {$dni} no existe o es incorrecto."], 404);
        }

        // Si ya registró asistencia, devolver el mensaje correspondiente
        if ($result->asistencia_d2) {
            return response()->json([
                "message" => "El participante con DNI {$dni} ya registró su asistencia.",
                "nombres" => "{$result->paterno} {$result->materno}, {$result->nombres}",
                "area" => $result->area ?? "No asignado",
                "path" => $result->path ?? null
            ], 200);
        }

        // Marcar asistencia como registrada
        InscripcionCursoTaller::where("nro_documento", $dni)->update([
            'asistencia_d2' => true
        ]);

        return response()->json([
            "message" => "Asistencia registrada correctamente.",
            "nombres" => "{$result->paterno} {$result->materno}, {$result->nombres}",
            "dni" => $dni,
            "area" => $result->area ?? "No asignado",
            "path" => $result->path ?? null
        ], 200);
    }

    public function pdf($id_encrypt)
    {

        $id_simulacro = Crypt::decryptString($id_encrypt);

        $InscripcionSimulacro = DB::table('inscripcion_curso_tallers as i')
            ->select(
                "i.*",
                DB::raw("DATE_FORMAT(i.created_at,'%d/%m/%Y %h:%i %p') as Fecha")
            )
            ->where('i.id', $id_simulacro)
            ->first();

        //dd($InscripcionSimulacro);

        // $periodo = Periodo::where("estado","1")->first();
        if (!isset($InscripcionSimulacro)) {
            abort(401, "no existe la inscripcion");
        }

        $pagos = DB::table("pagos as p")
            ->select(
                "p.*"
            )
            ->join("inscripcion_curso_tallers as ip", "ip.pagos_id", "p.id")
            ->where("p.id",  $InscripcionSimulacro->pagos_id)
            ->groupBy("p.id")
            ->get();

        //return $inscripcion;
        //$inscripcionPagos = InscripcionPago::where('inscripciones_id', $id)->orderBy('concepto_pagos_id')->get();
        // $tipo_documento = TipoDocumento::find($inscripcion->estudiantes_id);
        //dd($pagos);
        $pdf = new PDF();
        PDF::setFooterCallback(function ($pdf) use ($InscripcionSimulacro) {
            $pdf->SetY(-15);
            // $y = $pdf->SetY(-15);
            $pdf->Line(10, 283, 200, 283);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 10, 'CEPREUNA 2025 - Fecha y Hora de Registro: ' . $InscripcionSimulacro->Fecha, "t", false, 'L', 0, '', 0, false, 'T', 'M');
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
        //$pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN : SÁBADO 21 DE DICIEMBRE DEL 2024', 0, 1, 'C', 0, '', 0);


        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 14);

        $pdf::Cell(0, 5, 'FICHA DE ' .  'INSCRIPCIÓN', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', 'b', 14);

        //$pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN PRESENCIAL SETIEMBRE - DICIEMBRE 2024 ', 0, 1, 'C', 0, '', 0);
        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(130, 6, 'DATOS DEL DOCENTE INSCRITO', 1, 1, 'C', 0, '', 0);
        // $pdf::SetFont('helvetica', 'b', 8);
        // **********
        $TipoDocumento = '';
        switch ($InscripcionSimulacro->tipo_documentos_id) {
            case 1:
                $TipoDocumento = 'Documento Nacional de Identidad (DNI)';
                break;
            case 2:
                $TipoDocumento = 'Pasaporte';
                break;
            case 3:
                $TipoDocumento = 'Carné de Extranjería';
                break;
            case 4:
                $TipoDocumento = 'Cédula de identidad';
                break;
            default:
                $TipoDocumento = 'Tipo documento no definidos';
        }


        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'TIPO DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $TipoDocumento, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'NÚMERO DE DOCUMENTO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->nro_documento, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'APELLIDO PATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->paterno, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'APELLIDO MATERNO:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->materno, 0, 1, 'L', 0, '', 1);
        // ********
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'NOMBRES:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->nombres, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'CELULAR:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->celular, 0, 0, 'L', 0, '', 1);

        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'EMAIL:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $InscripcionSimulacro->email, 0, 0, 'L', 0, '', 1);


        // ********
        // ***********************DATOS ADICIONALES****************
        $area = '';
        $condicion= '';

        switch ($InscripcionSimulacro->area) {
            case 1:
                $area = 'Razonamiento Matemático, Aritmética, Álgebra, Geometría, Trigonometría';
                break;
            case 2:
                $area = 'Razonamiento Verbal, Comunicación, Literatura, Quechua y Aimara';
                break;
            case 3:
                $area = 'Física, Química, Biología y Anatomía';
                break;
            case 4:
                $area = 'Geografía, Historia, Educación Cívica, Economía, Psicología y Filosofía';
                break;
            default:
                $area = 'Área no definida';
        }

        switch ($InscripcionSimulacro->condicion) {
            case 1:
                $condicion = 'Unap';
                break;
            case 2:
                $condicion = 'Particular';
                break;
            default:
                $condicion = 'Condición no definida';
        }

        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 10);
        $pdf::Cell(130, 6, 'DATOS ADICIONALES', 1, 1, 'C', 0, '', 0);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'AREA:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $area, 0, 0, 'L', 0, '', 1);

        $pdf::SetFont('helvetica', 'b', 7);
        $pdf::Cell(30, 5, 'CONDICÍON:', 0, 0, 'L', 0, '', 1);
        $pdf::SetFont('helvetica', '', 8);
        $pdf::Cell(40, 5, $condicion, 0, 1, 'L', 0, '', 1);
        // ********
        // $pdf::SetFont('helvetica', 'b', 7);
        // $pdf::Cell(30, 5, 'PROGRAMA DE ESTUDIOS:', 0, 0, 'L', 0, '', 1);
        // $pdf::SetFont('helvetica', '', 8);
        //$pdf::Cell(40, 5, $inscripcion->Escuela, 0, 0, 'L', 0, '', 1);

        $pdf::Image('images/UNAPUNO.png', 10, 5, 24, 24, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $pdf::Image('images/logo-oficial.png', 170, 5, 31, 29, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
        $style = array(
            'border' => true,
            'padding' => 1,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false
        );

        $pdf::write2DBarcode($InscripcionSimulacro->nro_documento, 'QRCODE,L', 156, 60, 44, 52, $style, 'N');
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

            //$total = 0;

                // $pdf::SetFont('helvetica', 'b', 7);
                // $pdf::Cell(30, 5, 'FECHA:', 0, 0, 'L', 0, '', 1);
                // $pdf::SetFont('helvetica', '', 8);
                // $pdf::Cell(30, 5, $pago->fecha, 0, 0, 'L', 0, '', 1);

            $pdf::Cell(65, 5, '', 0, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(30, 5, 'FECHA:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(30, 5, $pago->fecha, 0, 0, 'L', 0, '', 1);

            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(30, 5, 'COMISIÓN BANCO:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', '', 8);
            $pdf::Cell(30, 5, "S/ " . number_format((float)$comision, 2, '.', ''), 0, 1, 'L', 0, '', 1);

            $pdf::Cell(125, 5, '', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 7);
            $pdf::Cell(30, 5, 'TOTAL:', 0, 0, 'L', 0, '', 1);
            $pdf::SetFont('helvetica', 'b', 8);
            $pdf::Cell(30, 5, "S/ " . number_format((float)($total - 1) , 2, '.', ''), 0, 1, 'L', 0, '', 1);
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
            <th>HORARIO DE INGRESO - DIA 1</th>
        </tr>
        </table>
        <table>
        <tr >
            <th>
                <ul>
                    <li>
                    Hora de Ingreso: <span style="font-weight:bold; font-size:11px;">&nbsp;&nbsp;&nbsp;08:00 a.m. a 09:00 a.m.</span>
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
        ';

        $pdf::writeHTML($html, true, false, true, false, '');

        $simulacro_pdf = InscripcionCursoTaller::find($id_simulacro);
        $simulacro_pdf->path = 'api/inscripciones/curso/' . $id_encrypt;
        $simulacro_pdf->save();
        $pdf::SetAutoPageBreak(TRUE, 0);
        $pdf::Output('inscripcion.pdf', 'I');
    }

}
