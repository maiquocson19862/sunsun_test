<?php

namespace App\Http\Controllers\Sunsun\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\ForgotJob;
use App\Models\MsUser;
use App\Models\PasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller {

    public function index(Request $request) {
        return view('sunsun.auth.forgot_password');
    }

    public function exec (Request $request) {
        $data = [];
        $data['status'] =  false;
        $data['notify'] = __('auth.user_not_found');
        $email = $request->email;
        $user = MsUser::where('email', $email)->first();

        if($user){
            $password_reset = PasswordReset::updateOrCreate([
                'email' => $user->email,
            ], [
                'token' => Str::random(60),
            ]);
            if($password_reset){
                ForgotJob::dispatch($email, $password_reset->token, $user);
                $data['status'] =  true;
                $data['notify'] = __('auth.check_inbox');
            }
        }

        return view('sunsun.auth.forgot_password', $data);
    }

    public function change(Request $request, $token){
        $data = $request->all();
        $data['token'] = $token;
        $passwordReset = PasswordReset::where('token', $data['token'])->first();
        if($passwordReset){
            $data['forgot'] = true;
            \Auth::logout();
            return view('sunsun.auth.change_password', $data);
        }else{
            return redirect("/login");
        }

    }

    public function update(Request $request){
        $data = $request->all();
        $data['forgot'] = true;
        if(($data['password_repeat'] !== $data['password']) || (isset($data['password_repeat']) === false) || (isset($data['password']) === false) ){
            $data['status'] = false;
            $data['notify'] = __('auth.repeat_not_match');
            return view('sunsun.auth.change_password', $data);
        }
        $passwordReset = PasswordReset::where('token', $data['token'])->first();
        if($passwordReset){
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
                $passwordReset->delete();
                $data['notify'] = __('auth.token_invalid');
                return view('sunsun.auth.change_password', $data);
            }
            $user = MsUser::where('email', $passwordReset->email)->first();
            $updatePasswordUser = $user->update(['password' => bcrypt($data['password'])]);
            $passwordReset->delete();
            if($updatePasswordUser){
                $data['status'] = true;
                $data['notify'] = __('auth.update_successfully');
                return view('sunsun.auth.change_password', $data);
            }
        }else{
            $data['status'] = false;
            $data['notify'] = __('auth.update_failed');
            return view('sunsun.auth.change_password', $data);
        }
    }
}
