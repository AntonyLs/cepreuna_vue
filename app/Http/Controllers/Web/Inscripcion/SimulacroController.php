<?php

namespace App\Http\Controllers\Web\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Models\Periodo;
use App\Models\Apoderado;
use App\Models\EstudianteApoderado;
use App\Models\Pago;
use App\Models\BancoPago;
use App\Models\Colegio;
use App\Models\ConfiguracionInscripciones;
use App\Models\ConfiguracionVacante;
use App\Models\Docente;
use App\Models\Tarifa;
use App\Models\InscripcionPago;
use App\Models\Parentesco;
use App\Models\TarifaEstudiante;
use App\Services\GWorkspace;
use Illuminate\Validation\Rule;
use App\Models\InscripcionSimulacro;
use PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SimulacroController extends Controller
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
        return view('web.inscripcion.validacion');
    }
    public function login(Request $request)
    {


        $credentials = $request->only('email', 'password');

        if ($estudiante = Estudiante::where('usuario', $credentials['email'])->first()) {

            // Verificar si se encontró el estudiante y la contraseña coincide
            if ($estudiante->usuario == $credentials['email'] && $estudiante->password == $credentials['password']) {

                //validacion
                $Existe = InscripcionSimulacro::where('estudiantes_id', $estudiante->id)->first();
                //return $Existe;
                if ($Existe) {
                    return redirect()->back()->with('error', 'El estudiante ya se encuentra registrado para el simulacro.');
                }
                // para el formulario
                $estudiante = Estudiante::join('inscripciones', 'estudiantes.id', '=', 'inscripciones.estudiantes_id')
                    ->join('sedes', 'inscripciones.sedes_id', '=', 'sedes.id')
                    ->select('estudiantes.*', 'inscripciones.*', 'sedes.*')
                    ->where('estudiantes.usuario', $credentials['email'])
                    ->where('estudiantes.password', $credentials['password'])
                    ->first();

                $response["configuracion"] = ConfiguracionInscripciones::where([['tipo_usuario', 1], ['estado', '1'], ['observacion', 'simulacro']])->first();

                $response['estudiante'] = json_encode($estudiante);
                return view('web.inscripcion.estudiante-simulacro', $response);
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
        $rules = $request->validate([
            'nombres' => 'required|string',
            'paterno' => 'required|string',
            'materno' => 'required|string',
            'tipo_documento' => 'required',
            'nro_documento' => 'required',
            'email' => 'required|email',
            'celular' => 'required',
            'sede' => 'required',
            'area' => 'required',
            'escuela' => 'required',
            'tokens' => 'required',
            'id' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'email.email' => '* El formato del correo no es el correcto',
        ]);

        // dd($cantidadVacantes->cantidad);
        $tokens = $request->tokens;
        $pagoExistente = true;
        $documentoValidado = true;
        $sumaPagoDB = 0;
        $cont = 0;
        $validarPago = 0;
        $comisionBanco = 0;
        if (isset($tokens)) {
            while ($cont < count($tokens)) {

                $validarPago = Pago::where('token', $tokens[$cont])->first();
                $comisionBanco = $comisionBanco + 1;

                if (empty($validarPago)) {
                    $pagoExistente = false;
                } else {
                    if ($validarPago->estado == '1') {
                        $sumaPagoDB = $sumaPagoDB + $validarPago->monto;
                        // validar pago con el numero de documento del estudiante
                        $validarDocumento = BancoPago::where([
                            ["secuencia", $validarPago->secuencia],
                            ["imp_pag", $validarPago->monto],
                            ["fch_pag", $validarPago->fecha],
                            ["num_doc", str_pad($request->nro_documento, 15, '0', STR_PAD_LEFT)],
                        ])
                            ->first();
                        if (empty($validarDocumento)) {
                            $documentoValidado = false;
                        }
                    } else {
                        $pagoExistente = false;
                    }
                }
                $cont = $cont + 1;
            }
        } else {
            $pagoExistente = false;
        }


        $totalPagar = round(11, 2);
        if (empty($validarPago)) {
            $pagoExistente = false;
        }

        $periodoActual = Periodo::where('estado', '1')->first();

        if ($pagoExistente) {
            if ($documentoValidado) {
                if (round($sumaPagoDB, 2) >= $totalPagar) {
                    //DB::beginTransaction();
                    try {
                        $cont = 0;
                        $sumaImporte = 0;
                        while ($cont < count($tokens)) {
                            $pago = Pago::where('token', $tokens[$cont])->first();
                            if (!empty($pago)) {
                                $pago = Pago::find($pago->id);
                                $pago->estado = '2';
                                $pago->save();
                                //  215.00
                                // guadar el pago en la misma tabla (todos los campos)

                                $controlEstudiante = Estudiante::where('nro_documento', $request->nro_documento)->first();

                                if (isset($controlEstudiante)) {
                                    $InscripcionSimulacro = new InscripcionSimulacro();
                                    $InscripcionSimulacro->estudiantes_id = $controlEstudiante->id;
                                    $InscripcionSimulacro->monto = $pago->monto;
                                    $InscripcionSimulacro->secuencia = $pago->secuencia;
                                    $InscripcionSimulacro->nro_documento = $pago->nro_documento;
                                    $InscripcionSimulacro->codigoqr = $request->nro_documento;
                                    $InscripcionSimulacro->pagos_id = $pago->id;
                                    $InscripcionSimulacro->save();
                                } else {
                                    $response = array(
                                        "message" => '* El Estudiante no se encuenta matriculado.',
                                        "status" => false,
                                    );
                                }
                            }
                            $cont = $cont + 1;
                        }
                        //DB::commit();
                        $message = 'Inscripcion realizada con exito.';
                        $status = true;
                        $error = '';
                    } catch (\Exception $e) {
                        //DB::rollback();
                        $message = 'Error al inscribirse, intentelo nuevamante.';
                        $status = false;
                        $error = $e;
                    }

                    $response = array(
                        "message" => $message,
                        "status" => $status,
                        "url" => url("inscripciones/simulacro/" . Crypt::encryptString($InscripcionSimulacro->id)),
                        "error" => $error
                    );
                } else {
                    $response = array(
                        "message" => '* El monto total de pago no es valido, ingrese nuevamente el pago correspondiente.',
                        "status" => false,
                    );
                }
            } else {
                $response = array(
                    "message" => '* Error al validar pago, Ud. esta intentando ingresar un pago que no esta a su nombre.',
                    "status" => false,
                );
            }
        } else {

            $response = array(
                "message" => '* El monto total de pago no es valido, ingrese nuevamente el pago correspondiente.',
                "status" => false,
            );
        }
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
            $pdf->Cell(0, 10, 'CEPREUNA SETIEMBRE 2024 - DICIEMBRE 2024 - Fecha y Hora de Registro: ' . $InscripcionSimulacro->Fecha, "t", false, 'L', 0, '', 0, false, 'T', 'M');
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
        $pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN : SÁBADO 21 DE DICIEMBRE DEL 2024', 0, 1, 'C', 0, '', 0);


        $pdf::ln();
        $pdf::SetFont('helvetica', 'b', 14);

        $pdf::Cell(0, 5, 'FICHA DE ' .  'INSCRIPCIÓN', 0, 1, 'C', 0, '', 0);
        $pdf::SetFont('helvetica', 'b', 14);

        $pdf::Cell(0, 5, 'SIMULACRO DE EXAMEN PRESENCIAL SETIEMBRE - DICIEMBRE 2024 ', 0, 1, 'C', 0, '', 0);
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
            <th style="text-align:justify;">El que suscribe declara bajo juramento que la información proporcionada durante el proceso de inscripción y registro para participar del "Simulacro de Examen CEPREUNA ciclo SETIEMBRE - DICIEMBRE 2024" es precisa, completa y veraz. Asumo plena responsabilidad por los datos proporcionados y soy consciente de que cualquier falsedad, omisión o información incorrecta, excluye mi participación en el simulacro; en consecuencia, ratifico la veracidad de los datos en la presente declaración jurada. </th>
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

}
