<?php

use App\Models\Yoyaku;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Mail;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/aaa', function () {
//     $payment  = new Payment();
//     $payment->booking_id = "1";
//     $payment->access_id = "1";
//     $payment->access_pass = "1";
//     $payment->save();
// });
// Route::get('/phantom', function () {
//
// });


// Route::get('/clear_yoyaku', function () {
//     DB::table('tr_jobs')->truncate();
//     DB::table('tr_failed_jobs')->truncate();
//     Yoyaku::truncate();
//     \App\Models\YoyakuDanjikiJikan::truncate();
//     echo "done";
// });
// Route::get('/clear_nomal_user', function () {
//     \App\Models\MsUser::where('user_type', '<>', "admin")->delete();
//     echo "done";
// });

// function new_test(){
//     $Yoyaku1 = new Yoyaku();
//     $Yoyaku1->booking_id = 2;
//     $Yoyaku1->course = 7;
//     $Yoyaku1->save();
// }


Route::get('/migrate', function () {
    exec("alias php=\'/usr/local/php7.3/bin/php\'");
    \Artisan::call('migrate');
    echo "Migrate done!";
});

// Route::get('/reset', function () {
//     exec("alias php=\'/usr/local/php7.3/bin/php\'");
//     \Artisan::call('migrate:reset');
//     \Artisan::call('migrate');
//     \Artisan::call('db:seed');
//     echo "Reset done!";
// });

Route::get('/cache', function () {
    exec("alias php=\'/usr/local/php7.3/bin/php\'");
    \Artisan::call('cache:clear');
    \Artisan::call('route:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear ');
    echo "Cache cleared!";
});


