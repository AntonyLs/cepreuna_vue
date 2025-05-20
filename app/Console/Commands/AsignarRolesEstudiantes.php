<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Estudiante;

class AsignarRolesEstudiantes extends Command
{
    protected $signature = 'asignar:roles-estudiantes';
    protected $description = 'Asigna el rol Estudiante a los estudiantes sin rol';

    public function handle()
    {
        $estudiantesSinRol = Estudiante::doesntHave('roles')->get();

        foreach ($estudiantesSinRol as $estudiante) {
            $estudiante->assignRole('Estudiante');
        }

        $this->info("Se asignó el rol 'Estudiante' a {$estudiantesSinRol->count()} estudiantes.");
    }
}
