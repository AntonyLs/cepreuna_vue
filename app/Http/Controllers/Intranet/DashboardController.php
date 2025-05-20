<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Area;
use App\Models\Inscripciones;
use App\Models\Matricula;
use Illuminate\Support\Facades\Crypt;
use DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('intranet.dashboard');
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
        //
    }
    public function getCantidad(Request $request)
    {
        $cantidad = Inscripciones::where([['turnos_id', $request->turno], ['areas_id', $request->area]])->count();
        return $cantidad;
    }
    public function getCantidadMatriculados(Request $request)
    {
        // $cantidad = Matricula::where([['turnos_id',$request->turno],['areas_id',$request->area]])->count();
        $cantidad = DB::table('matriculas as m')
            ->select(
                "g.denominacion",
                DB::raw("COUNT(g.id) as total")
            )
            ->join("grupo_aulas as ga", "ga.id", "m.grupo_aulas_id")
            ->join("grupos as g", "g.id", "ga.grupos_id")
            ->where([['m.turnos_id', $request->turno], ['ga.areas_id', $request->area]])
            ->groupBy("g.id")
            ->get();
        return $cantidad;
    }
    public function encrypt($id)
    {
        return Crypt::encryptString($id);
        // return view("intranet.dashboard");
    }
    public function script()
    {
        // return 1;
        DB::beginTransaction();
        try {
            $query = Inscripciones::all();
            foreach ($query as $key => $value) {
                $data = Inscripciones::find($value->id);
                $data->correlativo = str_pad($value->id, 5, "0", STR_PAD_LEFT);
                $data->save();
            }

            DB::commit();
            // $response["message"] = 'Guardado';
            // $response["status"] = true;

        } catch (\Exception $e) {
            DB::rollback();
            // $response["message"] =  'Error al Guardar, intentelo nuevamante.';
            // $response["status"] =  false;
        }
        // dd($query);
    }
}
