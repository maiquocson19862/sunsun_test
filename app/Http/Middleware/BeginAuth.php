<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use \Session;
use App\Http\Controllers\Sunsun\Front\BookingController;

class BeginAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	$bookCon = new BookingController();
        Session::put("date_holiday",$bookCon->get_free_holiday());
        return $next($request);
        // $AUTH_USER = 'sunsunad';
        // $AUTH_PASS = '123456';
        // header('Cache-Control: no-cache, must-revalidate, max-age=0');
        // $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        // $is_not_authenticated = (
        //     !$has_supplied_credentials ||
        //     $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
        //     $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        // );
        // if ($is_not_authenticated) {
        //     header('HTTP/1.1 200 Authorization Required');
        //     header('WWW-Authenticate: Basic realm="Access denied"');
        //     echo "Login issues. Access denied!";
        //     exit;
        // }
        // return $next($request);
        // return Auth::onceBasic() ?: $next($request);
    }
}
