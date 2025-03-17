<?php

namespace App\Http\Middleware;

use App\Models\MsUser;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            //$ms_user = Auth::user();
            $currentRoute = Route::getCurrentRoute()->getName();
            if (Str::startsWith($currentRoute, 'admin')) {
                return redirect()->route('admin.day');

            } else {
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
