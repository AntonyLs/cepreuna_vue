<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Inscripciones;
use App\Services\GWorkspace;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('intranet.administracion.estudiante');
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Estudiante, [
            'estudiantes.id',
            'estudiantes.nombres',
            'estudiantes.paterno',
            'estudiantes.materno',
            'estudiantes.nro_documento',
            'estudiantes.celular',
            'estudiantes.estado',
            'u.departamento',
            'u.provincia',
            'u.distrito',
            'c.denominacion as colegio'
        ]);

        $data = $data->leftJoin('ubigeos as u', 'u.id', 'estudiantes.ubigeos_id')
            ->join('colegios as c', 'c.id', 'estudiantes.colegios_id');
        $data = $data->orderBy('id', 'desc');
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
        //
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
        $estudiante = Estudiante::select('paterno', 'materno', 'nombres', 'foto', 'id')->where('nro_documento', $id)->first();
        return response()->json($estudiante);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        // $estudiante = Estudiante::with(['colegio', 'pais', 'ubigeo', 'tipoDocumento'], 'id')->find($id);
        $estudiante = Estudiante::with('colegio', 'pais', 'ubigeo', 'tipoDocumento', 'ubigeo_nacimiento')->find($id);
        // dd($estudiante);
        return response()->json($estudiante);
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
        // dd($request->all());
        $rules = $request->validate([
            'nombres' => 'required',
            'paterno' => 'required',
            'materno' => 'required',
            'tipo_documento' => 'required',
            'nro_documento' => 'required',
            'fecha_nac' => 'required',
            'email' => 'required|email',
            'celular' => 'required',
            'colegio' => 'required',
            'egreso' => 'required|integer|min:1900',
            'pais' => 'required',
            // 'departamento' => 'required_if:pais,1',
            // 'provincia' => 'required_if:pais,1',
            'ubigeo' => 'required_if:pais,1',
            'usuario' => 'required',
            'password' => 'required'
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'ubigeo.required_if' => '* El campo distrito es obligatorio.',
            'email.email' => '* El formato del correo no es el correcto',
            'egreso.min' => '* El año ingresado no es valido',
            'fecha_nac.required' => '* La fecha de nacimiento es obligatorio'
        ]);

        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $estudiante->nombres = mb_strtoupper($request->nombres);
            $estudiante->paterno = mb_strtoupper($request->paterno);
            $estudiante->materno = mb_strtoupper($request->materno);
            $estudiante->nro_documento = $request->nro_documento;
            $estudiante->fecha_nac = $request->fecha_nac;
            $estudiante->celular = $request->celular;
            $estudiante->email = $request->email;
            $estudiante->sexo = $request->sexo;
            $estudiante->anio_egreso = $request->egreso;
            $estudiante->foto = $request->foto;
            $estudiante->usuario = $request->usuario;
            // $estudiante->estado = $request->estado;
            $estudiante->pais_id = $request->pais;
            $estudiante->ubigeos_id = $request->ubigeo;
            $estudiante->ubigeos_nacimiento = $request->ubigeon;
            $estudiante->tipo_documentos_id = $request->tipo_documento;
            $estudiante->colegios_id = $request->colegio;
            $estudiante->save();

            DB::commit();
            $response["message"] = 'Estudiante actualizado correctamente.';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actualizar, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return $response;
    }

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

    public function indexFoto()
    {
        return view('intranet.administracion.actualizar-foto');
    }
    public function updateImage(Request $request)
    {
        $rules = $request->validate([
            'fotografia' => 'required',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($request->id);

            Storage::disk('fotos')->delete($estudiante->foto);

            $estudiante->foto = $this->save_image($request->fotografia);
            $estudiante->save();


            DB::commit();
            $response["message"] = 'Fotografia actualizada correctamente.';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actualizar, intentelo nuevamante.';
            $response["status"] =  false;
        }
        return response()->json($response);
    }
    public function save_image($base64_image)
    {

        $img = $this->getB64Image($base64_image);

        $ldate = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $img_name = $first . $ldate . '.png';

        Storage::disk('fotos')->put($img_name, $img);
        // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
        return $img_name;
    }
    public function getB64Image($base64_image)
    {

        $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
        $image = base64_decode($image_service_str);

        return $image;
    }

    public function activarGsuite($id)
    {
        $correo = new GWorkspace();
        $estudiante = Estudiante::find($id);
        $datos = json_encode(array(
            "primaryEmail" => $estudiante->nro_documento . "@cepreuna.edu.pe",
            "name_givenName" => strtoupper($estudiante->nombres),
            "name_familyName" => strtoupper($estudiante->paterno . ' ' . $estudiante->materno),
            "orgUnitPath"  => env('ORGUNITPATHE'),
            "password"  => $estudiante->password,
            "recoveryEmail"  => $estudiante->email,
            "recoveryPhone"  => "",
            "customSchemas_Institution_Document"  => $estudiante->nro_documento,
            "customSchemas_Institution_Type"  => "Student"
        ));
        $correoGenerado = json_decode($correo->correo($datos));
        if (isset($correoGenerado->message->id)) {
            $estudiante->idgsuite = $correoGenerado->message->id;
            $estudiante->save();

            $response["message"] = 'GSuite activado correctamente.';
            $response["status"] = true;
        } else {
            $response["message"] =  'Error al activar GSuite.';
            $response["status"] =  false;
        }
        return response()->json($response);
    }
}
