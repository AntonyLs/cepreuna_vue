<?php

namespace App\Http\Controllers\Intranet\Inscripcion;

use App\Models\Docente;
use App\Models\DocenteApto;
use Illuminate\Support\Str;
use App\Services\GWorkspace;
use Illuminate\Http\Request;
use App\Models\Disponibilidades;
use App\Models\InscripcionCurso;
use App\Models\InscripcionDocente;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\VueTables\EloquentVueTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder;

class DocenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $inscripcion = InscripcionDocente::with(['docentes'])->where("docentes.nro_documento","47520697")->first();
        // $inscripcion = InscripcionDocente::with(['docente'])
        //         ->whereHas('docente', function (Builder $query) {
        //             $query->where('nombres', 'JAVIER');
        //          })->get();
        // $inscripcion = InscripcionDocente::with(["docente" => function($q){
        //             $q->where('nombres', 'JAVIER');
        //         }])->get();
        // dd($inscripcion);
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

        return view("intranet.inscripcion.docente.index", $response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lista(Request $request)
    {
        $table = new EloquentVueTables;
        $data = $table->get(new InscripcionDocente, ['id', 'apto', 'puntaje', 'docentes_id'], ['docente']);
        // $data = $data->select("inscripcion_docentes,*",
        //                         DB::raw("()")
        //                         )
        // $data = $data->where("apto","0");
        if (isset($request->all)) {
            $response = $data->get()->toArray();
        } else {
            $response = $table->finish($data);
        }
        return response()->json($response);
    }
    public function create()
    {
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
        // dd($id);
        $response["inscripcion"] = InscripcionDocente::with("grados")->find($id);
        $response["docente"] = Docente::with("programa", "gradoAcademico")->find($response["inscripcion"]->docentes_id);
        // dd($inscripcion);
        return $response;
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

    public function calificar(Request $request, $id)
    {
        $correoDocente = new GWorkspace();
        $password = Str::random(8);

        $rules = $request->validate([
            'puntaje' => 'required',
            'apto' => 'required'

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        // dd($request);
        DB::beginTransaction();
        try {

            $data = InscripcionDocente::find($id);
            $data->puntaje = $request->puntaje;
            $data->apto = (string)$request->apto;
            $data->save();
            if ($data->apto == "1") {

                $docente = Docente::find($data->docentes_id);
                $nombres = $docente->nombres;
                $paterno = $docente->paterno;
                $materno = $docente->materno;

                $correo = "";

                $nameArray = explode(" ", $nombres);
                if (count($nameArray) >= 2) {
                    foreach ($nameArray as  $value) {
                        $correo .= substr(Str::slug($value, ""), 0, 1);
                    }
                } else {
                    $correo .= substr(Str::slug($nombres, ""), 0, 1);
                }

                $correo .= Str::slug($paterno, "");
                $query = DocenteApto::where("usuario", ($correo . "@cepreuna.edu.pe"))->first();
                // dd($query);
                if ($query != NULL) {
                    $correo .= substr(Str::slug($materno, ""), 0, 1);
                }

                $correo .= "@cepreuna.edu.pe";
                // crear correo institucional
                $datos = json_encode(array(
                    "primaryEmail" => $correo,
                    "name_givenName" => $docente->nombres,
                    "name_familyName" => $docente->paterno . ' ' . $docente->materno,
                    "orgUnitPath"  => env('ORGUNITPATHD'),
                    "password"  => $password,
                    "recoveryEmail"  => $docente->email,
                    "recoveryPhone"  => "",
                    "customSchemas_Institution_Document"  => $docente->nro_documento,
                    "customSchemas_Institution_Type"  => "Teacher"
                ));
                $correoGenerado = json_decode($correoDocente->correo($datos));

                $apto = new DocenteApto;
                $apto->usuario = $correo;
                $apto->password = $password;
                $apto->idgsuite = $correoGenerado->message->id;
                // $apto->idgsuite = '123456789';
                $apto->docentes_id = $data->docentes_id;
                $apto->periodos_id = $data->periodos_id;
                $apto->save();

                $nombreDocente = $docente->nombres . ' ' . $docente->paterno . ' ' . $docente->materno;

                $this->sendEmail($nombreDocente, $docente->email, $apto->usuario, $apto->password);
            }

            DB::commit();
            $response["message"] = 'Docente calificado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Calificar, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function cursos(Request $request, $id)
    {
        // return($request);
        DB::beginTransaction();
        try {
            InscripcionCurso::where("inscripcion_docentes_id", $id)->delete();
            foreach ($request->cursos as $value) {
                $cursos = new InscripcionCurso;
                $cursos->inscripcion_docentes_id = $id;
                $cursos->curricula_detalles_id = $value;
                $cursos->save();
            }


            DB::commit();
            $response["message"] = 'Cursos actulizados correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actulizar cursos, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function disponibilidad(Request $request, $id)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            Disponibilidades::where("inscripcion_docentes_id", $id)->delete();
            foreach ($request->disponibilidad as $value) {
                $ids = explode("-", $value);

                $disponibilidad = new Disponibilidades;
                $disponibilidad->prioridad = "1";
                $disponibilidad->dia = $ids[0];
                $disponibilidad->plantilla_horarios_id = $ids[1];
                $disponibilidad->sedes_id = $ids[2];
                $disponibilidad->inscripcion_docentes_id = $id;
                $disponibilidad->save();
            }


            DB::commit();
            $response["message"] = 'Disponibilidad actulizado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actulizar disponibilidad, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function enviarAccesos($id){
        // dd($id);
        DB::beginTransaction();
        try {

            $docenteApto = DocenteApto::where("docentes_id",$id)->first();
            $docenteApto->enviar_acceso = "1";
            $docenteApto->save();

            DB::commit();
            $response["message"] = ' actulizado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actulizar, intentelo nuevamante.';
            $response["status"] =  false;
        }

        return $response;
    }
    public function sendEmail($name, $email, $usuario, $password)
    {
        $to_name = 'Correo Institucional Docente';
        $to_email = $email;

        $datos = array('nombres' => $name, 'usuario' => $usuario, 'password' => $password);

        Mail::send('intranet.emails.cuenta-institucional', $datos, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject($to_name);
            $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        });
    }
}
