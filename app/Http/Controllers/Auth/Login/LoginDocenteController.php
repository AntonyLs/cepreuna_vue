<?php

namespace App\Http\Controllers\Auth\Login;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\DocenteApto;
use Exception;

class LoginDocenteController extends Controller
{

    use AuthenticatesUsers;

    protected $guard = 'docente';

    protected $redirectTo = '/docente/dashboard';
    public function __construct()
    {
        $this->middleware('guest:docente')->except('logout');
    }
    public function showLoginForm()
    {
        return view('auth.login.login-docente');
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
            $findUser = DocenteApto::where('idgsuite',$user->id)->first();
// dd($findUser);
            if($findUser)
            {
                Auth::guard('docente')->login($findUser);
                return redirect('docente/dashboard');
            }else{
                // Auth::login($findUser);
                return redirect('/docente/login')->with('status', 'Su cuenta no pertenece a la de un docente');;
            }
        // } catch (Exception $e) {
        //     dd($e->getMessage());
        // }

        // $user->token;
    }

}
