<?php

namespace App\Console\Commands;

use App\Models\CargaAcademica;
use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\MatriculaDetalle;
use App\Services\GWorkspace;
use Illuminate\Console\Command;

class MatriculaClassroom extends Command
{
    protected $signature = 'matricula:classroom {--documentos=}';

    protected $description = 'Sincronizar matriculas al Classroom';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $apiGsuite = new GWorkspace();
        $documentos = $this->option('documentos');

        $matriculaDetalle = MatriculaDetalle::select("matricula_detalles.*")
            ->join("matriculas as mat", "mat.id", "=", "matricula_detalles.matriculas_id")
            ->join("inscripciones as ins", "ins.estudiantes_id", "=", "mat.estudiantes_id")
            ->join("estudiantes as est", "est.id", "=", "mat.estudiantes_id")
            ->whereIn("matricula_detalles.estado", ["0", "2"])
            ->where("ins.modalidad", "1");

        if ($documentos) {
            $documentosArray = explode(',', $documentos);
            $matriculaDetalle->whereIn("est.nro_documento", $documentosArray);
        }

        $matriculaDetalle = $matriculaDetalle->get();

        $this->info("Iniciando sincronización...");

        foreach ($matriculaDetalle as $value) {
            $estudiante = Matricula::with('estudiante')->find($value->matriculas_id);
            $curso = CargaAcademica::find($value->carga_academicas_id);

            if (!$estudiante || !$curso) {
                $this->error("No se encontró información para el estudiante o el curso en la matrícula ID: {$value->id}");
                continue;
            }

            try {
                $datos = json_encode([
                    "courseId" => $curso->idclassroom,
                    "userId" => $estudiante->estudiante->idgsuite,
                ]);

                $matricular = json_decode($apiGsuite->matricularEstudiante($datos));
                $status = $matricular->success ? true : false;
                $message = $status ? 'Success' : 'Error';

                $matricula = MatriculaDetalle::find($value->id);
                $matricula->estado = $status ? '1' : '2';
                $matricula->save();
            } catch (\Exception $e) {
                $matricula = MatriculaDetalle::find($value->id);
                $matricula->estado = '2';
                $matricula->save();
                $status = false;
                $message = 'Error Exception';
            }

            $this->info("[ " . date('Y-m-d H:i:s') . " ] ID: {$value->id} - Estudiante: {$estudiante->estudiante->nombres} - Estado: " . ($status ? 'OK' : 'Fallo') . " - Mensaje: $message");
        }
    }
}
