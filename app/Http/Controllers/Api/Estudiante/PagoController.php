<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\BancoPago;
use App\Models\CronogramaPago;
use App\Models\Inscripciones;
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

class PagoController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:estudiante');
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }

    public function getDataPagos(Request $request)
    {
        $token = explode("-", Crypt::decryptString($request->_token));

        $inscripcion = Inscripciones::where('estudiantes_id', $token[0])->first();
        $response["vouchers"] = $inscripcion->pago()->get();
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $response['total_pagado'] = InscripcionPago::where([['inscripciones_id', $inscripcion->id], ['concepto_pagos_id', '!=', '3']])->sum('monto');
        $periodo = Periodo::where('estado', '1')->first();
        $response['cronograma_actual'] = CronogramaPago::where([
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
            default:
                $descuento = '0';
                break;
        }
        // dd($descuento);
        if ($descuento == '0') {
            $response['total_pagar'] = 0;
        } else {
            $tarifaInscripcion = Tarifa::where([
                ['tipo_colegios_id', $estudiante->colegio->tipo_colegios_id],
                ['concepto_pagos_id', '1'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $tarifaMensual = Tarifa::where([
                ['tipo_colegios_id', $estudiante->colegio->tipo_colegios_id],
                ['concepto_pagos_id', '2'],
                ['tipo_estudiante', $descuento]
            ])->first();

            $response['total_pagar'] = floatVal($tarifaInscripcion->importe) + floatVal($tarifaMensual->importe * $response['cronograma_actual']->nro_cuota);
        }
        // dd($tarifaMensual->importe.'-'.$tarifaInscripcion->importe);
        return $response;
    }
    public function validarPagoCuota(Request $request, $id)
    {
        // dd($request->all());
        // return $request->all();
        $idEstudiante = $id;
        $inscripcion = Inscripciones::where('estudiantes_id', $idEstudiante)->first();
        $rules = $request->validate([
            'secuencia' => 'required',
            'monto' => 'required',
            // 'fecha' => 'required',
            'fecha' => 'required|date|after:2020-12-14|date_format:Y-m-d',
            //'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2000',
            'file' => $request->pagarEnPagalo ? 'required|mimes:pdf|max:2000' : 'required|mimes:jpg,jpeg,png|max:2000'
        ], $messages = [
            'required' => '* El campo es obligatorio.',
            'fecha.after' => '* Solo se admiten pagos desde el 15/12/2020.',
            'file.required' => '* El voucher es obligatorio',
            'file.mimes' => '* Solo se admiten formatos pdf,jpg,jpeg,png.',
            'file.max' => '* El peso maximo del archivo debe ser menor a 2 MB.'
        ]);
        // dd($request->all());

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
        if ($request->pagarEnPagalo == true) {
            $pago = Pago::where([
                [DB::raw('SUBSTRING(secuencia, 2, 6)'), $request->secuencia], // Ignorar el primer dígito y comparar los siguientes seis dígitos
                ["monto", $request->monto],
                ["fecha", $request->fecha],
                ["nro_documento", $request->documento],
            ])->first();
            if (empty($pago)) {
                $bancoPago = BancoPago::where([
                    [DB::raw('SUBSTRING(secuencia, 2, 6)'), $request->secuencia],
                    ["imp_pag", $request->monto],
                    ["fch_pag", $request->fecha],
                    ["cod_age", '0987'],
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
                        $nuevoPago->secuencia = $bancoPago->secuencia;
                        $nuevoPago->fecha = $request->fecha;
                        $nuevoPago->nro_documento = $request->documento;
                        $nuevoPago->tipo_pago = "1";
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
                        "message" => 'Pago validado correctamente.',
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
        } else {
            $bancoPagoValidacion = BancoPago::where([
                ["secuencia", $request->secuencia],
                ["imp_pag", $request->monto],
                ["fch_pag", $request->fecha],
                ["concepto", '00000067']
            ])
                ->first();

            if (empty($bancoPagoValidacion)) {
                $response = array(
                    "message" => 'Datos invalidos o el concepto de pago no pertenece a  CEPREUNA, intentelo nuevamente.',
                    "status" => false,
                );
                return response()->json($response);
            }

            if ($bancoPagoValidacion->cuenta == '0701010736') {
                $pago = Pago::where([
                    ["secuencia", $request->secuencia],
                    ["monto", $request->monto],
                    ["fecha", $request->fecha],
                ])->first();
            } else {
                $pago = Pago::where([
                    ["secuencia", $request->secuencia],
                    ["monto", $request->monto],
                    ["fecha", $request->fecha],
                    ["nro_documento", $request->documento],
                ])->first();
            }

            if (empty($pago)) {
                if ($bancoPagoValidacion->cuenta == '0701010736') {
                    $bancoPago = BancoPago::where([
                        ["secuencia", $request->secuencia],
                        ["imp_pag", $request->monto],
                        ["fch_pag", $request->fecha],
                        ["concepto", '00000067']
                    ])
                        ->first();
                } else {
                    $bancoPago = BancoPago::where([
                        ["secuencia", $request->secuencia],
                        ["imp_pag", $request->monto],
                        ["fch_pag", $request->fecha],
                        ["num_doc", str_pad($request->documento, 15, '0', STR_PAD_LEFT)],
                        ["concepto", '00000067']
                    ])
                        ->first();
                }

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
        }
        return response()->json($response);
    }

    public function validarPagoCurso(Request $request)
    {
        $rules = $request->validate([
            'secuencia' => 'required',
            'monto' => 'required',
            // 'fecha' => 'required',
            'fecha' => 'required|date|after:2020-12-14|date_format:Y-m-d',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2000',
        ], $messages = [
            'required' => '* El campo es obligatorio.',
            'fecha.after' => '* Solo se admiten pagos desde el 15/12/2020.',
            // 'fecha.before' => '* Solo se admiten pagos hasta antes del 10/04/2022.',
            'file.required' => '* El voucher es obligatorio',
            'file.mimes' => '* Solo se admiten formatos pdf,jpg,jpeg,png.',
            'file.max' => '* El peso maximo del archivo debe ser menor a 2 MB.'
        ]);
        // dd($request->file('file')->getClientOriginalExtension());
        $token = Str::random(40);

        $tarifa = Tarifa::where('id', $request->tarifa)->first();

        if ($request->pagarEnPagalo == true) {
            $pago = Pago::where([
                [DB::raw('SUBSTRING(secuencia, 2, 6)'), $request->secuencia], // Ignorar el primer dígito y comparar los siguientes seis dígitos
                ["monto", $request->monto],
                ["fecha", $request->fecha],
                ["nro_documento", $request->documento],
            ])->first();
            if (empty($pago)) {
                $bancoPago = BancoPago::where([
                    [DB::raw('SUBSTRING(secuencia, 2, 6)'), $request->secuencia],
                    ["imp_pag", $request->monto],
                    ["fch_pag", $request->fecha],
                    ["cod_age", '0987'],
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
                        $nuevoPago->secuencia = $bancoPago->secuencia;
                        $nuevoPago->fecha = $request->fecha;
                        $nuevoPago->nro_documento = $request->documento;
                        $nuevoPago->tipo_pago = "1";
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
                        "message" => 'Pago validado correctamente.',
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
        } else {
            $pago = Pago::where([
                ["secuencia", $request->secuencia],
                ["monto", $request->monto],
                ["fecha", $request->fecha],
                ["nro_documento", $request->documento],
            ])->first();
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
                        $nuevoPago->tipo_pago = "1";
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
                        "message" => 'Pago validado correctamente.',
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
