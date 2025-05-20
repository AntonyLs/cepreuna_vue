<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CargaAcademica;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\Grupo;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use App\Models\GrupoAula;
use App\Models\Periodo;
use App\Services\GWorkspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class GrupoAulaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.grupo-aula");
    }
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new GrupoAula, ['id', 'grupos_id', 'aulas_id', 'turnos_id', 'areas_id'], ['grupo', 'aula', 'turno', 'area']);

        // $data = $table->get(new Inscripciones,['id','correlativo','estado','estudiantes_id','areas_id','sedes_id','periodos_id','turnos_id'],['estudiante','area','sede','periodo','turno']);
        // $data = $data->orderBy('id','desc');
        if (isset($request->area)) {
            $data = $data->whereHas('area', function (Builder $query) use ($request) {
                $query->where('id', $request->area);
            });
        }
        if (isset($request->turno)) {
            $data = $data->whereHas('turno', function (Builder $query) use ($request) {
                $query->where('id', $request->turno);
            });
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
        $apiGsuite = new GWorkspace();
        $rules = $request->validate([
            'aula' => 'required',
            'grupo' => 'required',
            'turno' => 'required',
            'area' => 'required'
        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);

        $periodo = Periodo::where('estado', '1')->first();
        $curricula = Curricula::where([['areas_id', $request->area], ['estado', '1']])->first();
        $detalleCurriculas = CurriculaDetalle::with('curso')->where('curriculas_id', $curricula->id)->get();
        $grupo = Grupo::find($request->grupo);
        $area = Area::find($request->area);
        // dd($detalleCurriculas);
        DB::beginTransaction();
        try {
            $grupoAula = new GrupoAula;
            $grupoAula->grupos_id = $request->grupo;
            $grupoAula->aulas_id = $request->aula;
            $grupoAula->turnos_id = $request->turno;
            $grupoAula->areas_id = $request->area;
            $grupoAula->periodos_id = $periodo->id;
            $grupoAula->save();

            foreach ($detalleCurriculas as $value) {
                // $datos = json_encode(array(
                //     "name" => $value->curso->denominacion,
                //     "section" => $grupo->denominacion,
                //     "room" => $area->denominacion.' ['.$grupo->denominacion.']',
                // ));
                // $cursoCreado = json_decode($apiGsuite->crearCurso($datos));

                $carga = new CargaAcademica;
                // $carga->idclassroom =  $cursoCreado->message->id;;
                $carga->tipo = '1';
                $carga->cursos_id = $value->cursos_id;
                $carga->grupo_aulas_id = $grupoAula->id;
                $carga->periodos_id = $periodo->id;
                $carga->save();
            }

            DB::commit();
            $message = 'Grupo Habilitado correctamente.';
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al habilitar grupo';
            $status = false;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        DB::beginTransaction();
        try {
            CargaAcademica::where("grupo_aulas_id",$id)->delete();
            $grupoAula = GrupoAula::find($id);
            $grupoAula->delete();



            DB::commit();
            $message = 'Grupo Eliminado correctamente.';
            $status = true;
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Error al eliminar grupo';
            $status = false;
        }
        $response = array(
            "message" => $message,
            "status" => $status,
        );

        return response()->json($response);
    }
}
