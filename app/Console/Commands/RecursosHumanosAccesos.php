<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TPControlCron;
use App\Models\TPDocente;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecursosHumanosAccesos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accesos_rh:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar correos docentes RH';

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
        $data = TPDocente::where("estado_correo",'1')
                    ->limit(50)
                    ->get();
        // dd($data);
        $url = "RH-accesos-docentes-".time() . ".txt";
        Storage::disk("crons")->append($url, "Iniciando sincronización...");

        $control = new TPControlCron;
        $control->total_registros = count($data);
        $control->ejecutado_registros = 0;
        $control->tipo = 1;
        $control->estado = '0';
        $control->url = $url;
        $control->users_id = Auth::user()->id;
        $control->save();

        foreach ($data as $key => $value) {

            // dd($value);
            try {

                // $apto = DocenteApto::with("docente")->find($value->id);
                $nombreDocente = $value->nombres . ' ' . $value->paterno . ' ' . $value->materno;
                // dd($value);

                $this->sendEmail($nombreDocente, $value->email, $value->email, $value->password);

                // $response["status"] = $status;
                $response["status"] = true;
                $response["message"] =  'Success.';

                $docente = TPDocente::find($value->id);
                $docente->estado_correo =  "2";
                $docente->save();
                // $response["message"] = 'Curso matriculado en ClassRoom.';

                // dd($value);
            } catch (\Exception $e){
                // var_dump($value->id);
                $docente = TPDocente::find($value->id);
                // dd($docente);
                $docente->estado_correo = "1";
                $docente->save();
                // var_dump($key);

                $response["message"] =  'Error Exception.';
                $response["status"] =  false;
            }


            $cronActual = TPControlCron::find($control->id);
            $cronActual->ejecutado_registros = $key + 1;
            $cronActual->save();

            $texto = "[" . date('Y-m-d H:i:s') . "]:  registration synchronization with id:" . $value->id . ' status: ' . $response["status"] . ' message: ' . $response["message"];
            Storage::disk("crons")->append($url, $texto);
        }

        $cronActual = TPControlCron::find($control->id);
        $cronActual->estado = '1';
        $cronActual->save();
    }
    public function sendEmail($name, $email, $usuario, $password)
    {
        $to_name = 'Trámite de pago';
        $to_email = $email;

        $datos = array('nombres' => $name, 'usuario' => $usuario, 'password' => $password);

        Mail::send('intranet.emails.accesos-rh', $datos, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject($to_name);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
