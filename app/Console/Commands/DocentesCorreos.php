<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GWorkspace;
use Illuminate\Support\Facades\Auth;
use App\Models\ControlCron;
use Illuminate\Support\Facades\Storage;
use App\Models\InscripcionDocente;
use App\Models\DocenteApto;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class DocentesCorreos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docente:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear correos para docentes';

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
        $correoDocente = new GWorkspace();
        $data = InscripcionDocente::with("docente")->whereIn("sincronizar", ["0","2"])->where("apto","1")->get();
        $url = "docente-".time() . ".txt";
        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new ControlCron;
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 4;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = Auth::user()->id;
        $control->save();

        foreach ($data as $key => $value) {
            // var_dump($value);
            // dd($value);
            try {
                // dd($value);
                $password = Str::random(8);
                $docente = $value->docente;
                $nombres = $docente->nombres;
                $paterno = $docente->paterno;
                $materno = $docente->materno;

                $correo = "d_";

                $nameArray = explode(" ", $nombres);
                if (count($nameArray) >= 2) {
                    foreach ($nameArray as  $val) {
                        $correo .= substr(Str::slug($val, ""), 0, 1);
                    }
                } else {
                    $correo .= substr(Str::slug($nombres, ""), 0, 1);
                }

                $correo .= Str::slug($paterno, "");
                $query = DocenteApto::where("usuario", ($correo . "@cepreuna.edu.pe"))->first();
                // dd($query);
                if ($query != NULL) {
                    $correo .= substr(Str::slug($materno, ""), 0, 1);
                    $query1 = DocenteApto::where("usuario", ($correo . "@cepreuna.edu.pe"))->first();
                    if ($query1 != NULL) {
                        $correo .= Str::slug($materno, "");
                    }
                }

                $correo .= "@cepreuna.edu.pe";
                $datos = json_encode(array(
                    "primaryEmail" => $correo,
                    "name_givenName" => $docente->nombres,
                    "name_familyName" => $docente->paterno . ' ' . $docente->materno,
                    "orgUnitPath"  => env('ORGUNITPATHD'),
                    "password"  => $password,
                    "recoveryEmail"  => $docente->email,
                    "recoveryPhone"  => "",
                    "customSchemas_Institution_Document"  => $docente->nro_documento,
                    "customSchemas_Institution_Type"  => "Teacher"
                ));
                $correoGenerado = json_decode($correoDocente->correo($datos));
                if ($correoGenerado->success) {
                    $status = true;
                    $response["message"] =  'Success.';
                } else {
                    $status = false;
                    $response["message"] =  'Error.';
                }

                $apto = new DocenteApto;
                $apto->usuario = $correo;
                $apto->password = $password;
                $apto->idgsuite = $correoGenerado->message->id;
                // $apto->idgsuite = '123456789';
                $apto->docentes_id = $value->docentes_id;
                $apto->periodos_id = $value->periodos_id;
                $apto->save();

                $apto->syncRoles('Docente');
                // $nombreDocente = $docente->nombres . ' ' . $docente->paterno . ' ' . $docente->materno;

                // $this->sendEmail($nombreDocente, $docente->email, $apto->usuario, $apto->password);

                $response["status"] = $status;
                // $response["status"] = true;
                // $response["message"] =  'Correo sincronizado';

                $carga = InscripcionDocente::find($value->id);
                $carga->sincronizar =  "1";
                $carga->save();
                // $response["message"] = 'Curso matriculado en ClassRoom.';

                // dd($value);
            } catch (\Exception $e){

                $carga = InscripcionDocente::find($value->id);
                $carga->sincronizar =  "2";
                $carga->save();

                $response["message"] =  'Error Exception.'.$e->getMessage();
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
    public function sendEmail($name, $email, $usuario, $password)
    {
        $to_name = 'Correo Institucional Docente';
        $to_email = $email;

        $datos = array('nombres' => $name, 'usuario' => $usuario, 'password' => $password);

        Mail::send('intranet.emails.cuenta-institucional', $datos, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject($to_name);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
