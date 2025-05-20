<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RedSocialController extends Controller
{
    public function token(){
        dd(Crypt::encryptString("CepreUna2022"));
    }
    public function validator(Request $request){
        $token = "eyJpdiI6ImhTSVMvUmlwL3o3WmdTTDRhQkZlU3c9PSIsInZhbHVlIjoia2xWbzN1dWtnaEUrbjlBNE5lZEN0QT09IiwibWFjIjoiNTU3MTc5MTYwZDZiYmM5NTBhZTAwYTg5OTkxMjQ1ZjMyNTdkODk1ZjUxZGIyMjNmN2MwMjdhYWI1ODA2NWJhZCJ9";
        if($request->_token==$token){
            $usuario = $request->email;
            $idgsuite = $request->idgsuite;
            $query = Estudiante::select(
                        "nombres as Nombres",
                        DB::raw("CONCAT(paterno,' ',materno) as Apellidos"),
                        "nro_documento as Dni",
                        "celular as Celular",
                        DB::raw("IF(estado='1','Activo','Inactivo') as Estado")
                        )
                        ->where("usuario",$usuario)
                        ->where("idgsuite",$idgsuite)
                        ->where("estado","1")
                        ->first();
            // dd(empty($query),$query);
            if($query){
                $response["status"] = true;
                $response["message"] = "successful access.";
                $response["data"] = $query;
            }else{
                $response["status"] = false;
                $response["message"] = "invalid user.";
            }
            return response()->json($response);
        }else{
            $response["status"] = false;
            $response["message"] = "access denied.";
            return response()->json($response);
        }
    }
}

// nombres
// apellidos
// dni
// celular
// idgoogle
// estado
