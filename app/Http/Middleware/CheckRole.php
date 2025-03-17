<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
class CheckRole {
    public function handle($request, Closure $next)
    {
        $ms_user = Auth::user();
        if (!empty($ms_user) && $ms_user->user_type == config('const.auth.permission.ADMIN')) {
            return $next($request);
        }
        $check_admin = $request->session()->get('check_admin');
        if (empty($check_admin)) {
            $request->session()->put('check_admin',"1");
            \Auth::logout();
            return \Redirect::to("/admin/login");
        } else {
            $request->session()->forget('check_admin');
            return \Redirect::to("/admin/login");
        }
    }
}