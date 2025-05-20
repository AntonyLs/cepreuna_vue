<?php

namespace App\Console\Commands;

use App\Models\CargaAcademica;
use App\Models\ControlCron;
use App\Models\Matricula;
use App\Models\MatriculaDetalle;
use App\Services\GWorkspace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MatriculasClassroom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matricula:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar matriculas al classroom';

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
        $apiGsuite = new GWorkspace();


        // $matriculaDetalle = MatriculaDetalle::select("matricula_detalles.*")
        //     ->join("matriculas as mat", "mat.id", "matriculas_id")
        //     ->join("inscripciones as ins", "ins.estudiantes_id", "=", "mat.estudiantes_id")
        //     ->whereIn("matricula_detalles.estado",["0","2"])
        //     ->where([["matricula_detalles.estado", "0"], ["ins.modalidad", "1"]])
        //     ->get();
        $matriculaDetalle = MatriculaDetalle::select("matricula_detalles.*")
            ->join("matriculas as mat", "mat.id", "matricula_detalles.matriculas_id")
            ->join("inscripciones as ins", "ins.estudiantes_id", "=", "mat.estudiantes_id")
            ->whereIn("matricula_detalles.estado", ["0", "2"])
            ->where("ins.modalidad", "1")
            ->limit(10)
            ->get();

        $url = "matricula-" . time() . ".txt";

        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new ControlCron;
        $control->total_registros = count($matriculaDetalle);
        $control->ejecutado_registros = 0;
        $control->tipo = 1;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = 6;
        $control->save();

        foreach ($matriculaDetalle as $key => $value) {

            $estudiante = Matricula::with('estudiante')->find($value->matriculas_id);
            $curso = CargaAcademica::find($value->carga_academicas_id);


            // sleep(3);
            try {

                $datos = json_encode(array(
                    "courseId" => $curso->idclassroom,
                    "userId" => $estudiante->estudiante->idgsuite,
                ));

                $matricular = json_decode($apiGsuite->matricularEstudiante($datos));
                if ($matricular->success) {
                    $status = true;
                    $response["message"] =  'Success.';
                } else {
                    $status = false;
                    $response["message"] =  'Error.';
                }

                $response["status"] = $status;

                $matricula = MatriculaDetalle::find($value->id);
                $matricula->estado = '1';
                $matricula->save();
                // $response["message"] =  'Error al matricular cursos.';
                // $response["message"] = 'Curso matriculado en ClassRoom.';

            } catch (\Exception $e) {
                $matricula = MatriculaDetalle::find($value->id);
                $matricula->estado = '2';
                $matricula->save();

                $response["message"] =  'Error Exception.';
                $response["status"] =  false;
            }


            $cronActual = ControlCron::find($control->id);
            $cronActual->ejecutado_registros = $key + 1;
            $cronActual->save();

            $texto = "[" . date('Y-m-d H:i:s') . "]: synchronization with id:" . $value->id . ' status: ' . $response["status"] . ' message: ' . $response["message"];
            Storage::disk("crons")->append($url, $texto);
        }

        $cronActual = ControlCron::find($control->id);
        $cronActual->estado = '1';
        $cronActual->save();
    }
}
