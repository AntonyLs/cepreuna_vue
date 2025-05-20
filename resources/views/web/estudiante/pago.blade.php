@extends('layouts.estudiante')
@section('titulo', ' Pago')

@section('content')
    {{-- {{var_dump($cronograma->inicio.' ****** '.date("Y-m-d")).'***********'.$cronograma->fin}} --}}
    <div class="row">
        <div class="col-md-7 col-xs-12">

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"> Registro de Pagos </h5>
                    <div class="small text-muted">Pago de cuota N° {{ $cronograma->nro_cuota }} del
                        {{ date('d-m-Y', strtotime($cronograma->inicio)) }} al
                        {{ date('d-m-Y', strtotime($cronograma->fin)) }}</div>
                </div>
                <div class="card-body">
                    <div class="text-center font-weight-bold">
                        @switch($inscripcion->tipo_estudiante)
                            @case('2')
                                Descuento por Hijo de trabajador
                            @break

                            @case('3')
                                Descuento por planilla
                            @break

                            @case('4')
                                Descuento por hermanos
                            @break

                            @case('5')
                                Descuento por Resolucion Rectoral
                            @break

                            @case('6')
                                Descuento por Servicio Militar
                            @break

                            @default
                                Normal (sin descuento)
                        @endswitch
                    </div>
                    {{-- <w-resumen-pago></w-resumen-pago> --}}
                    @if ($inscripcion->tipo_estudiante == '5' || $inscripcion->tipo_estudiante == '3')
                        <div class="alert alert-success" role="alert">
                            <p class="mb-0 font-weight-bold">Aviso.</p>
                            <p class="mb-0">El tipo de descuento al que accede no requiere registro de pagos.</p>
                        </div>
                    @else
                        @if (date('Y-m-d') >= $cronograma->inicio && date('Y-m-d') <= $cronograma->fin)
                            @if ($total_pagado >= $total_pagar)
                                <div class="alert alert-success" role="alert">
                                    <p class="mb-0 font-weight-bold">Aviso.</p>
                                    <p class="mb-0">Ud. ya cumplio con el pago del monto correspondiente a la
                                        cuota N° {{ $cronograma->nro_cuota }}.</p>
                                </div>
                            @else
                                <div class="alert alert-info" role="alert">
                                    <p class="mb-0"> Añade el voucher de pago segun el monto que le corresponda y
                                        luego presione <b>Guardar Pago</b> para registrar su pago.</p>
                                    <br>
                                    <p class="mb-0"><b>Importante:</b></p>
                                    <p class="mb-0"> el monto de <b>S/ 0.60</b> es por comisión al Banco de la
                                        Nación </p>
                                </div>
                                <w-pago-normal></w-pago-normal>
                            @endif
                        @elseif(date('Y-m-d') > $cronograma->fin)
                            @if ($total_pagado >= $total_pagar)
                                <div class="alert alert-success" role="alert">
                                    <p class="mb-0 font-weight-bold">Aviso.</p>
                                    <p class="mb-0">Ud. ya cumplio con el pago del monto correspondiente a la
                                        cuota N° {{ $cronograma->nro_cuota }}.</p>
                                </div>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <p class="mb-0"><b>Aviso</b>:</p>
                                    <p>Pago atrasado, debera agregar <b>S/ 30.60 </b> de mora.</p>
                                    {{-- <p class="mb-0">Comuniquese con CepreUNA para regularizar su pago</p> --}}
                                </div>
                                <w-pago-mora></w-pago-mora>
                            @endif
                        @else
                            <div class="alert alert-success" role="alert">
                                <p class="mb-0 font-weight-bold">Aviso.</p>
                                <p class="mb-0">El registro de pagos aun no ha comenzado.</p>
                            </div>

                        @endif
                    @endif
                    @if ($matricula)
                        <w-comunicado-habilitado idmatricula="{{ $idmatricula }}"></w-comunicado-habilitado>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-5 col-xs-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"> Vouchers de Pago Registrados </h5>
                    <div class="small text-muted"> Pagos con comisión al Banco de la Nacion S/ 0.60</div>
                </div>
                <div class="card-body">
                    <w-vouchers-pago></w-vouchers-pago>
                    {{-- <div class="alert alert-success" role="alert">
                          <p class="mb-0 font-weight-bold">Aviso.</p>
                          <p class="mb-0">El registro de pagos aun no esta habilitado.</p>
                        </div> --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

@endsection
