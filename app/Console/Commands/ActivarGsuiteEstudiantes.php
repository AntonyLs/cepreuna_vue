<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Matricula;
use App\Http\Controllers\Intranet\Administracion\EstudianteController;

class ActivarGsuiteEstudiantes extends Command
{
    protected $signature = 'estudiantes:activar-gsuite';
    protected $description = 'Activa el Gsuite para todos los estudiantes matriculados sin idgsuite';

    public function handle()
    {
        $estudiantes = Matricula::whereHas('estudiante', function ($query) {
            $query->whereNull('idgsuite')->orWhere('idgsuite', '');
        })->with('estudiante')->get();

        $total = $estudiantes->count();
        $this->info("Se encontraron $total estudiantes sin Gsuite. Activando...");

        $controlador = new EstudianteController(); // Instancia del controlador

        foreach ($estudiantes as $matricula) {
            $estudiante = $matricula->estudiante;
            

            $response = $controlador->activarGsuite($estudiante->id);
            $this->info("Intentando activar GSuite para: " . $estudiante->nro_documento ."estudiante". $estudiante->nombres);
        }

        $this->info("Proceso completado.");
    }
}
