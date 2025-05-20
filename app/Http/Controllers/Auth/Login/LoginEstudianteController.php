<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Estudiante;
use App\Models\Inscripciones;

class LoginEstudianteController extends Controller
{
    use AuthenticatesUsers;

    protected $guard = 'estudiante';

    protected $redirectTo = '/estudiante/dashboard';
    // public function __construct()
    // {
    //     $this->middleware('guest:docente')->except('logout');
    // }
    public function showLoginForm()
    {
        return view('auth.login.login-estudiante');
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
        // try {
        $user = Socialite::driver('google')->stateless()->user();

        // dd($user);
        $findUser = Estudiante::where('idgsuite', $user->id)->first();

        $idUser = $findUser ? $findUser->id : '';
        // dd($idUser);
        $inscripcion = Inscripciones::where([['matricula', '1'], ['estudiantes_id', $idUser]])->first();
        // dd($findUser);
        if ($inscripcion) {
            Auth::guard('estudiante')->login($findUser);
            return redirect('estudiante/dashboard');
        } else {
            // Auth::login($findUser);
            return redirect('/estudiante/login')->with('status', 'Su cuenta no pertenece a la de un estudiante');
        }
        // } catch (Exception $e) {
        //     dd($e->getMessage());
        // }

        // $user->token;
    }
}
