<?php

namespace App\Console\Commands;

use App\Models\AsistenciaEstudianteDetalle;
use App\Models\ControlCron;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Models\Matricula;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HabilitacionEstudiantesDga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habilitacion:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Habilitacion de estudiantes para registro de informacion en base de datos DGA';

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
        // $apiGsuite = new GWorkspace();


        $data =  Matricula::where([["habilitado_estado", "0"], ["habilitado", "1"]])->get();
        $url = "habilitacion-" . time() . ".txt";

        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new ControlCron();
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 3;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = Auth::user()->id;
        $control->save();

        foreach ($data as $key => $value) {

            $estudiante = Estudiante::with('colegio')->find($value->estudiantes_id);
            $inscripciones = Inscripciones::where("estudiantes_id", $value->estudiantes_id)->first();
            $matriculaEncrypt = Crypt::encryptString($value->id);

            $totalAsistencias = AsistenciaEstudianteDetalle::where([["estudiantes_id", $estudiante->id], ["estado", '!=', '3']])->groupBy("estudiantes_id")->count("estudiantes_id");

            $estudianteUb = DB::table('estudiantes as e')
                ->select('u.codigo_distrito as ubigeo', 'ub.codigo_distrito as ubigeos_nacimiento')
                ->join('ubigeos as u', 'u.id', 'e.ubigeos_id')
                ->leftjoin('ubigeos as ub', 'ub.id', 'e.ubigeos_nacimiento')
                ->where('e.id', $value->estudiantes_id)
                ->first();

            $tipo = "";
            switch ($inscripciones->tipo_estudiante) {
                case '1':
                    $tipo = "normal";
                    # biomedicas...
                    break;
                case '2':
                    $tipo = "hijo de trabajador";
                    # ingenierias...
                    break;
                case '3':
                    $tipo = "descuento trabajador UNA";
                    # sociales...
                    break;
                case '4':
                    $tipo = "descuento hermanos";
                    # sociales...
                    break;
                case '5':
                    $tipo = "inscripcion por RR";
                    # sociales...
                    break;
                case '6':
                    $tipo = "Servicio Militar";
                    # sociales...
                    break;

                default:
                    $tipo = "";
                    break;
            }
            DB::beginTransaction();
            try {

                // DB::connection("dga")->table("c_cepreuna_07082022")->insert([
                //     "correo" => $estudiante->usuario,
                //     "dni" => $estudiante->nro_documento,
                //     "nombres" => $estudiante->nombres,
                //     "paterno" => $estudiante->paterno,
                //     "materno" => $estudiante->materno,
                //     "tipo" => $tipo,
                //     "tipo_colegio" => $estudiante->colegio->tipo_colegio->id == 1 ? 'particular' : 'publico',
                //     "asistencia" => $totalAsistencias,
                //     "habilitado" => $value->habilitado,
                //     "fecha" => date('Y-m-d H:m:s'),
                //     "fecha_nacimiento" => $estudiante->fecha_nac,
                //     "ubigeo_nacimiento" => isset($estudianteUb->ubigeos_nacimiento) ? $estudianteUb->ubigeos_nacimiento : $estudianteUb->ubigeo,
                //     "anio_egreso" => $estudiante->anio_egreso,
                //     "foto" => url('') . "/dga/estudiantes/foto/" . $matriculaEncrypt,
                //     "constancia" => url('') . "/dga/estudiantes/pdf-constancia/" . $matriculaEncrypt,
                //     "sexo" => $estudiante->sexo == '1' ? 'M':'F'

                // ]);

                DB::commit();
                $message = 'Sincronización realizada con exito.';
                $status = true;

                $matricula = Matricula::find($value->id);
                $matricula->habilitado_estado =  '1';
                $matricula->save();
            } catch (\Exception $e) {
                DB::rollback();
                $message = 'Error al sincronizar - ' . $e->getMessage();
                $status = false;

                $matricula = Matricula::find($value->id);
                $matricula->habilitado_estado =  "2";
                $matricula->save();
            }

            $response["message"] =  $message;
            $response["status"] =  $status;

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
