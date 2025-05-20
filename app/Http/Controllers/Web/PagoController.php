<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarifa;
use Illuminate\Support\Facades\DB;
use App\Models\Pago;
use App\Models\BancoPago;
use App\Models\Colegio;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    //
    private $dateTime;
    private $dateTimePartial;
    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }

    public function validarPago(Request $request)
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
    public function getTarifaColegio(Request $request)
    {
        $tarifa = Tarifa::select('id', 'importe')->where([['tipo_colegios_id', $request->tipo_colegio], ['concepto_pagos_id', $request->concepto]])->first();

        return response()->json($tarifa);
    }
    public function getTarifaModalidad(Request $request)
    {
        $colegio = Colegio::where('id', $request->colegio)->first();
        $tarifa = Tarifa::select('id', 'importe')->where([['modalidad', $request->modalidad], ['concepto_pagos_id', $request->concepto], ['tipo_colegios_id', $colegio->tipo_colegios_id]])->first();

        return response()->json($tarifa);
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
