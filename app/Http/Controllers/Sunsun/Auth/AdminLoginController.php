<?php

namespace App\Http\Controllers\Sunsun\Auth;

use App\Http\Controllers\Controller;
use App\Models\MsUser;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

class AdminLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     *
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        if ($this->check_admin())
            $this->redirectTo = route('admin.day');
        else  {
            Auth::logout();
            return \Redirect::to("/admin/login");
        }
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('sunsun.auth.login');
    }
    public function check_admin() {
        $ms_user = Auth::user();
        if (!empty($ms_user) && $ms_user->user_type == config('const.auth.permission.ADMIN'))
            return true;
        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        if ($this->check_admin())
            return redirect()->route('admin.day');
        else {
            Auth::logout();
            return \Redirect::to("/admin/login");
        }
    }
    protected function redirectTo()
    {
        // $ms_user = Auth::user();
        // $currentRoute = Route::getCurrentRoute()->getName();
        // if ($ms_user->is_admin() && $currentRoute == "auth-admin") {
        //     return route('admin.day');
        // }
        // return route('home');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request)
        );
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect()->route('admin-login');
    }
}
