<?php

namespace App\Http\Controllers\Web\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CargaAcademica;
use App\Models\Cuadernillo;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\DocenteApto;
use App\Models\Periodo;
use App\Models\Sesiones;
use App\VueTables\EloquentVueTables;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Facades\DB;

class CursosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    public function index()
    {
        // dd(Auth::user()->usuario);
        return view('web.docente.cursos');
    }
    public function getCarga()
    {
        $idDocenteApto = Auth::user()->id;
        $docenteApto = DocenteApto::find($idDocenteApto);
        // $carga = CargaAcademica::with('curso')->select('link')->where('docentes_id',$docenteApto->docentes_id)->get();

        $cargaAcademica = DB::table('carga_academicas as ca')
            ->select(
                'ca.link',
                'c.denominacion as curso',
                'g.denominacion as grupo',
                'ca.id',
                'ga.areas_id as area',
                'c.id as c',
                'ca.tipo',
                'ca.estado',
                'ga.id as grupo_aula_id',
                'a.codigo as Aula',
                'l.denominacion as Local',
                'l.direccion as DireccionLocal',
                'l.foto as Foto',
                's.denominacion as Sede'
            )
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->join('grupo_aulas as ga', 'ga.id', 'ca.grupo_aulas_id')
            ->join('aulas as a', 'a.id', 'ga.aulas_id')
            ->join('locales as l', 'l.id', 'a.locales_id')
            ->join('sedes as s', 's.id', 'l.sedes_id')
            ->join('grupos as g', 'g.id', 'ga.grupos_id')
            ->where('docentes_id', $docenteApto->docentes_id)
            ->get();

        // $response['docente'] = Docente::with('tipoDocumento','gradoAcademico','programa')->find($docenteApto->docentes_id);
        // $response['docentea'] = Auth::user()->usuario;
        return response()->json($cargaAcademica);
    }
    public function storeLink(Request $request)
    {
        $rules = $request->validate([
            'meet' => 'required',

        ], $messages = [
            'required' => '* El link de meet es obligatorio.',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $data = CargaAcademica::find($request->id);
            $data->link = $request->meet;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro Guardado';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al Guardar, intentelo nuevamante.';
            $response["status"] =  false;
        }

        // $response = array(
        //     "message" => $message,
        //     "status" => $status,
        //     "url" => $url,
        //     "error" => $error
        // );
        // }

        return response()->json($response);
        // return $response;
        // dd($request->denominacion);
    }
    public function indexTemario()
    {
        return view('web.docente.temario');
    }
    public function getCursosDocenteTemario()
    {
        $docenteApto = Auth::user();
        $periodo = Periodo::where('estado', '1')->first();
        $cargaAcademica = DB::table('carga_academicas as ca')
            ->select(
                'c.denominacion as curso',
                'c.id as id',
                'a.id as area'
            )
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->join('grupo_aulas as ga', 'ga.id', 'ca.grupo_aulas_id')
            ->join('areas as a', 'a.id', 'ga.areas_id')
            ->where('ca.docentes_id', $docenteApto->docentes_id)
            ->get();

        foreach ($cargaAcademica as $k => $val) {
            $curricula = Curricula::where('areas_id', $val->area)->first();
            $curriculaDetalle = CurriculaDetalle::where([['cursos_id', $val->id], ['curriculas_id', $curricula->id]])->first();
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->curso = $val->curso;
            $obj->temarios = DB::table('temarios')->select('path', 'id')->where([
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->get();
            $temarios[] = $obj;
        }
        $response["temarios"] = $temarios;
        return response()->json($response);
    }
    public function indexCuadernillo()
    {
        return view('web.docente.cuadernillos');
    }
    public function getCursosDocente()
    {
        $docenteApto = Auth::user();
        $periodo = Periodo::where('estado', '1')->first();
        $cargaAcademica = DB::table('carga_academicas as ca')
            ->select(
                'c.denominacion as curso',
                'c.id as id',
                'a.id as area'
            )
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->join('grupo_aulas as ga', 'ga.id', 'ca.grupo_aulas_id')
            ->join('areas as a', 'a.id', 'ga.areas_id')
            ->where('ca.docentes_id', $docenteApto->docentes_id)
            ->get();

        foreach ($cargaAcademica as $k => $val) {
            $curricula = Curricula::where('areas_id', $val->area)->first();
            $curriculaDetalle = CurriculaDetalle::where([['cursos_id', $val->id], ['curriculas_id', $curricula->id]])->first();
            $obj = new \stdClass;
            $obj->id = $val->id;
            $obj->curso = $val->curso;
            $obj->cuadernillos = DB::table('cuadernillos')->select('semana', 'path', 'id')->where([
                ['tipo', '1'],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->get();
            $obj->cuadernillosEstudiante = DB::table('cuadernillos')->select('semana', 'path', 'id')->where([
                ['tipo', '2'],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->get();
            $cuadernillos[] = $obj;
        }
        $response["cuadernillos"] = $cuadernillos;
        return response()->json($response);
    }
    public function getUrlCuadernillo(Request $request)
    {
        // $idDocenteApto = Auth::user()->id;
        $periodo = Periodo::where('estado', '1')->first();
        $curricula = Curricula::where('areas_id', $request->area)->first();

        $curriculaDetalle = CurriculaDetalle::where([['cursos_id', $request->curso], ['curriculas_id', $curricula->id]])->first();
        // return response()->json($curriculaDetalle);
        if (empty($curriculaDetalle)) {
            return "";
        } else {
            $cuadernillo = DB::table('cuadernillos')->select('path')->where([
                ['semana', $request->semana],
                ['tipo', '1'],
                ['periodos_id', $periodo->id],
                ['curricula_detalles_id', $curriculaDetalle->id]
            ])->first();
            // dd($cuadernillo);

            if (empty($cuadernillo)) {
                return "";
            } else {
                return response()->json($cuadernillo);
            }
        }
    }
    public function indexSesiones()
    {
        return view('web.docente.sesiones');
    }
    public function listaSesion(Request $request)
    {
        $docenteApto = Auth::user();
        $table = new EloquentVueTables;
        $data = $table->get(new Sesiones, [
            'sesiones.tema',
            DB::raw('DATE_FORMAT(sesiones.fecha,"%d-%m-%Y") as fecha'),
            'sesiones.carga_academicas_id',
            'c.denominacion as curso',
            'sesiones.id',
            'g.denominacion as grupo'
        ]);

        $data = $data->join('carga_academicas as ca', 'ca.id', 'sesiones.carga_academicas_id')
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->join('grupo_aulas as ga', 'ga.id', 'ca.grupo_aulas_id')
            ->join('grupos as g', 'g.id', 'ga.grupos_id');

        $data = $data->where('ca.docentes_id', $docenteApto->docentes_id);
        if (isset($request->curso)) {
            $data = $data->where('ca.id', $request->curso);
        }

        $response = $table->finish($data);
        return response()->json($response);
    }
    public function getCursosCarga()
    {
        $docenteApto = Auth::user();
        $periodo = Periodo::where('estado', '1')->first();
        $cargaAcademica = DB::table('carga_academicas as ca')
            ->select(
                DB::raw('CONCAT(c.denominacion," (",g.denominacion,")") as curso'),
                'c.id as idCurso',
                'ca.id as id',
                'ca.estado'
            )
            ->join('cursos as c', 'c.id', 'ca.cursos_id')
            ->join('grupo_aulas as ga', 'ga.id', 'ca.grupo_aulas_id')
            ->join('grupos as g', 'g.id', 'ga.grupos_id')
            ->join('areas as a', 'a.id', 'ga.areas_id')
            ->where([
                ['ca.docentes_id', $docenteApto->docentes_id],
                ['ca.estado', '1']
            ])
            ->get();

        return response()->json($cargaAcademica);
    }
    public function storeSesion(Request $request)
    {
        $rules = $request->validate([
            'fecha' => 'required',
            'tema' => 'required',
            'carga_academica' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $data = new Sesiones;
            $data->tema = $request->tema;
            $data->fecha = $request->fecha;
            $data->carga_academicas_id = $request->carga_academica;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro guardado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al guardar, intentelo nuevamante.';
            $response["status"] =  false;
            $response["error"] = $e;
        }

        return response()->json($response);
    }
    public function editSesion($id)
    {
        $sesion = Sesiones::find($id);
        return $sesion;
    }
    public function updateSesion(Request $request, $id)
    {
        $rules = $request->validate([
            'fecha' => 'required',
            'tema' => 'required',
            'carga_academica' => 'required',

        ], $messages = [
            'required' => '* El campo :attribute es obligatorio.',
        ]);
        // dd($request->all());
        DB::beginTransaction();
        try {
            $data = Sesiones::find($id);
            $data->tema = $request->tema;
            $data->fecha = $request->fecha;
            $data->carga_academicas_id = $request->carga_academica;
            $data->save();

            DB::commit();
            $response["message"] = 'Registro actualizado correctamente';
            $response["status"] = true;
        } catch (\Exception $e) {
            DB::rollback();
            $response["message"] =  'Error al actualizar registro, intentelo nuevamante.';
            $response["status"] =  false;
            $response["error"] = $e;
        }

        return response()->json($response);
    }
    public function getEstudiantes($id)
    {
        $response["estudiantes"] = DB::table('matriculas as m')
            ->select(
                DB::raw("CONCAT(e.paterno,' ',e.materno,' ',e.nombres) as nombres"),
                "e.paterno",
                "e.materno",
                "e.nombres",
                "e.nro_documento",
                "e.usuario"
            )
            ->join("estudiantes as e", "e.id", "m.estudiantes_id")
            ->where("m.grupo_aulas_id", $id)
            ->orderBy("e.paterno")
            ->orderBy("e.materno")
            ->orderBy("e.nombres")
            ->get();

        return $response;
    }
}
