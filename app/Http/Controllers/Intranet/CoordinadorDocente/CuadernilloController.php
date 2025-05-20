<?php

namespace App\Http\Controllers\Intranet\CoordinadorDocente;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Cuadernillo;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\Curso;
use App\Models\Periodo;
use App\Models\Sede;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

class CuadernilloController extends Controller
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
        return view('intranet.coordinadorDocente.cuadernillo', $response);
    }

    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(
            new Cuadernillo,
            [
                'cuadernillos.id',
                'cuadernillos.semana',
                'cuadernillos.tipo',
                'cuadernillos.periodos_id',
                'cuadernillos.curricula_detalles_id',
                'a.denominacion as area',
                'cu.denominacion as curso'
            ],
            ['periodo']
        );

        $user = Auth::user();
        // dd($user->hasAnyRole(['Super Admin', 'Administrador']));
        // if ($user->hasAnyRole(['Super Admin', 'Administrador'])) {
        $data = $data->join('curricula_detalles as cd', 'cd.id', 'cuadernillos.curricula_detalles_id')
            ->join('curriculas as c', 'c.id', 'cd.curriculas_id')
            ->join('areas as a', 'a.id', 'c.areas_id')
            ->join('cursos as cu', 'cu.id', 'cd.cursos_id');
        // } else {
        //     $data = $data->join('curricula_detalles as cd', 'cd.id', 'cuadernillos.curricula_detalles_id')
        //         ->join('curriculas as c', 'c.id', 'cd.curriculas_id')
        //         ->join('areas as a', 'a.id', 'c.areas_id')
        //         ->join('cursos as cu', 'cu.id', 'cd.cursos_id')
        //         ->where('users_id', Auth::user()->id);
        // }

        if ($request->semana) {
            $data = $data->where('cuadernillos.semana', $request->semana);
        }

        if (isset($request->area)) {
            $data = $data->where('a.id', $request->area);
        }
        if (isset($request->curso)) {
            $data = $data->where('cd.id', $request->curso);
        }

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
            'area' => 'required',
            'semana' => 'required',
            'curricula_detalle' => 'required',
            'file' => 'required|mimes:pdf|max:9000',
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'curricula_detalle.required' => '* El campo curso es obligatorio.',
            'file.required' => '* El cuadernillo en pdf es obligatorio.',
            'file.mimes' => '* Solo se admiten formatos pdf.',
            'file.max' => '* El peso maximo del archivo debe ser menor a 9 MB.'
        ]);
        $periodo = Periodo::where('estado', '1')->first();

        $validarCuadernillo = Cuadernillo::where(
            [
                ['semana', $request->semana],
                ['tipo', $request->tipo],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $request->curricula_detalle]
            ]
        )->first();

        if (empty($validarCuadernillo)) {
            $pdf = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'documentos');
            DB::beginTransaction();
            try {

                $cuadernillo = new Cuadernillo;
                $cuadernillo->semana = $request->semana;
                $cuadernillo->tipo = $request->tipo;
                $cuadernillo->path = $pdf;
                $cuadernillo->curricula_detalles_id = $request->curricula_detalle;
                $cuadernillo->periodos_id = $periodo->id;
                $cuadernillo->users_id = Auth::user()->id;
                $cuadernillo->save();

                DB::commit();
                $response["message"] = 'Cuadernillo guardado correctamente';
                $response["status"] = true;
            } catch (\Exception $e) {
                DB::rollback();
                $response["message"] =  'Error al guardar cuadernillo, intentelo nuevamante.';
                $response["status"] =  false;
            }
        } else {

            $response["message"] = 'El cuadernillo que intenta registrar ya existe';
            $response["status"] = false;
        }
        return $response;
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
        $cuadernillo = Cuadernillo::find($id);

        return $cuadernillo;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response['cuadernillo'] = Cuadernillo::find($id);
        $curricula_detalle = CurriculaDetalle::find($response['cuadernillo']->curricula_detalles_id);
        $curricula = Curricula::find($curricula_detalle->curriculas_id);
        // dd($curricula_detalle->curriculas_id);
        $response['cursos'] = DB::table('curricula_detalles as cd')
            ->select('cd.id', 'c.denominacion')
            ->join('cursos as c', 'c.id', 'cd.cursos_id')
            ->where('cd.curriculas_id', $curricula_detalle->curriculas_id)
            ->get();

        $response['area'] =  Area::find($curricula->areas_id);

        return $response;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCuadernillo(Request $request, $id)
    {
        $rules = $request->validate([
            // 'area' => 'required',
            // 'semana' => 'required',
            // 'curricula_detalle' => 'required',
            'file' => 'required|max:5000',
        ], $messages = [
            // 'required' => '* El campo :attribute es obligatorio.',
            // 'curricula_detalle.required' => '* El campo curso es obligatorio.',
            'file.required' => '* El cuadernillo en pdf es obligatorio.',
            'file.mimes' => '* Solo se admiten formatos pdf.',
            'file.max' => '* El peso maximo del archivo debe ser menor a 5 MB.'
        ]);
        // dd($request->all());
        $pdf = $this->save_file($request->file, $request->file('file')->getClientOriginalExtension(), 'documentos');
        DB::beginTransaction();
        try {

            $cuadernillo = Cuadernillo::find($id);
            // $cuadernillo->semana = $request->semana;
            // $cuadernillo->tipo = $request->tipo;
            $cuadernillo->path = $pdf;
            // $cuadernillo->curricula_detalles_id = $request->curricula_detalle;
            $cuadernillo->save();

            DB::commit();
            $response["message"] = 'Cuadernillo editado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al editar cuadernillo, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function update(Request $request, $id)
    {
        $rules = $request->validate([
            // 'area' => 'required',
            // 'semana' => 'required',
            // 'curricula_detalle' => 'required',
            'file' => 'required|max:5000',
        ], $messages = [
            // 'required' => '* El campo :attribute es obligatorio.',
            // 'curricula_detalle.required' => '* El campo curso es obligatorio.',
            'file_edit.required' => '* El cuadernillo en pdf es obligatorio.',
            'file_edit.mimes' => '* Solo se admiten formatos pdf.',
            'file_edit.max' => '* El peso maximo del archivo debe ser menor a 5 MB.'
        ]);

        $pdf = $this->save_file($request->file, $request->file('file_edit')->getClientOriginalExtension(), 'documentos');
        DB::beginTransaction();
        try {

            $cuadernillo = Cuadernillo::find($id);
            // $cuadernillo->semana = $request->semana;
            // $cuadernillo->tipo = $request->tipo;
            $cuadernillo->path = $pdf;
            // $cuadernillo->curricula_detalles_id = $request->curricula_detalle;
            $cuadernillo->save();

            DB::commit();
            $response["message"] = 'Cuadernillo editado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al editar cuadernillo, intentelo nuevamante.';
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
    public function save_file($file, $extension, $disk)
    {
        $date = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $file_name = 'Cuadernillo-' . $date . $first . '.' . $extension;
        $name_complete = $this->dateTimePartial . '/' . $file_name;
        Storage::disk($disk)->putFileAs($this->dateTimePartial, $file, $file_name);
        return $name_complete;
    }
}
