<?php

namespace App\Http\Controllers\Intranet\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VueTables\EloquentVueTables;
use App\Models\Locales;
use DB;

class LocalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("intranet.configuracion.local");
    }

    public function lista(Request $request){
        $table = new EloquentVueTables;
        $data = $table->get(new Locales,['id','denominacion','direccion','nro_aulas','sedes_id'],['sede']);
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
        //return $request;
        $rules = $request->validate([
            'denominacion' => 'required',
            'direccion' => 'required',
            'nro_aulas' => 'required',
            'sede' => 'required',
            'image' => 'required'
           
        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

            $data = new Locales;
            $data->denominacion = $request->denominacion;
            $data->direccion = $request->direccion;
            $data->nro_aulas = $request->nro_aulas;
            $data->sedes_id = $request->sede;
            $data->foto = $request->image;
            $data->save();



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
        $response = Locales::find($id);
        // $response["provincia"]
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
        //dd($request->all());

        $rules = $request->validate([
            'denominacion' => 'required',
            'direccion' => 'required',
            'nro_aulas' => 'required',
            'sede' => 'required',
            'image' => 'required'
        ],$messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        DB::beginTransaction();
        try {

       
            $data = Locales::find($id);
            $data->denominacion = $request->denominacion;
            $data->direccion = $request->direccion;
            $data->nro_aulas = $request->nro_aulas;
            $data->sedes_id = $request->sede;
            $data->foto = $request->image;
            $data->save();



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
    public function upload(Request $request)
    {
    //    return $request;
        if ($request->hasFile('image')){
            $rules = $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg|max:2048'
            ],$messages = [
                'required' => '* El campo :attribute es obligatorio.',
                'image' => 'El archivo debe ser una imagen válida en formato jpeg, png, jpg.',
                'mimes' => 'El archivo debe ser de tipo jpeg, png, jpg.',
                'max' => 'El tamaño máximo del archivo es 2 MB.',
            ]);

            $image = $request->file('image');
            if ($image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/locales'), $imageName);
                return $imageName;
            }
        }
        else{
            return "virtual.jpg";
        }
    }
    public function destroy($id)
    {
        //
    }
}
