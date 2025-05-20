<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use App\Models\Matricula;
use App\Models\Periodo;
use App\Models\Estudiante;
use App\Models\InscripcionSimulacro;
use Illuminate\Http\Request;

// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class FichaSimulacroController extends Controller
{
    public function getFicha(Request $request)
    {
        if (request()->header('Authorization') !== "cepreuna_v1_api") {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $credentials = $request->only('email', 'password');

        if ($estudiante = Estudiante::where('usuario', $credentials['email'])->first()) {

            // Verificar si se encontró el estudiante y la contraseña coincide
            if ($estudiante->usuario == $credentials['email'] && $estudiante->password == $credentials['password']) {

                //validacion
                $Existe = InscripcionSimulacro::where('estudiantes_id', $estudiante->id)->first();
                //return $Existe;
                if ($Existe) {
                    $url = "inscripciones/simulacro/".Crypt::encryptString($Existe->id);
                    $response["status"] = true;
                    $response["datos"] = $url;
                    $response["mensajes"] = "Acceso correcto";
                }else{
                    $response["status"] = false;
                    $response["datos"] = [];
                    $response["mensajes"] = "El estudiante no se encuentra registrado para el Examen Simulacro";
                }
            } else{
                $response["status"] = false;
                $response["datos"] = [];
                $response["mensajes"] = "Contraseña Invalida";
            }
        }else{
            $response["status"] = false;
            $response["datos"] = [];
            $response["mensajes"] = "Usted no es estudiante del Cepreuna";
        }
        return response()->json($response);
    }
}