<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\DocenteApto;
use App\Estudiante;
use App\Models\Inscripciones;

class LoginGoogleController extends Controller
{
    use AuthenticatesUsers;

    protected $guard = '';

    protected $redirectTo = '/estudiante/dashboard';
    // public function __construct()
    // {
    //     $this->middleware('guest:docente')->except('logout');
    // }
    public function showLoginForm()
    {
        return view('auth.login.login-google');
    }

    protected function guard()
    {
        return Auth::guard($this->guard);
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        $docenteApto = DocenteApto::where('idgsuite', $user->id)->first();
        $estudiante = Estudiante::where('idgsuite', $user->id)->first();
        $idEstudiante = $estudiante ? $estudiante->id : '';
        // validar periodo para el siguiente proceso
        $inscripcion = Inscripciones::where([['matricula', '1'], ['estudiantes_id', $idEstudiante]])->first();


        if ($docenteApto) {
            $this->guard = 'docente';

            Auth::guard('docente')->login($docenteApto);
            return redirect('docente/dashboard');
        } elseif ($inscripcion) {
            $this->guard = 'estudiante';

            Auth::guard('estudiante')->login($estudiante);
            return redirect('estudiante/dashboard');
        } else {
            // Auth::login($findUser);
            return redirect('/web/login')->with('status', 'Acceso Denegado');
        }
    }
    public function logout()
    {
        // dd("docente");
        if (Auth::guard('docente')->check()) // this means that the admin was logged in.
        {
            Auth::guard('docente')->logout();
            return redirect('web.login');
        } elseif (Auth::guard('estudiante')->check()) {
            Auth::guard('estudiante')->logout();
            dd("estudiante");
            return redirect('web.login');
        }

        $this->guard()->logout();
        $request->session()->invalidate();

        return redirect('/');
    }
}
