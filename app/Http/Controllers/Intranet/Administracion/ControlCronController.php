<?php

namespace App\Http\Controllers\Intranet\Administracion;

use App\Http\Controllers\Controller;
use App\Models\CargaAcademica;
use App\Models\ControlCron;
use App\Models\Matricula;
use Illuminate\Http\Request;

class ControlCronController extends Controller
{
    //
    public function indexCronMatricula()
    {
        // $tmp = Matricula::with('estudiante')->find(1);
        // dd($tmp->estudiante->idgsuite);
        return view('intranet.administracion.cron-matricula');
    }

    public function controlMatricula()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "1"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }
    public function indexCronGrupo()
    {
        // $data = CargaAcademica::where("sincronizar", "0")->get();
        // dd(count($data));
        return view('intranet.administracion.cron-grupo');
    }

    public function controlGrupo()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "2"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }

    public function indexCronHabilitacion()
    {
        // $data = CargaAcademica::where("sincronizar", "0")->get();
        // dd(count($data));
        return view('intranet.administracion.cron-habilitacion');
    }

    public function controlHabilitacion()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "3"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }
    public function indexCronDocente()
    {
        // $tmp = Docente::with('estudiante')->find(1);
        // dd($tmp->estudiante->idgsuite);
        return view('intranet.administracion.cron-docente');
    }


    public function controlDocente()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "4"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }

    public function indexCronMinCalificacion()
    {
        return view('intranet.administracion.cron-minCalificacion');
    }
    public function controlMinCalificacion()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "10"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }

    public function indexCronCorreo()
    {
        // $tmp = Correo::with('estudiante')->find(1);
        // dd($tmp->estudiante->idgsuite);
        return view('intranet.administracion.cron-correo');
    }

    public function controlCorreo()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "5"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }

    //control encuesta docente
    public function indexCronEncuesta()
    {
        return view('intranet.administracion.cron-encuesta');
    }

    public function controlEncuesta()
    {
        $query = ControlCron::where([["estado", "0"], ["tipo", "15"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }
}
