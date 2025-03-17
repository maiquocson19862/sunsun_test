<?php

namespace App\Http\Controllers\Sunsun\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Sunsun\Front\BookingController;
use App\Models\MsHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\MsHolidayAcom;

class DayOffController extends Controller
{
    public function beforeCreate($param = null)
    {
        $data = [];
        $bookCon = new BookingController();
        $date = Carbon::now();
        $year = $date->year;
        $year_full = (empty($param))? $year : $param;
        $data['list_holiday'] = $bookCon->get_free_holiday($year_full);
        return $data;
    }
    public function beforeCreateAcom($param = null)
    {
        $data = [];
        $bookCon = new BookingController();
        $date = Carbon::now();
        $year = $date->year;
        $year_full = (empty($param))? $year : $param;
        $data['list_holiday'] = $bookCon->get_free_holiday_acom($year_full);
        return $data;
    }

    public function Create(Request $request)
    {
        $data = $this->beforeCreate();
        return view('sunsun.admin.day_off', $data);
    }

    public function Submit(Request $request)
    {
        $check_result = false;
        try {
            $data = $request->all();
            $year = $data['year'];
            // delete data history
            $range_day = DB::select("
                SELECT  *
                FROM    ms_holiday
                WHERE   type_holiday is null and SUBSTR(date_holiday,1,4) = $year
            ");
            foreach ($range_day as $value) {
                DB::table('ms_holiday')->where('ms_holiday_id', $value->ms_holiday_id)->delete();
            }
            // add new
            if (!empty($data["data"])) {
                foreach ($data["data"] as $value) {
                    $holiday = new MsHoliday;
                    $holiday->date_holiday = $value;
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

    public function GetAjax(Request $request)
    {
    	$data = $this->beforeCreate($request->year_search);
        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function CreateAcom(Request $request)
    {
        $data = $this->beforeCreateAcom();
        return view('sunsun.admin.day_off_acom', $data);
    }

    public function SubmitAcom(Request $request)
    {
        $check_result = false;
        try {
            $data = $request->all();
            $year = $data['year'];
            // delete data history
            $range_day = DB::select("
                SELECT  date_holiday
                FROM    ms_holiday_acom
                WHERE SUBSTR(date_holiday,1,4) = $year
            ");
            foreach ($range_day as $value) {
                DB::table('ms_holiday_acom')->where('ms_holiday_acom_id', $value->ms_holiday_acom_id)->delete();
            }
            // add new
            if (!empty($data["data"])) {
                foreach ($data["data"] as $value) {
                    $holiday = new MsHolidayAcom;
                    $holiday->date_holiday = $value;
                    $holiday->save();
                }
            }
            $check_result = true;
        } catch (\Exception $ex) {
            Log::debug($ex->getMessage());
        }
        return [
            'status' => $check_result
        ];
    }

    public function GetAjaxAcom(Request $request)
    {
        $data = $this->beforeCreateAcom($request->year_search);
        return [
            'status' => true,
            'data' => $data
        ];
    }
}
