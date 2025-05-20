<?php

namespace App\Console\Commands;

use App\Models\MatriculaDetalle;
use Illuminate\Console\Command;

class MatriculaPendienteClassroom extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'matricula:pendiente';

    /**
     * The console command description.
     */
    protected $description = 'Mostrar el número de estudiantes pendientes de sincronización en Classroom';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtener estudiantes con matrícula pendiente incluso si no tienen classroom
        $pendientes = MatriculaDetalle::join("matriculas as mat", "mat.id", "=", "matricula_detalles.matriculas_id")
            ->leftJoin("inscripciones as ins", "ins.estudiantes_id", "=", "mat.estudiantes_id")
            ->join("estudiantes as est", "est.id", "=", "mat.estudiantes_id")
            ->whereIn("matricula_detalles.estado", ["0", "2"]) // No sincronizados
            ->where(function ($query) {
                $query->where("ins.modalidad", "1")
                      ->orWhereNull("ins.modalidad"); // También incluir si no hay inscripción
            })
            ->select("est.nro_documento", "est.nombres", "est.paterno", "est.materno")
            ->distinct()
            ->get();

        $totalPendientes = $pendientes->count();

        if ($totalPendientes === 0) {
            $this->info("✅ Todos los estudiantes están sincronizados con Classroom.");
        } else {
            $this->warn("⚠️ Faltan sincronizar {$totalPendientes} estudiantes en Classroom.");
            $this->line("Lista de estudiantes pendientes:");

            $this->table(
                ['Nro Documento', 'Nombres'],
                $pendientes->map(fn($est) => [
                    $est->nro_documento,
                    "{$est->paterno} {$est->materno}, {$est->nombres}"
                ])
            );
        }
    }
}
