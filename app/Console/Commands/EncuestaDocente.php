<?php

namespace App\Console\Commands;

use App\Models\AsistenciaEstudianteDetalle;
use App\Models\ControlCron;
use App\Models\Estudiante;
use App\Models\GrupoAula;
use App\Models\Inscripciones;
use App\Models\Matricula;
use App\Models\CalificacionDocente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EncuestaDocente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encuesta:task {estado=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Habilitacion de grupos de estudiantes para encuesta de docentes';

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

        $data = GrupoAula::where('estado_encuesta', '0')->get();

        $url = "habilitacion-encuesta" . time() . ".txt";

        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new ControlCron();
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 15;
        $control->estado = $this->argument('estado');
        $control->url = $url;
        if (Auth::check()) {
            $control->users_id = Auth::user()->id;
        } else {
            $control->users_id = 1;
        }
        // $control->users_id = '1';
        $control->save();

        //cambiar el estado de grupo aula a 1
        $filasAfectadas = GrupoAula::query()->update(['estado_encuesta' => $this->argument('estado')]);
        if ((int)$this->argument('estado') == 1)
            $this->UpdateStadoCalificacion();

        $message = 'Sincronización realizada con exito.';
        $status = true;
        $response["message"] =  $message;
        $response["status"] =  $status;

        $cronActual = $control;
        $cronActual->ejecutado_registros = $filasAfectadas;
        $cronActual->save();

        $texto = "[" . date('Y-m-d H:i:s') . "]:  registration synchronization with rows affected: " . $filasAfectadas . ' status: ' . $response["status"] . ' message: ' . $response["message"];
        Storage::disk("crons")->append($url, $texto);

        $cronActual->estado = '1';
        $cronActual->save();
    }

    public function UpdateStadoCalificacion(){
        $data = CalificacionDocente::where("estado", '1')->get();
        $url = "Calificacion-".time() . ".txt";

        Storage::disk("crons")->append($url, "Iniciando sincronización estado...");

        $control = new ControlCron;
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 20;
        $control->estado = '0';
        $control->url = $url;
        if (Auth::check()) {
            $control->users_id = Auth::user()->id;
        } else {
            $control->users_id = 1;
        }
        $control->save();

        foreach ($data as $key => $value) {
            // var_dump($value);
            // dd($value);
            try {
                $calificacion = CalificacionDocente::find($value->id);
                $calificacion->estado =  "0";
                $calificacion->save();
                $response["message"] = 'calificacion sincronizada.';
                $response["status"] =  false;
                // dd($value);
            } catch (\Exception $e){

                $calificacion = CalificacionDocente::find($value->id);
                $calificacion->estado =  "1";
                $calificacion->save();

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
