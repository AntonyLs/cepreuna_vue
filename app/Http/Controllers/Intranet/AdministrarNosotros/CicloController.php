<?php

namespace App\Http\Controllers\Intranet\AdministrarNosotros;

use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class CicloController extends Controller
{
    private $dateTime;
    private $dateTimePartial;
    public function __construct()
    {
        date_default_timezone_set("America/Lima"); //Zona horaria de Peru
        $this->dateTime = date("Y-m-d H:i:s");
        $this->dateTimePartial = date("Y");
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
        return view('intranet.administrarNosotros.ciclos', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new Ciclo(), ['*'], []);
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
        $rules = $request->validate([
            'inicio_ciclo' => 'required',
            'fin_ciclo' => 'required',
            'inicio_inscripciones' => 'required',
            'fin_inscripciones' => 'required',
            'inicio_clases' => 'required',
            'estado' => 'required',
            'file' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $imagen = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'ciclos');

            $ciclo = new Ciclo();
            $ciclo->inicio_ciclo = $request->inicio_ciclo;
            $ciclo->fin_ciclo = $request->fin_ciclo;
            $ciclo->inicio_inscripciones = $request->inicio_inscripciones;
            $ciclo->fin_inscripciones = $request->fin_inscripciones;
            $ciclo->inicio_clases = $request->inicio_clases;
            $ciclo->estado = $request->estado;
            $ciclo->path = $imagen;
            $ciclo->save();

            DB::commit();
            $message = "Item creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear item, intente nuevamente.";
            $status = false;
            $error = $e->getMessage();
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
        $response['ciclos'] = Ciclo::find($id);
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
            'inicio_ciclo' => 'required',
            'fin_ciclo' => 'required',
            'inicio_inscripciones' => 'required',
            'fin_inscripciones' => 'required',
            'inicio_clases' => 'required',
            'estado' => 'required',
            'file' => 'required',
        ], $messages = [
            'required' => '*El campo :attribute es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            $imagen = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'ciclos');

            $ciclo = Ciclo::find($id);
            $ciclo->inicio_ciclo = $request->inicio_ciclo;
            $ciclo->fin_ciclo = $request->fin_ciclo;
            $ciclo->inicio_inscripciones = $request->inicio_inscripciones;
            $ciclo->fin_inscripciones = $request->fin_inscripciones;
            $ciclo->inicio_clases = $request->inicio_clases;
            $ciclo->estado = $request->estado;
            $ciclo->path = $imagen;
            $ciclo->save();

            DB::commit();
            $message = "Item creado correctamente.";
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = "Error al crear item, intente nuevamente.";
            $status = false;
            $error = $e->getMessage();
        }
        $response = array(
            "message" => $message,
            "status" => $status,
            "error" => isset($error) ? $error : ''
        );

        return response()->json($response);
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
    public function save_file($file, $extension, $disk)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = 'Ciclo-' . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk($disk)->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
}
