<?php

namespace App\Http\Controllers\Web\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\BancoPago;
use App\Models\CronogramaPago;
use App\Models\Inscripciones;
use App\Models\InscripcionPago;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\Periodo;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PagoController extends Controller
{
    private $dateTime;
    private $dateTimePartial;

    public function __construct()
    {
        $this->middleware('auth:estudiante');
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }


    public function index()
    {
        $idEstudiante = Auth::user()->id;

        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $periodo = Periodo::where('estado', '1')->first();
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $response['total_pagado'] = InscripcionPago::where([['inscripciones_id', $inscripcion->id], ['concepto_pagos_id', '!=', '3']])->sum('monto');
        $response['cronograma'] = CronogramaPago::where([
            ['estado', '1'],
            ['periodos_id', $periodo->id]
        ])
            ->first();
        $descuento = '0';
        switch ($inscripcion->tipo_estudiante) {
            case '1':
                $descuento = '1';
                break;
            case '2':
                $descuento = '2';
                break;
            case '3':
                $descuento = '2';
                break;
            case '4':
                $descuento = '2';
                break;
            case '6':
                $descuento = '2';
                break;
            default:
                $descuento = '0';
                break;
        }
        if ($descuento == '0') {
            $response['total_pagar'] = 0;
        } else {
            $tarifaInscripcion = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '1'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $tarifaMensual = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '2'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $response['total_pagar'] = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $response['cronograma']->nro_cuota);
        }

        $matricula = Matricula::where("estudiantes_id", $idEstudiante)->where("habilitado", "1")->first();
        if ($matricula) {
            $response["matricula"] = true;
            $response["idmatricula"] = Crypt::encryptString($matricula->id);
        } else {
            $response["matricula"] = false;
        }
        $response['inscripcion'] = $inscripcion;

        return view('web.estudiante.pago', $response);
    }
    public function getResumenPago()
    {
        $idEstudiante = Auth::user()->id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $response['total_pagado'] = InscripcionPago::where([['inscripciones_id', $inscripcion->id], ['concepto_pagos_id', '!=', '3']])->sum('monto');
        $periodo = Periodo::where('estado', '1')->first();
        $response['cronograma'] = CronogramaPago::select('nro_cuota')
            ->where([
                ['estado', '1'],
                ['periodos_id', $periodo->id]
            ])
            ->first();

        $descuento = '0';
        switch ($inscripcion->tipo_estudiante) {
            case '1':
                $descuento = '1';
                break;
            case '2':
                $descuento = '2';
                break;
            case '3':
                $descuento = '2';
                break;
            case '4':
                $descuento = '2';
                break;
            case '6':
                $descuento = '2';
                break;
            default:
                $descuento = '0';
                break;
        }
        // dd($descuento);
        if ($descuento == '0') {
            $response['total_pagar'] = 0;
        } else {
            $tarifaInscripcion = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '1'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $tarifaMensual = Tarifa::where([
                ['modalidad', $inscripcion->modalidad],
                ['concepto_pagos_id', '2'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $response['total_pagar'] = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $response['cronograma']->nro_cuota);
        }
        // dd($tarifaMensual->importe.'-'.$tarifaInscripcion->importe);
        return $response;
    }
    public function getVouchersPago()
    {
        $idEstudiante = Auth::user()->id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $pagos = $inscripcion->pago()->get();

        return response()->json($pagos);
    }
    public function validarPagoCuota(Request $request)
    {
        $idEstudiante = Auth::user()->id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();

        $rules = $request->validate([
            'secuencia' => 'required',
            'monto' => 'required',
            // 'fecha' => 'required',
            'fecha' => 'required|date|after:2020-12-14|date_format:Y-m-d',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2000',
        ], $messages = [
            'required' => '* El campo es obligatorio.',
            'fecha.after' => '* Solo se admiten pagos desde el 15/12/2020.',
            'file.required' => '* El voucher es obligatorio',
            'file.mimes' => '* Solo se admiten formatos pdf,jpg,jpeg,png.',
            'file.max' => '* El peso maximo del archivo debe ser menor a 2 MB.'
        ]);
        $descuento = '0';
        switch ($inscripcion->tipo_estudiante) {
            case '1':
                $descuento = '1';
                break;
            case '2':
                $descuento = '2';
                break;
            case '3':
                $descuento = '2';
                break;
            case '4':
                $descuento = '2';
                break;
            case '6':
                $descuento = '2';
                break;
            default:
                $descuento = '0';
                break;
        }
        // dd($request->file('file')->getClientOriginalExtension());
        $token = Str::random(40);

        // $tarifa = Tarifa::where('id',$request->tarifa)->first();

        $pago = Pago::where([
            ["secuencia", $request->secuencia],
            ["monto", $request->monto],
            ["fecha", $request->fecha],
            ["nro_documento", $request->documento],
        ])
            ->first();
        if (empty($pago)) {
            $bancoPago = BancoPago::where([
                ["secuencia", $request->secuencia],
                ["imp_pag", $request->monto],
                ["fch_pag", $request->fecha],
                ["num_doc", str_pad($request->documento, 15, '0', STR_PAD_LEFT)],
                ["concepto", '00000067']
            ])
                ->first();

            if (empty($bancoPago)) {
                $response = array(
                    "message" => 'Datos invalidos o el concepto de pago no pertenece a  CEPREUNA, intentelo nuevamente.',
                    "status" => false,
                );
            } else {
                $voucherAdjunto = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension());

                DB::beginTransaction();
                try {

                    $nuevoPago = new Pago();
                    $nuevoPago->monto = $request->monto;
                    $nuevoPago->secuencia = $request->secuencia;
                    $nuevoPago->fecha = $request->fecha;
                    $nuevoPago->nro_documento = $request->documento;
                    $nuevoPago->tipo_pago = $descuento;
                    $nuevoPago->token = $token . 'b' . time();
                    $nuevoPago->voucher = $voucherAdjunto;
                    $nuevoPago->save();

                    DB::commit();
                    $message = 'Pago validado correctamente.';
                    $status = true;
                    $token = $nuevoPago->token;
                    $monto = $nuevoPago->monto;
                    $secuencia = $nuevoPago->secuencia;
                    $fecha = $nuevoPago->fecha;
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = 'Error al validar pago, intentelo nuevamente.';
                    $status = false;
                }

                if ($status == true) {
                    $response = array(
                        "message" => $message,
                        "status" => $status,
                        "token" => $token,
                        "monto" => $monto,
                        "secuencia" => $secuencia,
                        "fecha" => $fecha,
                    );
                } else {
                    $response = array(
                        "message" => $message,
                        "status" => $status,
                    );
                }
            }
        } else {
            if ($pago->estado == '1') {

                $response = array(
                    "message" => 'Pago validado.',
                    "status" => true,
                    "token" => $pago->token,
                    "monto" => $pago->monto,
                    "secuencia" => $pago->secuencia,
                    "fecha" => $pago->fecha,
                );
            } else {
                $response = array(
                    "message" => 'El pago ya ha sido registrado anteriormente.',
                    "status" => false,
                );
            }
        }


        return response()->json($response);
    }
    public function registrarPagoCuota(Request $request)
    {
        $idEstudiante = Auth::user()->id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $totalPagado = InscripcionPago::where([['inscripciones_id', $inscripcion->id], ['concepto_pagos_id', '!=', '3']])->sum('monto');

        $periodo = Periodo::where('estado', '1')->first();
        $cronograma = CronogramaPago::select('nro_cuota')
            ->where([
                ['estado', '1'],
                ['periodos_id', $periodo->id]
            ])
            ->first();

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
                            ["num_doc", str_pad($estudiante->nro_documento, 15, '0', STR_PAD_LEFT)],
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

        // total a pagar hasta la cuota actual
        $descuento = '0';
        switch ($inscripcion->tipo_estudiante) {
            case '1':
                $descuento = '1';
                break;
            case '2':
                $descuento = '2';
                break;
            case '3':
                $descuento = '2';
                break;
            case '4':
                $descuento = '2';
                break;
            case '6':
                $descuento = '2';
                break;
            default:
                $descuento = '0';
                break;
        }

        $tarifaInscripcion = Tarifa::where([
            ['modalidad', $inscripcion->modalidad],
            ['concepto_pagos_id', '1'],
            ['tipo_estudiante', $descuento]
        ])->first();

        $tarifaMensual = Tarifa::where([
            ['modalidad', $inscripcion->modalidad],
            ['concepto_pagos_id', '2'],
            ['tipo_estudiante', $descuento]
        ])->first();

        $totalPagar = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $cronograma->nro_cuota);
        $importeActual = $sumaPagoDB - $comisionBanco;

        if ($pagoExistente) {
            if ($documentoValidado) {
                if (round($totalPagado + $importeActual, 2) >= $totalPagar) {
                    DB::beginTransaction();
                    try {
                        $cont = 0;
                        while ($cont < count($tokens)) {
                            $pago = Pago::where('token', $tokens[$cont])->first();

                            if (!empty($pago)) {
                                $pago = Pago::find($pago->id);
                                $pago->estado = '2';
                                $pago->save();

                                $pagoSinComision = round($pago->monto - 1, 2);

                                $mensualPago = new InscripcionPago();
                                $mensualPago->monto = $pagoSinComision;
                                $mensualPago->inscripciones_id = $inscripcion->id;
                                $mensualPago->pagos_id = $pago->id;
                                $mensualPago->concepto_pagos_id = 2;
                                $mensualPago->save();
                            }
                            $cont = $cont + 1;
                        }
                        DB::commit();
                        $message = 'Pago registrado correctamente.';
                        $status = true;
                        $error = '';
                    } catch (\Exception $e) {
                        DB::rollback();
                        $message = 'Error al registrar pago, intentelo nuevamante.';
                        $status = false;
                        $error = $e;
                    }
                    $response = array(
                        "message" => $message,
                        "status" => $status,
                        "error" => $error
                    );
                } else {
                    $response = array(
                        "message" => '* El monto total de pago es menor al monto total a pagar.',
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
                "message" => '* No se encontraron pagos.',
                "status" => false,
            );
        }

        return response()->json($response);
    }

    public function registrarPagoCuotaMora(Request $request)
    {
        // dd($request->all());
        // dd(strtotime("2022-05-20"));
        $idEstudiante = Auth::user()->id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $totalPagado = InscripcionPago::where('inscripciones_id', $inscripcion->id)->sum('monto');
        $periodo = Periodo::where('estado', '1')->first();
        $cronograma = CronogramaPago::select('nro_cuota', 'fin')
            ->where([
                ['estado', '1'],
                ['periodos_id', $periodo->id]
            ])
            ->first();

        $tarifaMora = Tarifa::where('concepto_pagos_id', '3')->first();

        $tokens = $request->tokens;
        $pagoExistente = true;
        $documentoValidado = true;
        $sumaPagoDB = 0;
        $cont = 0;
        $validarPago = 0;
        $comisionBanco = 0;
        $validarFecha = true;
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
                            ["num_doc", str_pad($estudiante->nro_documento, 15, '0', STR_PAD_LEFT)],
                        ])
                            ->first();
                        if (empty($validarDocumento)) {
                            $documentoValidado = false;
                        } else {
                            // dd(strtotime($validarDocumento->fecha) > strtotime($cronograma->fin));
                            if (strtotime($validarDocumento->fch_pag) > strtotime($cronograma->fin)) {
                                // dd(strtotime($validarDocumento->fecha) > strtotime($cronograma->fin));
                                $validarFecha = false;
                            }
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

        // total a pagar hasta la cuota actual
        $descuento = '0';
        switch ($inscripcion->tipo_estudiante) {
            case '1':
                $descuento = '1';
                break;
            case '2':
                $descuento = '2';
                break;
            case '3':
                $descuento = '2';
                break;
            case '4':
                $descuento = '2';
                break;
            case '6':
                $descuento = '2';
                break;
            default:
                $descuento = '0';
                break;
        }

        $tarifaInscripcion = Tarifa::where([
            ['modalidad', $inscripcion->modalidad],
            ['concepto_pagos_id', '1'],
            ['tipo_estudiante', $descuento]
        ])->first();

        $tarifaMensual = Tarifa::where([
            ['modalidad', $inscripcion->modalidad],
            ['concepto_pagos_id', '2'],
            ['tipo_estudiante', $descuento]
        ])->first();

        if ($validarFecha) {
            $totalPagar = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $cronograma->nro_cuota);
            $importeActual = $sumaPagoDB - $comisionBanco;

            if ($pagoExistente) {
                if ($documentoValidado) {
                    if (round($totalPagado + $importeActual, 2) >= $totalPagar) {
                        DB::beginTransaction();
                        try {
                            $cont = 0;
                            while ($cont < count($tokens)) {
                                $pago = Pago::where('token', $tokens[$cont])->first();

                                $pago = Pago::find($pago->id);
                                $pago->estado = '2';
                                $pago->save();

                                $pagoSinComision = round($pago->monto - 1, 2);

                                $mensualPago = new InscripcionPago();
                                $mensualPago->monto = $pagoSinComision;
                                $mensualPago->inscripciones_id = $inscripcion->id;
                                $mensualPago->pagos_id = $pago->id;
                                $mensualPago->concepto_pagos_id = 2;
                                $mensualPago->save();

                                $cont = $cont + 1;
                            }
                            DB::commit();
                            $message = 'Pago registrado correctamente.';
                            $status = true;
                            $error = '';
                        } catch (\Exception $e) {
                            DB::rollback();
                            $message = 'Error al registrar pago, intentelo nuevamante.';
                            $status = false;
                            $error = $e;
                        }
                        $response = array(
                            "message" => $message,
                            "status" => $status,
                            "error" => $error
                        );
                    } else {
                        $response = array(
                            "message" => '* El monto total de pago es menor al monto total a pagar.',
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
                    "message" => '* No se encontraron pagos.',
                    "status" => false,
                );
            }
        } else {
            $totalPagar = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $cronograma->nro_cuota) + floatVal($tarifaMora->importe);
            $importeActual = $sumaPagoDB - $comisionBanco;

            if ($pagoExistente) {
                if ($documentoValidado) {
                    if (round($totalPagado + $importeActual, 2) >= $totalPagar) {
                        DB::beginTransaction();
                        try {
                            $cont = 0;
                            while ($cont < count($tokens)) {
                                $pago = Pago::where('token', $tokens[$cont])->first();

                                $pago = Pago::find($pago->id);
                                $pago->estado = '2';
                                $pago->save();
                                if ($cont == 0) {
                                    $pagoSinComision = round($pago->monto - 1, 2) - floatVal($tarifaMora->importe);
                                    $mora = new InscripcionPago();
                                    $mora->monto = 30.00;
                                    $mora->inscripciones_id = $inscripcion->id;
                                    $mora->pagos_id = $pago->id;
                                    $mora->concepto_pagos_id = 3;
                                    $mora->save();
                                } else {
                                    $pagoSinComision = round($pago->monto - 1, 2);
                                }
                                $mensualPago = new InscripcionPago();
                                $mensualPago->monto = $pagoSinComision;
                                $mensualPago->inscripciones_id = $inscripcion->id;
                                $mensualPago->pagos_id = $pago->id;
                                $mensualPago->concepto_pagos_id = 2;
                                $mensualPago->save();

                                $cont = $cont + 1;
                            }
                            DB::commit();
                            $message = 'Pago registrado correctamente.';
                            $status = true;
                            $error = '';
                        } catch (\Exception $e) {
                            DB::rollback();
                            $message = 'Error al registrar pago, intentelo nuevamante.';
                            $status = false;
                            $error = $e;
                        }
                        $response = array(
                            "message" => $message,
                            "status" => $status,
                            "error" => $error
                        );
                    } else {
                        $response = array(
                            "message" => '* El monto total de pago es menor al monto total a pagar.',
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
                    "message" => '* No se encontraron pagos.',
                    "status" => false,
                );
            }
        }
        return response()->json($response);
    }
    public function save_file($file, $extension)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk('vouchers')->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
}
