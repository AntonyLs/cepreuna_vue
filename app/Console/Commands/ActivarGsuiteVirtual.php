<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Estudiante;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Intranet\Administracion\EstudianteController;

class ActivarGsuiteVirtual extends Command
{
    protected $signature = 'gsuite:activar-virtuales';
    protected $description = 'Activa GSuite solo para estudiantes de modalidad virtual (sedes_id = 1)';

    public function handle()
{
    // Obtener los IDs de estudiantes inscritos (estado = 1) en modalidad virtual (sedes_id = 1)
    $estudiantesVirtuales = DB::table('inscripciones')
        ->where('sedes_id', 1) // Modalidad virtual
        ->where('estado', 1)    // Solo inscritos
        ->pluck('estudiantes_id')
        ->toArray();

    $totalVirtuales = count($estudiantesVirtuales);
    $activados = 0;

    if ($totalVirtuales === 0) {
        $this->info('No hay estudiantes inscritos en modalidad virtual para activar.');
        return;
    }

    $controlador = new EstudianteController();

    foreach ($estudiantesVirtuales as $id) {
        $respuesta = $controlador->activarGsuite($id);

        // Revisar si la activación fue exitosa
        if ($respuesta->getData()->status) {
            $activados++;
        } elseif (strpos($respuesta->getData()->message, 'exceeded') !== false) {
            $this->error("Error: Workspace lleno. No se pueden activar más cuentas.");
            break;
        }

        sleep(2); // Evita saturar la API de Google
    }

    $this->info("Total estudiantes inscritos en virtual: $totalVirtuales");
    $this->info("GSuite activado para: $activados estudiantes.");

    if ($activados < $totalVirtuales) {
        $this->warn("Algunos estudiantes no fueron activados. Puede que el Workspace esté lleno.");
    }
}
}
