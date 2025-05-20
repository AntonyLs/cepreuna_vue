<?php

namespace App\Http\Controllers\Intranet\Inscripcion;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use App\Models\Inscripciones;
use App\Models\Tarifa;
use App\Models\Pago;
use App\Models\BancoPago;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\InscripcionPago;
use App\Models\TarifaEstudiante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Services\GWorkspace;

class EstudianteController extends Controller
{
    private $dateTime;
    private $dateTimePartial;
    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $inscripcion = Inscripciones::with(['estudiante','area','sede','periodo','turno'])->find(2);
        // dd($inscripcion);

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

        // $permisos = Permission::select('name')->get();
        // $response['permisos'] = $permisos;
        return view("intranet.inscripcion.estudiante.index", $response);
    }

    public function lista(Request $request)
    {
        // dd($request);
        $table = new EloquentVueTables;
        $data = $table->get(new Inscripciones, ['id', 'correlativo', 'estado', 'estudiantes_id', 'areas_id', 'sedes_id', 'periodos_id', 'turnos_id'], ['estudiante', 'area', 'sede', 'periodo', 'turno']);
        $data = $data->orderBy('id', 'desc');
        $response = $table->finish($data);
        return response()->json($response);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inscripcion = Inscripciones::find($id);
        $estudiante = $inscripcion->estudiante()->with('colegio')->first();
        $area = $inscripcion->area()->first();
        $sede = $inscripcion->sede()->first();
        $periodo = $inscripcion->periodo()->first();
        $turno = $inscripcion->turno()->first();
        $pago = $inscripcion->pago()->get();
        $inscripcionPagos = $inscripcion->inscripcionPago()->with('conceptoPago')->orderBy('concepto_pagos_id')->get();
        $sumaTotalPagos = $inscripcion->inscripcionPago()->sum('monto');
        $tarifaEstudiante = TarifaEstudiante::where("estudiantes_id", $estudiante->id)->get();

        $adicionalCuenta = 0;
        foreach ($pago as $key => $value) {
            $bancoPago = BancoPago::where([
                ["secuencia", $value->secuencia],
                ["imp_pag", $value->monto],
                ["fch_pag", $value->fecha],
                ["concepto", '00000067']
            ])
                ->first();

            if ($bancoPago->cuenta == '0701010736') {
                $adicionalCuenta = $adicionalCuenta + 0.6;
            }
        }

        $response = array(
            "inscripcion" => $inscripcion,
            "estudiante" => $estudiante,
            "area" => $area,
            "sede" => $sede,
            "periodo" => $periodo,
            "turno" => $turno,
            "pagos" => $pago,
            "inscripcionPagos" => $inscripcionPagos,
            "sumaTotalPagos" => $sumaTotalPagos,
            "tarifaEstudiante" => $tarifaEstudiante,
            "adicional" => $adicionalCuenta
        );

        return response()->json($response);
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
        // dd($request->all());
        DB::beginTransaction();
        try {
            $inscripcion  = Inscripciones::findOrFail($id);

            $input = $request->all();
            // dd($inscripcion->fill($input));
            $inscripcion->update($input);

            DB::commit();
            $message = 'Inscripción actualizada correctamente';
            $status = true;
            $error = '';
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al actualizar inscripción, intentelo nuevamente.';
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => $error
        );

        return response()->json($response);
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
    public function agregarPago(Request $request)
    {
        // dd($request->all());

        $rules = $request->validate([
            'secuencia' => 'required',
            'monto' => 'required',
            'nro_documento' => 'required',
            'fecha' => 'required|date|after:2020-12-14|date_format:Y-m-d',
            // 'concepto' => 'required',
            'tipo_pago' => 'required',
            'folio' => 'required_if:tipo_pago,2',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2000',
            // 'file_documento' => 'required_if:tipo_pago,2|max:2000',
        ], $messages = [
            'required' => '* El campo es obligatorio.',
            'fecha.after' => '* Solo se admiten pagos desde el 15/12/2020.',
            'file.required' => '* El voucher es obligatorio',
            'file.mimes' => '* Solo se admiten formatos pdf,jpg,jpeg,png.',
            'file_documento.mimes' => '* Solo se admite formato pdf.',
            // 'mimes' => '* Formato de archivo invalido',
            'file.max' => '* El peso maximo del archivo debe ser menor a 2 MB.'
        ]);

        $token = Str::random(40);

        $tarifa = Tarifa::where([
            ['modalidad', $request->modalidad],
            ['concepto_pagos_id', '1'],
            ['tipo_estudiante', $request->tipo_pago]
        ])->first();
        $inscripcion = Inscripciones::find($request->idInscripcion);
        if ($inscripcion->tipo_estudiante == '2') {
            $tarifa->importe = $tarifa->importe / 2;
        }

        $pago = Pago::where([
            ["secuencia", $request->secuencia],
            ["monto", $request->monto],
            ["fecha", $request->fecha]
        ])
            ->first();
        if (empty($pago)) {
            $bancoPago = BancoPago::where([
                ["secuencia", $request->secuencia],
                ["imp_pag", $request->monto],
                ["fch_pag", $request->fecha],
                ["num_doc", str_pad($request->nro_documento, 15, '0', STR_PAD_LEFT)],
                ["concepto", '00000067']
            ])
                ->first();

            if (empty($bancoPago)) {
                $response = array(
                    "message" => 'Datos invalidos o el concepto de pago no pertenece a  CEPREUNA, intentelo nuevamente.',
                    "status" => false,
                );
            } else {
                $voucherAdjunto = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'vouchers');

                DB::beginTransaction();
                try {

                    $nuevoPago = new Pago();
                    $nuevoPago->monto = $request->monto;
                    $nuevoPago->secuencia = $request->secuencia;
                    $nuevoPago->fecha = $request->fecha;
                    $nuevoPago->nro_documento = $request->nro_documento;
                    $nuevoPago->token = $token . 'b' . time();
                    $nuevoPago->voucher = $voucherAdjunto;
                    $nuevoPago->folio = isset($request->folio) ? $request->folio : '';
                    $nuevoPago->tipo_pago = $request->tipo_pago;
                    $nuevoPago->estado = '2';
                    $nuevoPago->save();

                    if ($bancoPago->cuenta == '0701010736') {
                        $pagoSinComision = round($nuevoPago->monto, 2);
                    } else {
                        $pagoSinComision = round($nuevoPago->monto - 1, 2);
                    }

                    $sumaInscripcion = InscripcionPago::where('inscripciones_id', $request->idInscripcion)->sum('monto');
                    //  215.00
                    $sumaAmbos = $pagoSinComision + $sumaInscripcion;
                    if (round($sumaInscripcion, 2) >= round($tarifa->importe, 2)) {
                        $inscripcionPago = new InscripcionPago();
                        $inscripcionPago->monto = $pagoSinComision;
                        $inscripcionPago->inscripciones_id = $request->idInscripcion;
                        $inscripcionPago->pagos_id = $nuevoPago->id;
                        $inscripcionPago->concepto_pagos_id = 2;
                        $inscripcionPago->save();
                    } else {
                        if ($sumaAmbos <= $tarifa->importe) {
                            $inscripcionPago = new InscripcionPago();
                            $inscripcionPago->monto = $pagoSinComision;
                            $inscripcionPago->inscripciones_id = $request->idInscripcion;
                            $inscripcionPago->pagos_id = $nuevoPago->id;
                            $inscripcionPago->concepto_pagos_id = 1;
                            $inscripcionPago->save();
                        } else {
                            $diferencia = round($sumaAmbos - $tarifa->importe, 2);
                            if ($diferencia != 0) {
                                $mensualPago = new InscripcionPago();
                                $mensualPago->monto = $diferencia;
                                $mensualPago->inscripciones_id = $request->idInscripcion;
                                $mensualPago->pagos_id = $nuevoPago->id;
                                $mensualPago->concepto_pagos_id = 2;
                                $mensualPago->save();
                            }

                            $inscripcionPago = new InscripcionPago();
                            $inscripcionPago->monto = round($pagoSinComision - $diferencia, 2);
                            $inscripcionPago->inscripciones_id = $request->idInscripcion;
                            $inscripcionPago->pagos_id = $nuevoPago->id;
                            $inscripcionPago->concepto_pagos_id = 1;
                            $inscripcionPago->save();
                        }
                    }

                    DB::commit();
                    $message = 'Pago validado correctamente.';
                    $status = true;
                    $error = '';
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = 'Error al validar pago, intentelo nuevamente.';
                    $status = false;
                    $error = $e;
                }

                $response = array(
                    "message" => $message,
                    "status" => $status,
                    "error" => $error
                );
            }
        } else {
            if ($pago->estado == '1') {
                $updatePago = Pago::find($pago->id);
                $updatePago->estado = '2';
                $updatePago->save();

                $pagoSinComision = round($pago->monto - 1, 2);

                $sumaInscripcion = InscripcionPago::where('inscripciones_id', $request->idInscripcion)->sum('monto');
                //  215.00
                $sumaAmbos = $pagoSinComision + $sumaInscripcion;
                DB::beginTransaction();
                try {
                    if (round($sumaInscripcion, 2) >= round($tarifa->importe, 2)) {
                        $inscripcionPago = new InscripcionPago();
                        $inscripcionPago->monto = $pagoSinComision;
                        $inscripcionPago->inscripciones_id = $request->idInscripcion;
                        $inscripcionPago->pagos_id = $pago->id;
                        $inscripcionPago->concepto_pagos_id = 2;
                        $inscripcionPago->save();
                    } else {
                        if ($sumaAmbos <= $tarifa->importe) {
                            $inscripcionPago = new InscripcionPago();
                            $inscripcionPago->monto = $pagoSinComision;
                            $inscripcionPago->inscripciones_id = $request->idInscripcion;
                            $inscripcionPago->pagos_id = $pago->id;
                            $inscripcionPago->concepto_pagos_id = 1;
                            $inscripcionPago->save();
                        } else {
                            $diferencia = round($sumaAmbos - $tarifa->importe, 2);
                            if ($diferencia != 0) {
                                $mensualPago = new InscripcionPago();
                                $mensualPago->monto = $diferencia;
                                $mensualPago->inscripciones_id = $request->idInscripcion;
                                $mensualPago->pagos_id = $pago->id;
                                $mensualPago->concepto_pagos_id = 2;
                                $mensualPago->save();
                            }

                            $inscripcionPago = new InscripcionPago();
                            $inscripcionPago->monto = round($pagoSinComision - $diferencia, 2);
                            $inscripcionPago->inscripciones_id = $request->idInscripcion;
                            $inscripcionPago->pagos_id = $pago->id;
                            $inscripcionPago->concepto_pagos_id = 1;
                            $inscripcionPago->save();
                        }
                    }
                    DB::commit();
                    $message = 'Pago validado correctamente.';
                    $status = true;
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = 'Error al validar pago, intentelo nuevamente.';
                    $status = false;
                }

                $response = array(
                    "message" => $message,
                    "status" => $status,
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
    public function agregarPagoMora(Request $request)
    {
        // dd($request->all());
        $rules = $request->validate([
            'secuencia' => 'required',
            'monto' => 'required',
            'nro_documento' => 'required',
            'fecha' => 'required|date|after:2020-12-14|date_format:Y-m-d',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:2000',
            // 'file_documento' => 'required_if:tipo_pago,2|max:2000',
        ], $messages = [
            'required' => '* El campo es obligatorio.',
            'required_if' => '* El archivo ajunto es obligatorio.',
            'fecha.after' => '* Solo se admiten pagos desde el 15/12/2020.',
            'file.required' => '* El voucher es obligatorio',
            'file.mimes' => '* Solo se admiten formatos pdf,jpg,jpeg,png.',
            'file_documento.mimes' => '* Solo se admite formato pdf.',
            // 'mimes' => '* Formato de archivo invalido',
            'file.max' => '* El peso maximo del archivo debe ser menor a 2 MB.'
        ]);

        // dd($request->file('file')->getClientOriginalExtension());
        $token = Str::random(40);

        $tarifa = Tarifa::where([
            ['tipo_colegios_id', $request->modalidad],
            ['concepto_pagos_id', '1'],
            ['tipo_estudiante', $request->tipo_pago]
        ])->first();
        // dd($tarifa->importe);

        $pago = Pago::where([
            ["secuencia", $request->secuencia],
            ["monto", $request->monto],
            ["fecha", $request->fecha]
        ])
            ->first();
        if (empty($pago)) {
            $bancoPago = BancoPago::where([
                ["secuencia", $request->secuencia],
                ["imp_pag", $request->monto],
                ["fch_pag", $request->fecha],
                ["num_doc", str_pad($request->nro_documento, 15, '0', STR_PAD_LEFT)],
                ["concepto", '00000067']
            ])
                ->first();

            if (empty($bancoPago)) {
                $response = array(
                    "message" => 'Datos invalidos o el concepto de pago no pertenece a  CEPREUNA, intentelo nuevamente.',
                    "status" => false,
                );
            } else {
                $voucherAdjunto = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'vouchers');

                DB::beginTransaction();
                try {

                    $nuevoPago = new Pago();
                    $nuevoPago->monto = $request->monto;
                    $nuevoPago->secuencia = $request->secuencia;
                    $nuevoPago->fecha = $request->fecha;
                    $nuevoPago->nro_documento = $request->nro_documento;
                    $nuevoPago->token = $token . 'b' . time();
                    $nuevoPago->voucher = $voucherAdjunto;
                    $nuevoPago->tipo_pago = '1';
                    $nuevoPago->estado = '2';
                    $nuevoPago->save();

                    $pagoSinComision = round($nuevoPago->monto - 1, 2);

                    $moraPago = new InscripcionPago();
                    $moraPago->monto = $pagoSinComision;
                    $moraPago->inscripciones_id = $request->idInscripcion;
                    $moraPago->pagos_id = $nuevoPago->id;
                    $moraPago->concepto_pagos_id = '3';
                    $moraPago->save();

                    DB::commit();
                    $message = 'Pago validado correctamente.';
                    $status = true;
                    $error = '';
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = 'Error al validar pago, intentelo nuevamente.';
                    $status = false;
                    $error = $e;
                }

                $response = array(
                    "message" => $message,
                    "status" => $status,
                    "error" => $error
                );
            }
        } else {
            if ($pago->estado == '1') {
                $updatePago = Pago::find($pago->id);
                $updatePago->estado = '2';
                $updatePago->save();

                $pagoSinComision = round($pago->monto - 1, 2);

                //  215.00
                DB::beginTransaction();
                try {

                    $moraPago = new InscripcionPago();
                    $moraPago->monto = $pagoSinComision;
                    $moraPago->inscripciones_id = $request->idInscripcion;
                    $moraPago->pagos_id = $pago->id;
                    $moraPago->concepto_pagos_id = '3';
                    $moraPago->save();

                    DB::commit();
                    $message = 'Pago validado correctamente.';
                    $status = true;
                } catch (\Exception $e) {
                    DB::rollback();
                    $message = 'Error al validar pago, intentelo nuevamente.';
                    $status = false;
                }

                $response = array(
                    "message" => $message,
                    "status" => $status,
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
    public function eliminarVoucher($id)
    {
        DB::beginTransaction();
        try {

            $inscripcionPago = InscripcionPago::where('pagos_id', $id)->delete();
            $voucher = Pago::find($id);
            $voucher->delete();
            DB::commit();

            $message = 'Pago eliminado correctamente.';
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al eliminar pago, intentelo nuevamente.';
            $status = false;
        }

        $response = array(
            "message" => $message,
            "status" => $status,
        );
        return response()->json($response);
    }
    public function save_file($file, $extension, $disk)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk($disk)->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
    public function retirar($id)
    {
        DB::beginTransaction();
        try {
            $inscripcion = Inscripciones::find($id);
            $inscripcion->estado = '2';
            $inscripcion->save();

            $estudiante = Estudiante::find($inscripcion->estudiantes_id);
            $estudiante->idgsuite = '';
            $estudiante->estado = '0';
            $estudiante->save();

            $apiGsuite = new GWorkspace();
            $datos = json_encode(array(
                "userId" => $estudiante->usuario,
            ));
            $eliminar = json_decode($apiGsuite->eliminarCorreo($datos));

            DB::commit();

            $response["message"] = 'Estudiantes retirado y correo Eliminado';
            $response["status"] =  true;

            if ($eliminar->success == false) {
                $response["message"] = 'Estudiantes retirado y El correo que intenta eliminar no existe, o ya ah sido eliminado.';
                $response["status"] =  true;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
}
