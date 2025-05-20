<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocenteApto;

class AsignarRolesDocentes extends Command
{
    protected $signature = 'asignar:roles-docentes';
    protected $description = 'Asigna el rol Docente a todos los DocenteApto que no tengan roles';

    public function handle()
    {
        $docentesSinRol = DocenteApto::doesntHave('roles')->get();

        foreach ($docentesSinRol as $docente) {
            $docente->assignRole('Docente');
        }

        $this->info("Se asignó el rol 'Docente' a {$docentesSinRol->count()} docentes.");
    }
}
