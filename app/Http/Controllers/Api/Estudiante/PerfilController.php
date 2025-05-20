<?php

namespace App\Http\Controllers\Api\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function getEstudiante(Request $request)
    {
        $token = explode("-", Crypt::decryptString($request->_token));
        $datos = Estudiante::with('tipoDocumento', 'colegio')
            ->select('nombres', 'paterno', 'materno', 'nro_documento', 'tipo_documentos_id', 'usuario', 'colegios_id', 'foto', 'celular')
            ->where('id', $token[0])
            ->where('nro_documento', $token[1])
            ->first();
        if ($datos) {
            $response["status"] = true;
            $response["datos"] = $datos;
        } else {
            $response["status"] = false;
            $response["datos"] = [];
        }
        return response()->json($response);
    }
    public function guardarFoto(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $estudiante = Estudiante::find($id);
            $imageName  = $this->save_image($request->foto);
            $estudiante->foto = $imageName;
            $estudiante->save();

            DB::commit();
            $response["status"] = true;
            $response["imageName"] = $imageName;
        } catch (\Exception $e) {
            DB::rollback();
            $response["status"] = false;
        }
        return response()->json($response);
    }
    public function save_image($base64_image)
    {

        $img = $this->getB64Image($base64_image);

        $ldate = date('Ymd_His');
        $first = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
        $img_name = $first . $ldate . '.png';

        Storage::disk('fotos')->put($img_name, $img);
        // Storage::disk('fotos')->putFileAs($this->dateTimePartial, $img, $img_name);
        return $img_name;
    }
    public function getB64Image($base64_image)
    {

        $image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
        $image = base64_decode($image_service_str);

        return $image;
    }
    public function encrypt($id)
    {
        return Crypt::encryptString($id);
    }
}
