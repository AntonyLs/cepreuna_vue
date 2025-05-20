<?php

namespace App\Http\Controllers\Intranet\Auxiliar;

use App\Http\Controllers\Controller;
use App\Models\CalificacionDocente;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Horario;
use App\Models\AsistenciaDocente;
use App\Models\CargaAcademica;
use App\Models\Sesiones;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class AsistenciaDocenteController extends Controller
{
    private $dateTime;
    private $dateTimePartial;

    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }
    public function index()
    {
        $response["fecha"] = date("Y-m-d");
        // $response["fecha"] = "2021-01-25";
        $permissions = [];
        if (auth()->user()->hasRole('Super Admin')) {
            foreach (Permission::get() as $key => $value) {
                array_push($permissions, $value->name);
            }
        } else {
            foreach (Auth::user()->getAllPermissions() as $key => $value) {
                array_push($permissions, $value->name);
            }
        }
        $response['permisos'] = json_encode($permissions);
        return view("intranet.auxiliar.asistencia.docente", $response);
    }
    public function store(Request $request)
    {
        
        
        // dd($request);
        // dd($request->file('imagen')->getClientOriginalExtension());
        $rules = $request->validate([
            'status_tema' => 'required',
            'cantidad_estudiantes' => 'required',
            'horas_asistidas' => 'required',
            // 'imagen' => 'image',
            'fecha_tema' => 'required_if:status_tema,2',
            'tema' => 'required_if:status_tema,2',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'required_if' => '* El campo :attribute es obligatorio.',
            'imagen.image' => '* Ingrese una imagen valida'
        ]);

        if($request->input('fecha_tema')!=date('Y-m-d')){
            
            if(!auth()->user()->hasPermissionTo('registro asistencia web')){
                return response([
                    'errors' => [
                        'fecha_tema' => ['No estas autorizado a validar esta fecha'],
                        
                    ],
                    'message'=>'No',
                ],422);
            }
        }
        
        $id = Auth::user()->id;
        $fecha = new \DateTime($request->fecha);
        $semana = $fecha->format("N");
        $horario = Horario::select("horarios.*", "ph.hora_inicio", "ph.hora_fin")
            ->where([
                ["carga_academicas_id", $request->carga],
                ["dia", $semana]
            ])
            ->join("plantilla_horarios as ph", "ph.id", "horarios.plantilla_horarios_id")
            ->orderBy("hora_inicio", "asc")
            ->get();
        // dd(reset($horario));
        // var_dump($horario);
        $cantidadHoras = count($horario);
        if(isset($request->imagen)){

            $path_imagen = $this->save_file($request->file('imagen'), $request->file('imagen')->getClientOriginalExtension());
        }
        DB::beginTransaction();
        try {

            $data = new AsistenciaDocente;
            $data->estado = $request->estado;
            $data->fecha = $request->fecha;
            $data->hora_inicio = $horario[0]->hora_inicio;
            $data->hora_fin = $horario[$cantidadHoras - 1]->hora_fin;
            $data->cantidad_horas = $cantidadHoras;
            $data->horas_pago = $request->horas_asistidas;
            $data->docentes_id = $request->docente;
            $data->carga_academicas_id = $request->carga;
            if($request->status_tema==1){
                $data->sesiones_id = $request->sesion;
            }
            if($request->status_tema==2&&$request->estado!=3){
                $sesion = new Sesiones;
                $sesion->tema = $request->tema;
                $sesion->fecha = $request->fecha_tema;
                $sesion->carga_academicas_id = $request->carga;
                $sesion->save();
                $data->sesiones_id = $sesion->id;
            }
            $data->cantidad_estudiantes = $request->cantidad_estudiantes;
            if(isset($request->imagen)){

                $data->path_imagen = $path_imagen;
            }
            $data->observacion = $request->observacion;
            $data->users_id = $id;
            $data->save();

            if ($request->estado == 1 || $request->estado == 2) {
                /* $calificacion = new CalificacionDocente;
                $calificacion->participantes = 0;
                $calificacion->docentes_id = $request->docente;
                $calificacion->carga_academicas_id = $request->carga;
                $calificacion->asistencia_docentes_id = $data->id;
                $calificacion->save(); */
            }


            DB::commit();
            $response["message"] = 'Asistencia Validada';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al validar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
    }
    public function regularizar(Request $request)
    {
        $rules = $request->validate([
            'cargaR' => 'required',
            'inicioR' => 'required',
            'finR' => 'required',
            'cantidadR' => 'required',
            'estadoR' => 'required',
            'observacionR' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        $id = Auth::user()->id;
        $carga = CargaAcademica::find($request->cargaR);
        DB::beginTransaction();
        try {

            $data = new AsistenciaDocente;
            $data->estado = $request->estadoR;
            $data->fecha = $request->fecha;
            $data->hora_inicio = $request->inicioR;
            $data->hora_fin = $request->finR;
            $data->cantidad_horas = $request->cantidadR;
            $data->docentes_id = $carga->docentes_id;
            $data->carga_academicas_id = $request->cargaR;
            // $data->sesiones_id = $request->sesion;
            $data->observacion = $request->observacionR;
            $data->users_id = $id;
            $data->save();


            DB::commit();
            $response["message"] = 'Asistencia Regaularizada';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Regularizar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
        // dd($request);
    }
    public function saveImageExterno(Request $request){
        // return($request);
        if(isset($request->imagen)){
            // $path_imagen = $this->save_file($request->file('imagen'), $request->file('imagen')->getClientOriginalExtension());
            $response["status"] = true;
            $response["message"] = "Mensaje enviado";
        }else{
            $response["status"] = false;
            $response["message"] = "Mensaje no enviado";
        }
    }
    public function save_file($file, $extension)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk('imagenes')->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }

}
