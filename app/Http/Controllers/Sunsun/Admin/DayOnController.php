<?php
namespace App\Http\Controllers\Sunsun\Admin;
use App\Http\Controllers\Controller;
use App\Models\MsDayOn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class DayOnController extends Controller
{
	public function beforeCreate($param = null)
	{
		$data = [];
		$date = Carbon::now();
		$year = $date->year;
		$year_full = (empty($param))? $year : $param;
		$data['list_day_on'] = \Helper::getDayOn($year_full);
		return $data;
	}
	public function Create(Request $request)
	{
		$data = $this->beforeCreate();
		return view('sunsun.admin.day_on', $data);
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
				FROM    ms_day_on
				WHERE   SUBSTR(date_on,1,4) = $year
			");
			foreach ($range_day as $value) {
				DB::table('ms_day_on')->where('ms_day_on_id', $value->ms_day_on_id)->delete();
			}
			// add new
			if (!empty($data["data"])) {
				foreach ($data["data"] as $value) {
					$dayon = new MsDayOn;
					$dayon->date_on = $value;
					$dayon->save();
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
}