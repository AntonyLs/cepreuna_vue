<?php

namespace App\Http\Controllers\Web\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:docente');
    }
    public function index()
    {
        // dd(Auth::user());
        return view('web.docente.dashboard');
    }
}
