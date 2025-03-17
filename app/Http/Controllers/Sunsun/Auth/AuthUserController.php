<?php

namespace App\Http\Controllers\Sunsun\Auth;

use App\Http\Controllers\Controller;
use App\Models\MsUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Sunsun\Front\BookingController;

class AuthUserController extends Controller {
    public function __construct() {
//        $this->middleware('auth');
    }

    protected function validator(array $data) {
        return Validator::make($data, [
            'username' => 'required|string|max:255',
            'tel' => 'required|numeric|regex:/[0-9]{10,11}/',
        ]);
    }
    public function edit(Request $request) {
        $data = [];
        if($request->isMethod('post')) {
            $data_request = $request->all();
            $validation = $this->validator($data_request);
            $error = $validation->errors();
            $bookCon = new BookingController();
            $check_kata = $bookCon->check_is_katakana($data_request['username']);
            if ($validation->fails() || !$check_kata) {
                if (!$check_kata) $error->add('username', config('const.error.katakana'));
                return redirect()
                    ->back()
                    ->withErrors($error)
                    ->withInput($request->all());
            }
            $this->update($data, $data_request);
        }
        $ms_user_id = Auth::user()->ms_user_id;
        $data['user'] = MsUser::find($ms_user_id);

        return view('sunsun.auth.edit')->with($data);
    }


    public function update ( &$data, $data_request) {
        $user_id = Auth::id();
        $user = MsUser::find($user_id);
        $user->username = $data_request['username'];
        // $user->gender = $data_request['gender'];
        $user->tel = $data_request['tel'];
        // $user->birth_year = $data_request['birth_year'];
        $user->save();
        $data['success'] = '変更に成功しました。';
    }


    public function changepassword (Request $request) {
        if ($request->isMethod('get')) {
            return view('sunsun.auth.change_password');
        }
        $data = $request->all();
        $current_password = \Auth::User()->password;
        $ms_user_id = \Auth::User()->ms_user_id;
        if(\Hash::check($request->input('old_password'), $current_password)) {
            $obj_user = MsUser::find($ms_user_id);
            $obj_user->password = \Hash::make($data['password']);
            $obj_user->save();
            $data['success'] = 'パスワードを変更しました';
            return view('sunsun.auth.change_password')->with($data);
        } else {
            $error['old_password'] = '間違ったパスワード';
            return redirect()->back()->withErrors($error);
        }

    }
}
