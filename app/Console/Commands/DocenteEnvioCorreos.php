<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\Models\ControlCron;
use Illuminate\Support\Facades\Storage;
use App\Models\InscripcionDocente;
use App\Models\DocenteApto;
use Illuminate\Support\Facades\Mail;

class DocenteEnvioCorreos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correo:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de correos de docentes';

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
        // $correoDocente = new GWorkspace();
        $data = DocenteApto::with("docente")
                    ->whereIn("sincronizar",["0","2"])
                    // ->where("enviar_acceso","1")
                    ->orderBy("sincronizar","desc")
                    ->limit(50)
                    ->get();
        // dd($data);
        $url = "docente-correos-".time() . ".txt";
        Storage::disk("crons")->append($url, "Iniciando sincronizaci�n...");

        $control = new ControlCron;
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 5;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = Auth::user()->id;
        $control->save();

        foreach ($data as $key => $value) {

            // dd($value);
            try {

                $apto = DocenteApto::with("docente")->find($value->id);
                $nombreDocente = $apto->docente->nombres . ' ' . $apto->docente->paterno . ' ' . $apto->docente->materno;

                $this->sendEmail($nombreDocente, $apto->docente->email, $apto->usuario, $apto->password);

                // $response["status"] = $status;
                $response["status"] = true;
                $response["message"] =  'Success.';

                $carga = DocenteApto::find($value->id);
                $carga->sincronizar =  "1";
                $carga->save();
                // $response["message"] = 'Curso matriculado en ClassRoom.';

                // dd($value);
            } catch (\Exception $e){
                // var_dump($value->id);
                $data = DocenteApto::find($value->id);
                // dd($data);
                $data->sincronizar =  "2";
                $data->save();
                // var_dump($key);

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
    public function sendEmail($name, $email, $usuario, $password)
    {
        $to_name = 'Correo Institucional Docente';
        $to_email = $email;

        $datos = array('nombres' => $name, 'usuario' => $usuario, 'password' => $password);

        Mail::send('intranet.emails.cuenta-institucional-docente', $datos, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject($to_name);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
