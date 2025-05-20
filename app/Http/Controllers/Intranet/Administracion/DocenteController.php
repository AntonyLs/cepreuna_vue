<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use App\Models\Docente;
use App\Models\DocenteApto;
use DB;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.administracion.docente");
    }
    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new Docente,['id','nro_documento','paterno','materno','nombres','condicion','codigo_unap','grado_academicos_id','programas_id'],['gradoAcademico','programa']);
        // $data = $data->select("inscripcion_docentes,*",
        //                         DB::raw("()")
        //                         )
        // $data = $data->where("apto","0");
        // if(isset($request->all)){
        //     $response = $data->get()->toArray();
        // }else{

        // }
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
        // return $request;

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
        $response = Docente::from('docentes as d')
        ->selectRaw('d.*')
        ->leftjoin('docente_aptos as da', 'd.id', '=', 'da.docentes_id')
        ->where('d.id', $id)
        ->first();
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
            'tipo_documento' => 'required',
            'nro_documento' => 'required',
            'email' => 'required|email',
            'celular' => 'required',
            'codigo_unap' => 'required_if:condicion,2',
            'programa' => 'required',
            'grado' => 'required',
            'cci' => 'digits:20',
            'ruc' => 'digits:11'


        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
            'codigo_unap.required_if' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $docente = Docente::find($id);
            $docente->nombres = strtoupper($request->nombres);
            $docente->paterno = strtoupper($request->paterno);
            $docente->materno = strtoupper($request->materno);
            $docente->nro_documento = $request->nro_documento;
            $docente->condicion = $request->condicion;
            $docente->email = $request->email;
            $docente->celular = $request->celular;
            $docente->ruc = $request->ruc;
            $docente->cci = $request->cci;
            $docente->codigo_unap = $request->codigo_unap;
            $docente->programas_id = $request->programa;
            $docente->grado_academicos_id = $request->grado;
            $docente->tipo_documentos_id = $request->tipo_documento;
            $docente->save();

            $docenteApto = DocenteApto::where('docentes_id', $id)->first();

            if ($docenteApto) {

                $docenteApto->idgsuite = $request->gsuite;
                $docenteApto->save();
            }


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
