<?php

namespace App\Http\Controllers\Web\Docente;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use Illuminate\Http\Request;
use App\Models\Docente;
use App\Models\DocenteApto;
use App\Models\AdjuntoGrado;
use App\Models\InscripcionDocente;


use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    public function index()
    {
        // dd(Auth::user()->usuario);
        return view('web.docente.perfil');
    }
    public function getDocente(){
        $idDocenteApto = Auth::user()->id;
        $docenteApto = DocenteApto::find($idDocenteApto);

        $response['docente'] = Docente::with('tipoDocumento','gradoAcademico','programa')->find($docenteApto->docentes_id);
        $response['docentea'] = Auth::user()->usuario;
        $inscripcion = InscripcionDocente::where("docentes_id",$docenteApto->docentes_id)->first();
        // dd($inscripcion);
        $response["grados"] = AdjuntoGrado::with(["gradoAcademico","programa"])->where("inscripcion_docentes_id",$inscripcion->id)->get();
        return response()->json($response);
    }
}
