<?php

namespace App\Console\Commands;

use App\Models\InscripcionPago;
use App\Models\Tarifa;
use App\Models\TarifaEstudiante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TarifaEstudianteSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarifa:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarifas Estudiantes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inscripciones  =  DB::table("inscripciones as i")
            ->select("*")
            ->where("i.estado", "1")
            ->get();
        // dd($inscripciones);
        // 1:normal  2:hijo de trabajador 3:descuento trabajdor UNA 4: hermanos 5: inscripcion por RR: 6 servicio militar

        // $tarifaMensual = Tarifa::where([
        //     ['modalidad', $inscripcion->modalidad],
        //     ['concepto_pagos_id', '2'],
        //     ['tipo_estudiante', $descuento]
        // ])->first();

        foreach ($inscripciones as $key => $inscripcion) {

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

            if ($inscripcion->tipo_estudiante == '2') {
                $tarifaInscripcion->importe = $tarifaInscripcion->importe / 2;
            }

            $pagoActual = InscripcionPago::where([["inscripciones_id", $inscripcion->id], ["concepto_pagos_id", "!=", 3]])->sum("monto");
            $mora = InscripcionPago::where([["inscripciones_id", $inscripcion->id], ["concepto_pagos_id", 3]])->sum("monto");
            // dd($mora);
            // 400  400-250 = 150
            $resto = $pagoActual;
            for ($i = 0; $i < 5; $i++) {
                if ($i == 0) {
                    // $monto = $resto;

                    $tarifaEstudiante = new TarifaEstudiante();
                    $tarifaEstudiante->monto = $tarifaInscripcion->importe;
                    if ($resto == 0) {

                        $tarifaEstudiante->pagado = 0;
                    } else if ($resto > 0 && $resto - $tarifaInscripcion->importe < 0) {
                        $tarifaEstudiante->pagado =  $resto;
                        $resto = 0;
                    } else {
                        $tarifaEstudiante->pagado =  $tarifaInscripcion->importe;
                        $resto = $resto - $tarifaInscripcion->importe;
                    }
                    $tarifaEstudiante->nro_cuota = $i;
                    $tarifaEstudiante->tipo_estudiante = $inscripcion->tipo_estudiante;
                    $tarifaEstudiante->modalidad = $inscripcion->modalidad;
                    $tarifaEstudiante->estudiantes_id = $inscripcion->estudiantes_id;
                    $tarifaEstudiante->save();
                } else {
                    $tarifaEstudiante = new TarifaEstudiante();
                    $tarifaEstudiante->monto = $tarifaMensual->importe;
                    if ($resto == 0) {

                        $tarifaEstudiante->pagado = 0;
                    } else if ($resto > 0 && $resto - $tarifaMensual->importe < 0) {
                        $tarifaEstudiante->pagado =  $resto;
                        $resto = 0;
                    } else {
                        $tarifaEstudiante->pagado =  $tarifaMensual->importe;
                        $resto = $resto - $tarifaMensual->importe;
                    }
                    $tarifaEstudiante->nro_cuota = $i;
                    $tarifaEstudiante->tipo_estudiante = $inscripcion->tipo_estudiante;
                    $tarifaEstudiante->modalidad = $inscripcion->modalidad;
                    $tarifaEstudiante->estudiantes_id = $inscripcion->estudiantes_id;
                    $tarifaEstudiante->save();
                }
            }
            $tarifaMora = TarifaEstudiante::where([["estudiantes_id", $inscripcion->estudiantes_id], ["nro_cuota", 1]])->first();
            $tarifaMora->mora = $mora;
            $tarifaMora->save();
        }
    }
}
