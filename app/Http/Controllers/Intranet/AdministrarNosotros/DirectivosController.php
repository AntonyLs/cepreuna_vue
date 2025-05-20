<?php

namespace App\Http\Controllers\Intranet\AdministrarNosotros;

use App\Http\Controllers\Controller;
use App\User;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use App\Models\NosotrosDirectivo;

class DirectivosController extends Controller
{
    private $dateTime;
    private $dateTimePartial;
    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("m-Y");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        return view('intranet.administrarNosotros.directivos', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new NosotrosDirectivo,['id','tipo','sigla_grado_academico','nombres','paterno','materno','activo'],[]);

        $response = $table->finish($data);
        return response()->json($response);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = $request->validate([
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'sigla_grado_academico' => 'required',
            'tipo' => 'required',
            'foto' => 'required'
            // 'file' => 'required|mimes:jpg|max:9000',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
            'foto.required' => '* La fotografia es obligatoria.',
            // 'file.required' => '* La imagen en jpg es obligatorio.',
            // 'file.mimes' => '* Solo se admiten formatos jpg.',
            // 'file.max' => '* El peso maximo del archivo debe ser menor a 9 MB.'
        ]);
        // $imagen = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'directivos');
        DB::beginTransaction();
        try {
            $directivo = new NosotrosDirectivo;
            $directivo->sigla_grado_academico = $request->sigla_grado_academico;
            $directivo->nombres = $request->nombres;
            $directivo->paterno = $request->paterno;
            $directivo->materno = $request->materno;
            $directivo->tipo = $request->tipo;
            // $directivo->foto_path = $imagen;
            // env("EXTERNALURLIMAGE") . '/storage/directivos/' .
            $directivo->foto_path = $this->save_image($request->foto);
            $directivo->activo = '1';
            // $directivo->foto_foto_path = $this->save_image($request->foto);
            $directivo->save();

            DB::commit();
            $message = "Directivo creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear directivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = NosotrosDirectivo::find($id);
        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = $request->validate([
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'sigla_grado_academico' => 'required',
            'tipo' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);
        // $imagen = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'directivos');
        DB::beginTransaction();
        try {
            $directivo = NosotrosDirectivo::find($id);
            $directivo->sigla_grado_academico = $request->sigla_grado_academico;
            $directivo->nombres = $request->nombres;
            $directivo->paterno = $request->paterno;
            $directivo->materno = $request->materno;
            $directivo->tipo = $request->tipo;
            $directivo->activo = '1';
            $directivo->foto_path = $this->save_image($request->foto);
            // $directivo->foto_path = $imagen;
            $directivo->save();

            DB::commit();
            $message = "Directivo actualizado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al actualizar directivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);   
    }

    public function desactivar ($id)
    {
        DB::beginTransaction();
        try {
            $directivo = NosotrosDirectivo::find($id);
            if($directivo->activo == '1'){
                $directivo->activo = '0';
            } else {
                $directivo->activo = '1';
            }
            $directivo->save();

            DB::commit();
            $message = "Directivo desactivado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al desactivar directivo, intente nuevamente.";
            $status = false;
            $error = $e;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
    }
    // public function save_image($base64_image)
    // {

    //     $img = $this->getB64Image($base64_image);

    //     $ldate = date('Ymd_His');
    //     $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
    //     $img_name = $first . $ldate . '.png';

    //     Storage::disk('fotos')->put($img_name, $img);
    //     // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
    //     return $img_name;
    // }
    // public function getB64Image($base64_image)
    // {

    //     $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
    //     $image = base64_decode($image_service_str);

    //     return $image;
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function save_image($base64_image)
    {

        $img = $this->getB64Image($base64_image);

        $ldate = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $img_name = $first . $ldate . '.jpg';

        Storage::disk('directivos')->put($img_name, $img);
        // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
        return $img_name;
    }
    public function getB64Image($base64_image)
    {

        $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
        $image = base64_decode($image_service_str);

        return $image;
    }
    public function save_file($file, $extension, $disk)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = 'Directivo-' . $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk($disk)->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
}
