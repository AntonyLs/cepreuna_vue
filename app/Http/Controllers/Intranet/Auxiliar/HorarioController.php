<?php

namespace App\Http\Controllers\Intranet\Auxiliar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        return view("intranet.auxiliar.horario");
    }
}
