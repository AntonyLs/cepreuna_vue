<?php

namespace App\Http\Controllers\Intranet\CoordinadorAuxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use App\Models\Horario;
use App\Models\AsistenciaDocente;
use App\Models\CalificacionDocente;
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
        return view("intranet.coordinadorAuxiliar.asistencia.docente", $response);
    }
    public function store(Request $request)
    {
        $rules = $request->validate([
            'modalidad' => 'required',
            'cantidad_estudiantes' => 'required',
            'horas_asistidas' => 'required',
            'imagen' => 'required|image',
            'fecha_tema' => 'required_if:modalidad,2',
            'tema' => 'required_if:modalidad,2',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'required_if' => '* El campo :attribute es obligatorio.',
            'imagen.image' => '* Ingrese una imagen valida'
        ]);
        
        
        // dd($request->input('fecha_tema')==null);
        if($request->input('fecha_tema')!=date('Y-m-d')){
            if(!auth()->user()->hasPermissionTo('registro asistencia web')){
                // dd("ggwp");
                
                return response([
                    'errors' => [
                        'fecha_tema' => ['No estas autorizado a validar esta fecha'],
                        'imagen' => ['No estas autorizado a validar esta fecha'],
                    ],
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
        $path_imagen = $this->save_file($request->file('imagen'), $request->file('imagen')->getClientOriginalExtension());
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
            if($request->modalidad==1){
                $data->sesiones_id = $request->sesion;
            }
            if($request->modalidad==2&&$request->estado!=3){
                $sesion = new Sesiones;
                $sesion->tema = $request->tema;
                $sesion->fecha = $request->fecha_tema;
                $sesion->carga_academicas_id = $request->carga;
                $sesion->save();
                $data->sesiones_id = $sesion->id;
            }
            $data->cantidad_estudiantes = $request->cantidad_estudiantes;
            $data->path_imagen = $path_imagen;
            $data->observacion = $request->observacion;
            $data->users_id = $id;
            $data->save();

            if($request->estado==1||$request->estado==2){
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
    public function update($id, Request $request)
    {
        // dd($request);
        $rules = $request->validate([
            'modalidad' => 'required',
            'cantidad_estudiantes' => 'required',
            'horas_asistidas' => 'required',
            'fecha_tema' => 'required_if:modalidad,2',
            // 'imagen' => 'required|image',
            'tema' => 'required_if:modalidad,2',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'required_if' => '* El campo :attribute es obligatorio.',
            'imagen.image' => '* Ingrese una imagen valida'
        ]);
        // dd($request->input('fecha_tema')!=date('Y-m-d'));
        if($request->input('fecha_tema')!=date('Y-m-d')){
                
                if(!auth()->user()->hasPermissionTo('registro asistencia web')){
                    return response([
                        'errors' => [
                            'fecha_tema' => ['No estas autorizado a validar esta fecha'],
                            
                        ],
                        'message'=>'No',
                    ],422);
        
                }
                $response["message"] = 'Asistencia Editarasasdasd';
                $response["status"] = true;
        }
        
        $id_user = Auth::user()->id;
        if(isset($request->imagen)){
            $path_imagen = $this->save_file($request->file('imagen'), $request->file('imagen')->getClientOriginalExtension());
        }
        DB::beginTransaction();
        try {

            $data = AsistenciaDocente::find($id);
            $data->estado = $request->estado;
            if($request->modalidad==1){
                $data->sesiones_id = $request->sesion;
            }
            if($request->modalidad==2&&$request->estado!=3){
                $sesion = new Sesiones;
                $sesion->tema = $request->tema;
                $sesion->fecha = $request->fecha_tema;
                $sesion->carga_academicas_id = $data->carga_academicas_id;
                $sesion->save();
                $data->sesiones_id = $sesion->id;
            }
            $data->cantidad_estudiantes = $request->cantidad_estudiantes;
            $data->horas_pago = $request->horas_asistidas;
            if(isset($request->imagen)){
                $data->path_imagen = $path_imagen;
            }
            $data->observacion = $request->observacion;
            $data->users_id = $id_user;
            $data->save();


            DB::commit();
            $response["message"] = 'Asistencia Editar';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Editar, intentelo nuevamante.';
            $response["status"] =  false;
        }


        return $response;
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
