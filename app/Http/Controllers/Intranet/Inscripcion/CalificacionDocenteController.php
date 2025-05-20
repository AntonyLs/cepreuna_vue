<?php

namespace App\Http\Controllers\Intranet\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\InscripcionDocente;
use App\Models\InscripcionDocenteDetalle;
use App\VueTables\EloquentVueTables;

class CalificacionDocenteController extends Controller
{
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

        return view("intranet.inscripcion.calificacionDocente.index", $response);
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new InscripcionDocente, ['inscripcion_docentes.id', 'apto', 'puntaje', 'docentes_id','areas_id'], ['docente','area','disponibilidades','grados','experiencias']);
        $data = $data->select(
            "inscripcion_docentes.*",
            DB::raw("(SELECT GROUP_CONCAT(c.denominacion SEPARATOR  ' | ')
                        FROM inscripcion_cursos AS ic
                        JOIN curricula_detalles as cd ON cd.id=ic.curricula_detalles_id
                        JOIN cursos as c ON c.id=cd.cursos_id
                        WHERE
                            ic.inscripcion_docentes_id = inscripcion_docentes.id

                            GROUP BY ic.inscripcion_docentes_id
                        ) AS cursos")
                        // ,
                        // DB::raw("(SELECT GROUP_CONCAT(c.denominacion SEPARATOR  ' | ')
                        // FROM inscripcion_cursos AS ic
                        // JOIN curricula_detalles as cd ON cd.id=ic.curricula_detalles_id
                        // JOIN cursos as c ON c.id=cd.cursos_id
                        // WHERE
                        //     ic.inscripcion_docentes_id = inscripcion_docentes.id

                        //     GROUP BY ic.inscripcion_docentes_id
                        // ) AS cursos")
        );
        // $data = $data->join("inscripcion_docentes as ins", "ins.id", "inscripcion_docentes.id");
        $data = $data->join("docentes", "docentes.id", "inscripcion_docentes.docentes_id");
        $data = $data->orderBy("docentes.paterno", "asc")->orderBy("docentes.materno", "asc")->orderBy("docentes.nombres", "asc");
        // $data = $data->select("inscripcion_docentes,*",
        //                         DB::raw("()")
        //                         )
        // $data = $data->where("apto","0");
        if (isset($request->area)) {
            $data = $data->where('inscripcion_docentes.areas_id', $request->area);
        }
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
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
        DB::beginTransaction();
        try {
            $puntaje = 0;
            $apto = 0;
            foreach ($request->puntaje as $key => $value) {
                // echo $value."-".$key."<br>";
                if($value!=NULL){
                    $puntaje+=$value;
                    $data = new InscripcionDocenteDetalle;
                    $data->puntaje = $value;
                    $data->criterios_id = $key;
                    $data->inscripcion_docentes_id = $request->id;
                    $data->save();
                }
            }
            if($puntaje>=15){
                $apto = '1';
            }
            $inscripcion = InscripcionDocente::find($request->id);
            $inscripcion->puntaje = $puntaje;
            $inscripcion->apto = $apto;
            $inscripcion->save();

            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
            $response["status"] =  false;
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // dd($request);
        DB::beginTransaction();
        try {
            InscripcionDocenteDetalle::where("inscripcion_docentes_id",$id)->delete();
            $puntaje = 0;
            $apto = '0';
            foreach ($request->puntaje as $key => $value) {
                // echo $value."-".$key."<br>";
                if($value!=NULL){
                    $puntaje+=$value;
                    $data = new InscripcionDocenteDetalle;
                    $data->puntaje = $value;
                    $data->criterios_id = $key;
                    $data->inscripcion_docentes_id = $request->id;
                    $data->save();
                }
            }
            if($puntaje>=15){
                $apto = '1';
            }
            $inscripcion = InscripcionDocente::find($request->id);
            $inscripcion->puntaje = $puntaje;
            $inscripcion->apto = $apto;
            $inscripcion->save();
            DB::commit();
            $response["message"] = 'Guardado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
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
}
