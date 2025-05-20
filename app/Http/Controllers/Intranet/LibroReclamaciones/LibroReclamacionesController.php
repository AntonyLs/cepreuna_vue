<?php

namespace App\Http\Controllers\Intranet\LibroReclamaciones;

use App\Http\Controllers\Controller;
use App\Models\ReclamoLR;
use App\VueTables\EloquentVueTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;

class LibroReclamacionesController extends Controller
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
        $response['external_url'] = json_encode(env("EXTERNALURL"));
        return view("intranet.libroReclamaciones.libroReclamaciones", $response);
    }

    public function getDatosReclamo($id)
    {
        $response = DB::connection('mysql')->table('reclamos')
            ->select("*")
            // ->select("id","paterno","materno","nombres","estado","descripcion","detalle","pedido","fecha","path","respuesta")
            ->where([["id",$id]])
            ->get();
        
            return response()->json($response);
    }

    public function getInfoReclamo($id)
    {
        $response = DB::connection('mysql')->table('reclamos as r')
            ->join("users as u", "u.id", "r.user_id")
            ->select("r.*","u.name as user_name","u.paterno as user_paterno","u.materno as user_materno")
            ->where([["r.id",$id]])
            ->get();
            // dd($response);
        
            return response()->json($response);
    }


    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new ReclamoLR, ['id','dni','paterno','materno','nombres','descripcion','fecha_ingreso','estado'],[]);
        // dd($data);
        if (isset($request->estado)) {
            $data = $data->where("reclamos.estado", $request->estado);
        }

        $response = $table->finish($data);
        return response()->json($response);
    }

    public function responderReclamo(Request $request)
    {
        DB::beginTransaction();
        try {
            $reclamo = ReclamoLR::find($request->id);
            $reclamo->respuesta = $request->texto;
            $reclamo->estado = '1';
            $reclamo->fecha_respuesta = $request->fecha_respuesta;
            $reclamo->user_id = Auth::user()->id;
            $reclamo->save();

            DB::commit();
            $response["status"] = true;
            $response["message"] = "Formulario enviado correctamente";
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
            $response["message"] = "Error al enviar formulario";
            $response["e"] = $e->getMessage();
        }

        return $response;
    }

    // public function showEvidencia($id)
    // {
    //     $reclamo = DB::connection('mysql')->table('reclamos')->where('id', $id)->first();
    //     // dd($reclamo->path);
    //     if(!empty($reclamo->path)){
    //         $name = explode('.', explode('/', $reclamo->path)[2])[0];

    //         $files = Storage::disk('evidenciasReclamos')->download($reclamo->path, $name, [
    //             'Content-Disposition' => 'inline'
    //         ]);
    //         return $files;
    //     }
    // }
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
}
