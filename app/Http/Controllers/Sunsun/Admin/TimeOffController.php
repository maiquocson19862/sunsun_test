<?php

namespace App\Http\Controllers\Sunsun\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MsHoliday;
use App\Models\MsKubun;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TimeOffController extends Controller
{
    public function beforeCreate($postdata=null) {
        $data = [];
        $data['list_holiday'] = $this->Search($postdata);
        $data['list_kubun'] = MsKubun::GetTimeKubunHoliday($postdata);
        $data['list_kubun_hotel'] = MsKubun::GetKubunHotelHoliday();
        foreach ($data['list_kubun_hotel'] as $row) {
            $data['list_kubun'][] = $row;
        }
        return $data;
    }
    public function Create()
    {
    	$data = $this->beforeCreate();
        return view('sunsun.admin.time_off', $data);
    }

    public function Search($data = null)
    {
    	$datetime = ($data == null) ? Carbon::now() : new Carbon($data["date_search"]);
        $dayOfWeek = $datetime->dayOfWeek;
        $day_diff = 0;
        // if ($dayOfWeek == 3) $day_diff = 2;
        // else if ($dayOfWeek == 4) $day_diff = 1;
        $datetime = $datetime->addDays($day_diff);
    	$date_search = $datetime->format('Ymd');
		$where = " type_holiday is not null and date_holiday = '$date_search' ";
    	return MsHoliday::whereRaw($where)->orderBy('type_holiday', 'ASC')->get();
    }

    public function GetAjax(Request $request)
    {
    	$data = $this->beforeCreate($request->all());
        return [
            'status' => true,
            'data' => $data
        ];
    }
    public function convertDateSubmit($date_search) {
        $arr = explode("-",$date_search);
        if (count($arr) == 1) $arr = explode("/",$date_search);
        Log::debug("start convertDateSubmit");
        $arr[0] = str_pad($arr[0],4,"0",STR_PAD_LEFT);
        $arr[1] = str_pad($arr[1],2,"0",STR_PAD_LEFT);
        $arr[2] = str_pad($arr[2],2,"0",STR_PAD_LEFT);
        return $arr[0].$arr[1].$arr[2];

    }
    public function Submit(Request $request)
    {
        $check_result = false;
        try {
            $data = $request->all();
            Log::debug($data['date_search']);
            $date_search = $this->convertDateSubmit($data['date_search']);
            // delete data history
            MsHoliday::WhereRaw("date_holiday = $date_search and type_holiday is not null")->delete();
            // add new
            if (!empty($data["data"])) {
                foreach ($data["data"] as $value) {
                    $arr = explode("_",$value);
                    $holiday = new MsHoliday;
                    $holiday->date_holiday = $date_search;
                    $holiday->type_holiday = $arr[0];
                    $holiday->time_holiday = $arr[1];
                    $holiday->save();
                }
            }
            $check_result = true;
        } catch (\Exception $ex) {
            Log::debug("-----error save time off holiday start------");
            Log::debug($ex->getMessage());
            Log::debug("-----error save time off holiday end------");
        }
        // return finish
        return [
            'status' => $check_result
        ];
    }
}
