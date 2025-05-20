<?php

namespace App\Http\Controllers\Web\Inscripcion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pais;
use App\Models\TipoDocumento;
use App\Models\Ubigeo;
use App\Models\Colegio;
use App\Models\Sede;
use App\Models\Area;
use App\Models\Parentesco;
use App\Models\Turno;
use App\Models\PlantillaHorario;
use App\Models\Curso;
use App\Models\Programa;
use App\Models\GradoAcademico;
use App\Models\ConfiguracionVacante;
use App\Models\Inscripciones;
use App\Models\Curricula;
use App\Models\CurriculaDetalle;
use App\Models\Experiencia;
use App\Models\CapacitacionTipo;
use App\Models\Escuela;
use App\Models\Producciones;
use App\Models\HorarioInscripciones;
use DB;
use Illuminate\Support\Facades\Config;
use SebastianBergmann\Environment\Console;

class IndexController extends Controller
{
    public function index()
    {
        return view('web.inscripcion.index');
    }

    public function getPaises()
    {
        $paises = Pais::get();

        return response()->json($paises);
    }
    public function getDepartamentos()
    {
        $departamentos = Ubigeo::select('codigo_departamento', 'departamento')->distinct()->get();

        return response()->json($departamentos);
    }
    public function getProvincias(Request $request)
    {
        $provincias = Ubigeo::select('codigo_provincia', 'provincia')->distinct()->where('codigo_departamento', $request->codigo)->get();

        return response()->json($provincias);
    }
    public function getDistritos(Request $request)
    {
        $distritos = Ubigeo::select('id', 'distrito')->distinct()->where('codigo_provincia', $request->codigo)->get();

        return response()->json($distritos);
    }
    public function getColegios(Request $request)
    {
        // dd($request->q);
        $colegios = Colegio::select(
            DB::raw('CONCAT(denominacion," ",distrito) as denominacion'),
            'id',
            'tipo_colegios_id'
        )
            ->where(DB::raw('CONCAT(denominacion," ",distrito)'), 'like', "%$request->q%")->get();

        return response()->json($colegios);
    }
    public function getSedes()
    {
        $sedes = Sede::where("estado", '1')->get();

        return response()->json($sedes);
    }
    public function getSedesModalidad(Request $request)
    {
        switch ($request->modalidad) {
            case '1':
                $sedes = Sede::where("estado", '1')->where("modalidad", '1')->get();
                break;
            case '2':
                $sedes = Sede::where("estado", '1')->where("modalidad", '2')->get();
                break;
            case '3':
                $sedes = Sede::where("estado", '1')->get();
                break;
            default:
                # code...
                break;
        }

        return response()->json($sedes);
    }
    public function getAreas()
    {
        $areas = Area::get();

        return response()->json($areas);
    }
    public function getVacanteAreas(Request $request)
    {
        $areas = DB::table('configuracion_vacantes as cv')
            ->select(
                'a.id',
                'a.denominacion'
            )->join('areas as a', 'a.id', 'cv.areas_id')
            ->where([
                ['cv.sedes_id', $request->sede],
                ['cv.estado', '1']
            ])
            ->groupBy('a.id')
            ->get();

        return response()->json($areas);
    }
    public function getVacanteTurnos(Request $request)
    {
        $areas = DB::table('configuracion_vacantes as cv')
            ->select(
                't.id',
                't.denominacion'
            )->join('turnos as t', 't.id', 'cv.turnos_id')
            ->where([
                ['cv.areas_id', $request->area],
                ['cv.estado', '1']
            ])
            ->groupBy("t.id")
            ->get();

        return response()->json($areas);
    }
    public function getVacanteModalidad(Request $request)
    {
        $sedes = DB::table('configuracion_vacantes as cv')
            ->select(
                's.id',
                's.denominacion'
            )->join('sedes as s', 's.id', 'cv.sedes_id')
            ->where([
                ['cv.areas_id', $request->area],
                ['cv.turnos_id', $request->turno],
                ['cv.estado', '1']
            ])
            ->groupBy("s.id")
            ->get();

        return response()->json($sedes);
    }
    public function getVacanteSede(Request $request)
    {
        $sedes = DB::table('configuracion_vacantes as cv')
            ->select(
                's.id',
                's.denominacion'
            )->join('sedes as s', 's.id', 'cv.sedes_id')
            ->where([
                ['cv.areas_id', $request->area],
                ['cv.turnos_id', $request->turno],
                ['s.modalidad', $request->modalidad],
                ['cv.estado', '1']
            ])
            ->groupBy("s.id")
            ->get();

        return response()->json($sedes);
    }
    public function getEstadoVacantes(Request $request)
    {
        $cantidadInscritos = Inscripciones::where([['turnos_id', $request->turno], ['areas_id', $request->area], ['sedes_id', $request->sede]])->count();
        $cantidadVacantes = ConfiguracionVacante::where([['turnos_id', $request->turno], ['areas_id', $request->area], ['sedes_id', $request->sede]])->first();
        // dd($cantidadVacantes->cantidad);
        if ($cantidadInscritos >= $cantidadVacantes->cantidad) {
            $status = false;
            $cantidadVacantes->estado = '0';
            $cantidadVacantes->save();
        } else {
            $status = true;
        }
        return response()->json($status);
    }
    public function getTurnos()
    {
        $turnos = Turno::where('estado', '1')->get();

        return response()->json($turnos);
    }
    public function getEscuelas()
    {
        $turnos = Escuela::get();

        return response()->json($turnos);
    }
    public function getParentescos()
    {
        $parentescos = Parentesco::get();

        return response()->json($parentescos);
    }
    public function getTipoDocumentos()
    {
        $tipoDocumentos = TipoDocumento::select('id', 'denominacion')->get();

        return response()->json($tipoDocumentos);
    }
    public function getPlantillaHorario(Request $request)
    {
        $turno = Turno::get();
        $horario = [];
        foreach ($turno as $key => $value) {
            $obj = new \stdClass;
            $obj->id = $value->id;
            $obj->turno = $value->denominacion;
            // $obj->horario = plantillaHorario::where("turnos_id",$value->id)->get();
            $plantilla = plantillaHorario::where("turnos_id", $value->id)->get();
            foreach ($plantilla as $k => $val) {
                $objeto_plantilla = new \stdClass;
                $objeto_plantilla->id = $val->id;
                $objeto_plantilla->hora_inicio = $val->hora_inicio;
                $objeto_plantilla->hora_fin = $val->hora_fin;
                if ($request->cursos) {
                    $objeto_plantilla->horario = HorarioInscripciones::with('curso')
                        ->where("plantilla_horarios_id", $val->id)
                        ->where("areas_id", $request->area)
                        ->whereIn("curricula_detalles_id", $request->cursos)
                        ->orderBy("dia", "asc")
                        ->get();
                } else {
                    $objeto_plantilla->horario = [];
                }
                $obj->plantilla[] = $objeto_plantilla;
            }
            $horario[] =  $obj;
        }
        // $plantillaHorario = plantillaHorario::where("turnos_id",$turno)->get();

        return response()->json($horario);
    }
    public function getCursos(Request $request)
    {
        $curricula = Curricula::with("area")->where("estado", "1")->where("areas_id", $request->area)->get();
        // dd($curricula);
        // $area = Area::get();
        // $cursos = Curso::orderBy("areas_id","asc")->get();
        $cursos = [];
        foreach ($curricula as $key => $value) {
            $obj = new \stdClass;
            $obj->id = $value->id;
            $obj->area = $value->area->denominacion;
            $obj->cursos = CurriculaDetalle::select("curricula_detalles.*", "c.denominacion", "c.color")
                ->join("cursos as c", "c.id", "curricula_detalles.cursos_id")
                ->where("curriculas_id", $value->id)
                ->get();
            $cursos[] =  $obj;
        }

        return response()->json($cursos);
    }
    public function getProgramas()
    {
        $programas = Programa::all();

        return response()->json($programas);
    }
    public function getGradosAcademicos()
    {
        $grado = GradoAcademico::where("id", "!=", 2)->get();

        return response()->json($grado);
    }
    public function getExperiencias()
    {
        $data = Experiencia::all();
        return response()->json($data);
    }
    public function getCapacitaciones()
    {
        $data = CapacitacionTipo::all();
        return response()->json($data);
    }
    public function getProducciones()
    {
        $data = Producciones::all();
        return response()->json($data);
    }
    public function consultarInscripcion()
    {
        return view('web.inscripcion.consultar');
    }
    public function getLocalEstudiante($dni)
    {

        $data = DB::table('estudiantes as e')
            ->select(
                DB::raw("CONCAT(e.paterno,' ',e.materno,' ',e.nombres) as nombres"),
                "s.denominacion as sede",
                "l.denominacion as local",
                "l.direccion",
                "l.foto",
                "a.codigo as aula",
                "g.denominacion as grupo",
                "t.denominacion as turno",
                "ar.denominacion as area"
            )
            ->join("matriculas as m", "m.estudiantes_id", "e.id")
            ->join("grupo_aulas as ga", "ga.id", "m.grupo_aulas_id")
            ->join("grupos as g", "g.id", "ga.grupos_id")
            ->join("areas as ar", "ar.id", "ga.areas_id")
            ->join("turnos as t", "t.id", "ga.turnos_id")
            ->join("aulas as a", "a.id", "ga.aulas_id")
            ->join("locales as l", "l.id", "a.locales_id")
            ->join("sedes as s", "s.id", "l.sedes_id")
            ->where("e.nro_documento", $dni)
            ->first();

        if (isset($data)) {
            $response["status"] = true;
            $response["result"] = $data;
        } else {
            $response["status"] = false;
            $response["result"] = "";
        }

        return $response;
    }
    public function getVacantesAgotadas()
    {

        $response["vacantes"] = ConfiguracionVacante::with('area', 'sede', 'turno')->where('estado', '0')->get();

        return response()->json($response);
    }
}
