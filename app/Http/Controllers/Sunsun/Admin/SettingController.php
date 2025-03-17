<?php

namespace App\Http\Controllers\Sunsun\Admin;

use App\Http\Controllers\Controller;
use App\Models\MsKubun;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    function __construct() {}


    public function setting() {
        $data = config('const.db.kubun_type');
        return view('sunsun.admin.setting',['data' => $data]);
    }
    public function get_setting_type(Request $request) {
        $data = $request->all();
        $MsKubun = MsKubun::all();
        $data['kubun_type'] = $MsKubun->where('kubun_type',$data['kubun_type'])->sortBy('sort_no');
        return view('sunsun.admin.parts.setting_type',$data)->render();
    }
    public function get_setting_kubun_type(Request $request) {
        $data = $request->all();
        if($data['new'] == 0){
            $MsKubun = MsKubun::all();
            $data['kubun_id'] = $MsKubun->where('kubun_type',$data['kubun_type'])->where('kubun_id',$data['kubun_id']);
        }
        // dd($data);
        return view('sunsun.admin.parts.setting_kubun_type',$data)->render();
    }

    public function update_setting_sort_no(Request $request) {

        $data = $request->all();
//        dd($data);
        if($data['type'] == 'up'){
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', $data['sort_no'])->update(['sort_no' =>'-1']);
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', $data['sort_no'] - 1)->update(['sort_no' =>$data['sort_no']]);
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', '-1')->update(['sort_no' =>$data['sort_no'] -1]);
        }else{
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', $data['sort_no'])->update(['sort_no' =>'-1']);
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', $data['sort_no'] + 1)->update(['sort_no' =>$data['sort_no']]);
            MsKubun::where('kubun_type',$data['kubun_type'])->where('sort_no', '-1')->update(['sort_no' =>$data['sort_no'] +1]);
        }
    }
    public function update_setting_kubun_type(Request $request) {
        $data = $request->all();
        if((strlen($data['kubun_id']) == 0 )  || (strlen($data['kubun_id']) > 2 ) || (strlen($data['kubun_value']) == 0 ) || (strlen($data['kubun_value']) > 255 )){
            return response()->json([
                'msg' => "length error!",
            ], 400);
        }


        if($data['new'] == 1){
            if (MsKubun::where('kubun_type', $data['kubun_type'])->where('kubun_id', $data['kubun_id'])->count() > 0) {
                return response()->json([
                    'msg' => "kubun_id exist!",
                ], 400);
            }
            $MsKubun = MsKubun::create(['kubun_type' => $data['kubun_type'],'kubun_id' => $data['kubun_id'], 'kubun_value' => $data['kubun_value'],'sort_no' => $data['sort_no'], 'notes'=> $data['notes']]);
        }else{
            MsKubun::where('kubun_type', $data['kubun_type'])->where('kubun_id', $data['kubun_id'])->update(['kubun_value' => $data['kubun_value'], 'notes'=> $data['notes']]);
        }
    }
    public function delete_setting_kubun_type(Request $request) {
        $data = $request->all();
        foreach ($data['arr_delete'] as $value) {
            MsKubun::where('kubun_type', $data['kubun_type'])->where('kubun_id', $value)->delete();
        }
        return response()->json([
            'msg' => 'success',
        ], 200);
    }

}
