<?php

namespace App\Console\Commands;

use App\Models\CargaAcademica;
use App\Models\ControlCron;
use App\Models\GrupoAula;
use App\Services\GWorkspace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GruposClassroom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grupo:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar grupos al classroom';

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


        $data = CargaAcademica::where("sincronizar", "0")->get();
        $url = "grupo-".time() . ".txt";

        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new ControlCron;
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 2;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = Auth::user()->id;
        $control->save();

        foreach ($data as $key => $value) {

            // $estudiante = Matricula::with('estudiante')->find($value->matriculas_id);
            $carga = CargaAcademica::with("curso")->find($value->id);
            $grupoAula = GrupoAula::with(["grupo","area"])->find($value->grupo_aulas_id);


            // sleep(3);
            try {

                $correo = "";
                switch ($grupoAula->area->id) {
                    case 1:
                        $correo = "biomedicas@cepreuna.edu.pe";
                        # biomedicas...
                        break;
                    case 2:
                        $correo = "ingenierias@cepreuna.edu.pe";
                        # ingenierias...
                        break;
                    case 3:
                        $correo = "sociales@cepreuna.edu.pe";
                        # sociales...
                        break;

                    default:
                        $correo = "";
                        break;
                }
                $datos = json_encode(array(
                    "name" => $carga->curso->denominacion,
                    "section" => $grupoAula->grupo->denominacion,
                    "room" => $grupoAula->area->denominacion.' ['.$grupoAula->grupo->denominacion.']',
                    "owner" => $correo
                ));

                $cursoCreado = json_decode($apiGsuite->crearCurso($datos));
                if ($cursoCreado->success) {
                    $status = true;
                    $response["message"] =  'Success.';
                } else {
                    $status = false;
                    $response["message"] =  'Error.';
                }

                $response["status"] = $status;

                $carga = CargaAcademica::find($value->id);
                $carga->idclassroom =  $cursoCreado->message->id;
                $carga->sincronizar =  "1";
                $carga->save();
                // $response["message"] =  'Error al matricular cursos.';
                // $response["message"] = 'Curso matriculado en ClassRoom.';

            } catch (\Exception $e) {
                $carga = CargaAcademica::find($value->id);
                $carga->sincronizar =  "2";
                $carga->save();

                $response["message"] =  'Error Exception.';
                $response["status"] =  false;
            }


            $cronActual = ControlCron::find($control->id);
            $cronActual->ejecutado_registros = $key + 1;
            $cronActual->save();

            $texto = "[" . date('Y-m-d H:i:s') . "]:  registration synchronization with id:" . $value->id . ' status: ' . $response["status"] . ' message: ' . $response["message"];
            Storage::disk("crons")->append($url, $texto);
        }

        $cronActual = ControlCron::find($control->id);
        $cronActual->estado = '1';
        $cronActual->save();
    }
}
