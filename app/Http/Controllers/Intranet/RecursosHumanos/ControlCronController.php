<?php

namespace App\Http\Controllers\Intranet\RecursosHumanos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TPControlCron;

class ControlCronController extends Controller
{
    public function indexCronCorreo()
    {
        // $tmp = Matricula::with('estudiante')->find(1);
        // dd($tmp->estudiante->idgsuite);
        return view('intranet.recursosHumanos.cron-accesos');
    }
    public function controlCorreo()
    {
        $query = TPControlCron::where([["estado", "0"], ["tipo", "1"]])->first();
        if ($query) {
            $response["query"] = $query;
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        return $response;
    }
}