Route::get('/law', function () {
    return view('sunsun.front.law');
})->name('law');
Route::get('/cancellation_policy', function () {
    return view('sunsun.front.cancellation_policy');
})->name('cancellation_policy');
Route::middleware('begin.auth')->group(function(){
    Route::namespace('Sunsun\Front')->group(function (){
        Route::get('/main', function (){
            return view('sunsun.front.main');
        })->name('main');
        Route::get('/thanks', function (){

        })->name('thanks');

        Route::get('/', function () {
            return view('sunsun.front.index');
        })->name('home');
        Route::get('/clear_session',['as' => '.clear_session', 'uses' => 'BookingController@clear_session']);

        Route::get('/booking',['as' => '.booking', 'uses' => 'BookingController@booking']);
        Route::post('/booking',['as' => '.back_2_booking', 'uses' => 'BookingController@back_2_booking']);
        Route::any('/confirm',['as' => '.confirm', 'uses' => 'BookingController@confirm']);
        Route::any('/payment',['as' => '.payment', 'uses' => 'BookingController@payment']);
        Route::post('/complete',['as' => '.complete', 'uses' => 'BookingController@complete']);
        Route::post('/make_payment',['as' => '.make_payment', 'uses' => 'BookingController@make_payment']);



        Route::post('/get_service',['as' => '.get_service', 'uses' => 'BookingController@get_service']);

        Route::post('/get_free_room',['as' => '.get_free_room', 'uses' => 'BookingController@get_free_room']);


        Route::post('/get_time_room',['as' => '.get_time_room', 'uses' => 'BookingController@get_time_room']);
        Route::get('/get_time_room',['as' => '.get_time_room', 'uses' => 'BookingController@get_time_room']);
        Route::post('/book_room',['as' => '.book_room', 'uses' => 'BookingController@get_time_room']);
        Route::post('/book_time_room_wt',['as' => '.book_time_room_wt', 'uses' => 'BookingController@book_time_room_wt']);
        Route::post('/book_time_room_pet',['as' => '.book_time_room_pet', 'uses' => 'BookingController@book_time_room_pet']);
        Route::post('/add_new_booking',['as' => '.add_new_booking', 'uses' => 'BookingController@add_new_booking']);
        Route::post('/validate_before_booking',['as' => '.validate_before_booking', 'uses' => 'BookingController@validate_before_booking']);
        Route::post('/save_booking',['as' => '.add_new_booking', 'uses' => 'BookingController@save_booking']);

    });

    // Auth
    Route::namespace('Sunsun\Auth')->group(function (){
        Route::get('/register', ['as' => 'register',  'uses' => 'MsUserController@register']);
        Route::get('/login', ['as' => 'login',  'uses' => 'LoginController@showLoginForm']);
        Route::get('/admin/login', ['as' => 'admin-login',  'uses' => 'LoginController@showLoginForm']);
        Route::post('/login', ['as' => 'auth', 'uses' => 'LoginController@login']);
        Route::post('/login-admin', ['as' => 'auth-admin', 'uses' => 'AdminLoginController@login']);
        Route::get('/logout', ['as' => 'logout', 'uses' => 'LoginController@logout']);
        Route::post('/create', ['as' => '.create',  'uses' => 'MsUserController@create']);
        Route::get('/forgot_password', ['as' => '.forgot_password',  'uses' => 'ResetPasswordController@index']);
        Route::post('/forgot_password', ['as' => '.reset_password',  'uses' => 'ResetPasswordController@exec']);
        Route::get('/reset_password/{token}', ['as' => '.change_password',  'uses' => 'ResetPasswordController@change']);
        Route::put('/reset_password', ['as' => '.reset_password',  'uses' => 'ResetPasswordController@update']);
    });
    // user
    Route::middleware('user.auth')->namespace('Sunsun\Auth')->group(function (){
        Route::get('/profile', ['as' => '.edit',  'uses' => 'AuthUserController@edit']);
        Route::post('/profile', ['as' => '.upload',  'uses' => 'AuthUserController@edit']);
        Route::get('/change_password', ['as' => '.change_password',  'uses' => 'AuthUserController@changepassword']);
        Route::put('/change_password', ['as' => '.changepassword',  'uses' => 'AuthUserController@changepassword']);
    });
    // Admin
    Route::group(['middleware' => 'CheckRole'], function () {
        Route::middleware('admin.auth')->prefix('admin')->name('admin')->namespace('Sunsun\Admin')->group(function (){
            Route::get('/',['as' => '.index', 'uses' => 'AdminController@index']);
            Route::any('/day',['as' => '.day', 'uses' => 'AdminController@day']);
            Route::get('/weekly',['as' => '.weekly', 'uses' => 'AdminController@weekly']);
            Route::get('/monthly',['as' => '.monthly', 'uses' => 'AdminController@monthly']);
            Route::get('/setting',['as' => '.setting', 'uses' => 'SettingController@setting']);
            Route::post('/get_setting_type',['as' => '.get_setting_type', 'uses' => 'SettingController@get_setting_type']);
            Route::post('/get_setting_kubun_type',['as' => '.get_setting_kubun_type', 'uses' => 'SettingController@get_setting_kubun_type']);
            Route::post('/update_setting_kubun_type',['as' => '.update_setting_kubun_type', 'uses' => 'SettingController@update_setting_kubun_type']);
            Route::post('/update_setting_sort_no',['as' => '.update_setting_sort_no', 'uses' => 'SettingController@update_setting_sort_no']);
            Route::delete('/delete_setting_kubun_type',['as' => '.delete_setting_kubun_type', 'uses' => 'SettingController@delete_setting_kubun_type']);

            Route::post('/edit_booking',['as' => '.edit_booking', 'uses' => 'AdminController@edit_booking']);
            // Route::post('/booking_history',['as' => '.booking_history', 'uses' => 'AdminController@booking_history']);
            Route::post('/show_history',['as' => '.show_history', 'uses' => 'AdminController@show_history']);
            Route::post('/update_booking',['as' => '.update_booking', 'uses' => 'AdminController@update_booking']);
            Route::post('/delete_booking',['as' => '.delete_booking', 'uses' => 'AdminController@delete_booking']);
            // user admin
            Route::get('/msuser',['as' => '.user', 'uses' => 'AdminController@user']);
            Route::post('/update_user',['as' => '.update_user', 'uses' => 'AdminController@update_user']);
            // Route::get('/search_user',['as' => '.search_user', 'uses' => 'AdminController@get_search_user']);
            Route::get('/search-paginate',['as' => '.search-paginate', 'uses' => 'AdminController@get_data_search_pagination']);
            Route::get('/export',['as' => '.export', 'uses' => 'AdminController@export']);

            // holiday
            Route::get('/ms_holiday',['as' => '.msholiday', 'uses' => 'AdminController@ms_holiday']);
            Route::post('/create',['as' => '.addmsholiday', 'uses' => 'AdminController@add_holiday']);
            Route::post('/update_holiday',['as' => '.update_holiday', 'uses' => 'AdminController@update_holiday']);
            Route::get('/holiday_search_paginate',['as' => '.search-paginate', 'uses' => 'AdminController@get_data_holiday_search_pagination']);

            // time off holiday
            Route::get('/time_off',['as' => '.timeoff', 'uses' => 'TimeOffController@Create']);
            Route::post('/ajax_time_off',['as' => '.ajax_time_off', 'uses' => 'TimeOffController@GetAjax']);
            Route::post('/submit_time_off',['as' => '.submit_time_off', 'uses' => 'TimeOffController@Submit']);
            // day off holiday
            Route::get('/day_off',['as' => '.dayoff', 'uses' => 'DayOffController@Create']);
            //Route::post('/create',['as' => '.create_dayoff', 'uses' => 'DayOffController@Submit']);
            Route::post('/submit_day_off',['as' => '.submit_day_off', 'uses' => 'DayOffController@Submit']);
            Route::post('/ajax_day_off',['as' => '.ajax_day_off', 'uses' => 'DayOffController@GetAjax']);
            // setting
            Route::get('/setting',['as' => '.setting', 'uses' => 'AdminController@setting']);
            Route::post('/update_setting',['as' => '.update_setting', 'uses' => 'AdminController@update_setting']);
            // day off holiday_acom
            Route::get('/day_off_acom',['as' => '.dayoff_acom', 'uses' => 'DayOffController@CreateAcom']);
            Route::post('/submit_day_off_acom',['as' => '.submit_day_off_acom', 'uses' => 'DayOffController@SubmitAcom']);
            Route::post('/ajax_day_off_acom',['as' => '.ajax_day_off_acom', 'uses' => 'DayOffController@GetAjaxAcom']);
            // notes
            Route::post('/ajax_save_notes',['as' => '.ajax_save_notes', 'uses' => 'AdminController@ajax_save_notes']);
            Route::post('/ajax_name_search',['as' => '.ajax_name_search', 'uses' => 'AdminController@ajax_name_search']);
            // payments_history
            Route::get('/create_payments_history',['as' => '.create_payments_history', 'uses' => 'AdminController@create_payments_history']);
            Route::get('/sales_list',['as' => '.sales_list', 'uses' => 'AdminController@sales_list']);
            Route::get('/pagination_sales_list',['as' => '.pagination_sales_list', 'uses' => 'AdminController@pagination_sales_list']);
            Route::get('/export_sales_list',['as' => '.export_sales_list', 'uses' => 'AdminController@export_sales_list']);
            Route::get('/day_on',['as' => '.dayon', 'uses' => 'DayOnController@Create']);
            Route::post('/submit_day_on',['as' => '.submit_day_on', 'uses' => 'DayOnController@Submit']);
            Route::post('/ajax_day_on',['as' => '.ajax_day_on', 'uses' => 'DayOnController@GetAjax']);
        });
    });
});
