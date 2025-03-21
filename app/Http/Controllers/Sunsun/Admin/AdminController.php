<?php
namespace App\Http\Controllers\Sunsun\Admin;
use App\Http\Controllers\Controller;
use App\Models\MsUser;
use App\Models\Yoyaku;
use App\Models\YoyakuDanjikiJikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MsKubun;
use App\Models\MsHoliday;
use App\Models\Payment;
use App\Http\Controllers\Sunsun\Front\BookingController;
use Illuminate\Support\Facades\Log;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\TrNotes;
use App\Exports\SalesListExport;
use App\Models\PaymentHistory;
class AdminController extends Controller
{
	private $session_info = 'SESSION_BOOKING_USER';
	private $session_html = 'SESSION_BOOKING_DATA';
	private $payment_html = 'SESSION_PAYMENT_DATA';
	private $session_price = "SESSION_PRICE";
	private $session_price_admin = "SESSION_PRICE_ADMIN";
	public function index() {
		return view('sunsun.admin.index');
	}
	public function day(Request $request) {
		$data = [];
		if ($request->has('date') && $request->date != '') {
			$datetime = new Carbon($request->date);
			$data['date'] =  $datetime->format('Y/m/d');
			$date = $datetime->format('Ymd');
		} else {
			$time_now = Carbon::now();
			$data['date'] =  $time_now->format('Y/m/d');
			$date = $time_now->format('Ymd');
		}
		$data['date_view'] = $this->get_week_day(Carbon::parse($data['date']));
		$data['data_date'] = Yoyaku::where('service_date_start',$date)->get();
		$data['pick_up'] = $data['data_date']->where('pick_up','01');
		$data['lunch'] = $data['data_date']->where('lunch','02');
		$data['kubun'] = MsKubun::where('kubun_type', '013')->get();
		$time_value = $this->get_time_value_array($date);
		$data['time_range'] =  \Helper::getTimeAdmin($date);
		$date_obj = new Carbon($date);
		$data['week_day'] = $date_obj->dayOfWeek;
		$data['chech_date_dis'] = \Helper::CheckDayDis($data['week_day'],$date);
		// $data['time_data'] = DB::table('tr_yoyaku')
		//     ->select(['email as title', 'service_date_start as start', 'service_date_start as end'])
		//     ->whereYear('service_date_start',2019)
		//     ->whereMonth('service_date_start',11)
		//     ->get();
		$data['search'] = $this->get_search();
		//$data['search'] = [];
		$this->set_course($data, $date, $time_value);
		$this->set_stay_room($data, $date);
		$this->set_lunch($data, $date);
		$this->set_tea($data, $date);
		$this->set_pick_up($data, $date);
		$data['bookingSeclect'] = $request->has('bookingSeclect')?$request->post('bookingSeclect'):null;
		$data['timeSeclect'] = $request->has('timeSeclect')?$request->post('timeSeclect'):null;
		$payments = [];
		// $li_pays = Payment::all();
		// foreach ($li_pays as $li) {
		//     $yo = Yoyaku::where('booking_id', $li->booking_id)->first();
		//     if(isset($yo->history_id)){
		//         $payments[$yo->history_id] = $li->booking_id;
		//     }else{
		//         $payments[$li->booking_id] = $li->booking_id;
		//     }
		// }
		$li_pays = DB::select("
			SELECT  COALESCE(yo.history_id,py.booking_id) as history_id, yo.booking_id
			FROM        tr_payments py
				INNER JOIN tr_yoyaku yo
					on py.booking_id = yo.booking_id
			WHERE      service_date_start = '$date'
		");
		foreach ($li_pays as $li) {
			$payments[$li->history_id] = $li->booking_id;
		}
		//Log::debug('$payments');
		//Log::debug($payments);
		$data['payments'] = $payments;
		// get notes
		$data_notes = TrNotes::where('date_notes','=', $date)->first();
		$data["notes"] = $data_notes;
		return view('sunsun.admin.day',$data);
	}
	private function getWhere_search($course,$name) {
		// $where_plus = " AND main.service_date_start = $date ";
		// if ($course == "01" || $course == "04" || $course == "04_1")
		//     $where_plus = " AND time.service_date = $date ";
		if ($course == "02_1") $course = "02";
		else if ($course == "04_1") $course = "04";
		else if ($course == "06_1") $course = "06"; // 2020/06/05
		return "
				WHERE   main.name = '$name' and main.course = '$course'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
				AND main.fake_booking_flg IS NULL
		";
	}
	private function getForm_search($type = "") {
		$From = " FROM tr_yoyaku as main ";
		if ($type == "2") {
			$From .= " LEFT JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id ";
		}
		return $From;
	}
	private function getSelect_search($course, $type = "") {
		$select = "";
		switch ($course) {
			case '02': case '07': case '08': case '09': case '10':
				$lunch = ", main.lunch";
				$turn = ", 1 as turn";
				if ($course == "02" || $course == "10")
					$lunch = ", '02' as lunch";
				$whitening = ", main.whitening";
				if ($course == "08" || $course == "09") {
					$whitening = ", '02' as whitening";
					$turn = ",0 as turn";
				}
				$select = " SELECT  main.booking_id
						, main.ref_booking_id
						, main.repeat_user
						, main.course
						, main.gender
						, main.age_value
						, main.name
						, main.transport
						, main.bus_arrive_time_slide
						, main.pick_up
						$lunch
						, main.lunch_guest_num
						, main.tea as tea
						$whitening
						, main.whitening2
						, main.core_tuning
						, main.pet_keeping
						, main.stay_room_type
						, main.stay_guest_num
						, main.breakfast
						, main.phone
						, main.payment_method
						, main.service_date_start
						, main.service_time_1 as time
						$turn
						, SUBSTRING(main.bed, 1, 1) as bed
						, main.whitening_repeat
						, main.whitening_repeat2 ";
				break;
			case '02_1': case '07_1': case '10_1':
				$select = " SELECT  main.booking_id
						, main.ref_booking_id
						, main.repeat_user
						, main.course
						, main.gender
						, main.age_value
						, main.name
						, NULL as transport
						, NULL as bus_arrive_time_slide
						, NULL as pick_up
						, NULL as lunch
						, main.lunch_guest_num
						, NULL as tea
						, NULL as whitening
						, NULL AS whitening2
						, NULL as core_tuning
						, NULL as pet_keeping
						, NULL as stay_room_type
						, main.stay_guest_num
						, NULL as breakfast
						, NULL as phone
						, NULL as payment_method
						, main.service_date_start
						, main.service_time_2 as time
						, 2 as turn
						, SUBSTRING(main.bed, 3, 1) as bed
						, NULL as whitening_repeat
						, NULL as whitening_repeat2 ";
				break;
			case '03': case '11':
				$select = " SELECT  main.booking_id
						, main.ref_booking_id
						, main.repeat_user
						, main.course
						, '01' AS gender
						, main.age_value
						, main.name
						, main.transport
						, main.bus_arrive_time_slide
						, main.pick_up
						, main.lunch
						, main.lunch_guest_num
						, main.tea
						, main.whitening
						, main.whitening2
						, main.core_tuning
						, main.pet_keeping
						, main.stay_room_type
						, main.stay_guest_num
						, main.breakfast
						, main.phone
						, main.payment_method
						, main.service_date_start
						, main.service_time_1 as time
						, 0 as turn
						, main.bed
						, main.whitening_repeat
						, main.whitening_repeat2 ";
				break;
			case '04': case '06': // 2020/06/05
				$select = " SELECT  main.booking_id
					  , main.ref_booking_id
					  , main.repeat_user
					  , main.course
					  , main.gender
					  , main.age_value
					  , main.name
					  , main.transport
					  , main.bus_arrive_time_slide
					  , main.pick_up
					  , main.lunch
					  , main.lunch_guest_num
					  , main.tea
					  , main.whitening
					  , main.whitening2
					  , main.core_tuning
					  , main.pet_keeping
					  , main.stay_room_type
					  , main.stay_guest_num
					  , main.breakfast
					  , main.phone
					  , main.payment_method
					  , time.service_date
					  , time.service_time_1 as time
					  , 1 as turn
					  , SUBSTRING(time.notes, 1, 1) as bed
					  , main.whitening_repeat
					  , main.whitening_repeat2 ";
				break;
			case '04_1': case '06_1': // 2020/06/05
				$select = " SELECT  main.booking_id
					  , main.ref_booking_id
					  , main.repeat_user
					  , main.course
					  , main.gender
					  , main.age_value
					  , main.name
					  , NULL as transport
					  , NULL as bus_arrive_time_slide
					  , NULL as pick_up
					  , NULL as lunch
					  , main.lunch_guest_num
					  , NULL as tea
					  , NULL as whitening
					  , NULL as whitening2
					  , NULL as core_tuning
					  , NULL as pet_keeping
					  , NULL as stay_room_type
					  , main.stay_guest_num
					  , NULL as breakfast
					  , NULL as phone
					  , NULL as payment_method
					  , time.service_date
					  , time.service_time_2 as time
					  , 2 as turn
					  , SUBSTRING(time.notes, 3, 1) as bed
					  , NULL as whitening_repeat
					  , NULL as whitening_repeat2 ";
				break;
			case '05':
				$select = " SELECT  main.booking_id
						, main.ref_booking_id
						, main.repeat_user
						, main.course
						, main.gender
						, main.age_value
						, main.name
						, NULL as transport
						, NULL as bus_arrive_time_slide
						, NULL as pick_up
						, NULL as lunch
						, main.lunch_guest_num
						, NULL as tea
						, NULL as whitening
						, NULL as whitening2
						, NULL as core_tuning
						, NULL as pet_keeping
						, NULL as stay_room_type
						, main.stay_guest_num
						, NULL as breakfast
						, NULL as phone
						, NULL as payment_method
						, main.service_date_start
						, CONCAT(main.service_time_1, main.service_time_2) as time
						, 0 as turn
						, 0 as bed
						, NULL as whitening_repeat
						, NULL as whitening_repeat2 ";
				break;
			default:
				$select = " SELECT  main.booking_id
					  , main.ref_booking_id
					  , main.repeat_user
					  , main.course
					  , main.gender
					  , main.age_value
					  , main.name
					  , main.transport
					  , main.bus_arrive_time_slide
					  , main.pick_up
					  , main.lunch
					  , main.lunch_guest_num
					  , main.tea
					  , main.whitening
					  , main.whitening2
					  , main.core_tuning
					  , main.pet_keeping
					  , main.stay_room_type
					  , main.stay_guest_num
					  , main.breakfast
					  , main.phone
					  , main.payment_method
					  , time.service_date
					  , time.service_time_1 as time
					  , 0 as turn
					  , SUBSTRING(time.notes, 1, 1) as bed
					  , main.whitening_repeat
					  , main.whitening_repeat2 ";
				break;
		}
		if ($type == "1")
			$select .= " , main.fake_booking_flg ";
		return $select;
	}
	private function get_search_expert($name){
		$sel_plus1 = $this->getSelect_search("01");
		$sel_plus2 = $this->getSelect_search("02");
		$sel_plus2_1 = $this->getSelect_search("02_1");
		$sel_plus3 = $this->getSelect_search("03");
		$sel_plus11 = $this->getSelect_search("11");
		$sel_plus4 = $this->getSelect_search("04");
		$sel_plus4_1 = $this->getSelect_search("04_1");
		$sel_plus6 = $this->getSelect_search("06"); // 2020/06/05
		$sel_plus6_1 = $this->getSelect_search("06_1"); // 2020/06/05
		$sel_plus5 = $this->getSelect_search("05");
		$sel_plus7 = $this->getSelect_search("07");
		$sel_plus7_1 = $this->getSelect_search("07_1");
		$sel_plus8 = $this->getSelect_search("08");
		$sel_plus9 = $this->getSelect_search("09");
		$sel_plus10 = $this->getSelect_search("10");
		$sel_plus10_1 = $this->getSelect_search("10_1");
		$where_plus1 = $this->getWhere_search("01",$name);
		$where_plus2 = $this->getWhere_search("02",$name);
		$where_plus2_1 = $this->getWhere_search("02_1",$name);
		$where_plus3 = $this->getWhere_search("03",$name);
		$where_plus11 = $this->getWhere_search("11",$name);
		$where_plus4 = $this->getWhere_search("04",$name);
		$where_plus4_1 = $this->getWhere_search("04_1",$name);
		$where_plus6 = $this->getWhere_search("06",$name); // 2020/06/05
		$where_plus6_1 = $this->getWhere_search("06_1",$name); // 2020/06/05
		$where_plus5 = $this->getWhere_search("05",$name);
		$where_plus7 = $this->getWhere_search("07",$name);
		$where_plus7_1 = $this->getWhere_search("07_1",$name);
		$where_plus8 = $this->getWhere_search("08",$name);
		$where_plus9 = $this->getWhere_search("09",$name);
		$where_plus10 = $this->getWhere_search("10",$name);
		$where_plus10_1 = $this->getWhere_search("10_1",$name);
		$form1 = $this->getForm_search();
		$form2 = $this->getForm_search("2");
		$expert_data = "
		SELECT *
		FROM (
			(
				$sel_plus1
				$form2
				$where_plus1
			)
			UNION
			(
				$sel_plus2
				$form1
				$where_plus2
			)
			UNION
			(
				$sel_plus2_1
				$form1
				$where_plus2_1
			)
			UNION
			(
				$sel_plus3
				$form1
				$where_plus3
			)
			UNION
			(
				$sel_plus11
				$form1
				$where_plus11
			)
			UNION
			(
				$sel_plus4
				$form2
				$where_plus4
			)
			UNION
			(
				$sel_plus4_1
				$form2
				$where_plus4_1
			)
			UNION
			(
				$sel_plus6
				$form2
				$where_plus6
			)
			UNION
			(
				$sel_plus6_1
				$form2
				$where_plus6_1
			)
			UNION
			(
				$sel_plus5
				$form1
				$where_plus5
			)
			UNION
			(
				$sel_plus7
				$form1
				$where_plus7
			)
			UNION
			(
				$sel_plus7_1
				$form1
				$where_plus7_1
			)
			UNION
			(
				$sel_plus8
				$form1
				$where_plus8
			)
			UNION
			(
				$sel_plus9
				$form1
				$where_plus9
			)
			UNION
			(
				$sel_plus10
				$form1
				$where_plus10
			)
			UNION
			(
				$sel_plus10_1
				$form1
				$where_plus10_1
			)
		) temp_table
		ORDER BY service_date DESC , time ASC
		"; // 2020/06/05
		//Log::debug('$expert_data');
		Log::debug($expert_data);
		return DB::select($expert_data);
	}
	private function get_search(){
		$all_data = DB::select("
		SELECT distinct main.name
		FROM        tr_yoyaku main
		WHERE       main.history_id IS NULL
		AND         main.name IS NOT NULL
		AND         main.ref_booking_id IS NULL
		AND         main.fake_booking_flg IS NULL
		AND         main.del_flg IS NULL
		");
		// AND         main.service_date_start = $date
		for($i = 0; $i < count($all_data); $i++){
			//Log::debug($all_data[$i]->booking_id);
			// $arr_booking_id = explode(",",$all_data[$i]->booking_id);
			$all_data[$i]->expert_data = "";
		}
		//Log::debug($all_data);
		return  collect($all_data);
	}
	private function set_stay_room(&$data, $date){
		$stay_room_raw1 = DB::select("
				SELECT DB1.name as name,
					ms_kubun.kubun_id as stay_room_type,
					DB1.stay_guest_num as stay_guest_num,
					DB1.breakfast as breakfast
				FROM
					ms_kubun
					INNER JOIN
						(   SELECT  main.name,
								main.stay_room_type,
								main.stay_guest_num,
								main.breakfast
							FROM        tr_yoyaku as main
							WHERE   main.stay_room_type <> '01'
								AND main.stay_room_type IS NOT NULL
								AND main.stay_checkin_date < $date
								AND main.stay_checkout_date >= $date
								AND main.history_id IS NULL
								AND main.fake_booking_flg IS NULL
								AND main.del_flg IS NULL
						) DB1 ON ms_kubun.kubun_id = DB1.stay_room_type
				where ms_kubun.kubun_type = '011' and ms_kubun.kubun_id <> '01' and ms_kubun.kubun_id <> '03'
		");
		$stay_room_raw2 = DB::select("
				SELECT DB2.name as name,
					ms_kubun.kubun_id as stay_room_type,
					DB2.stay_guest_num as stay_guest_num
				FROM
					ms_kubun
					INNER JOIN
						(   SELECT  main.name,
								main.stay_room_type,
								main.stay_guest_num
							FROM        tr_yoyaku as main
							WHERE   main.stay_room_type <> '01'
								AND main.stay_room_type IS NOT NULL
								AND main.stay_checkin_date <= $date
								AND main.stay_checkout_date > $date
								AND main.history_id IS NULL
								AND main.fake_booking_flg IS NULL
								AND main.del_flg IS NULL
					) DB2 ON ms_kubun.kubun_id = DB2.stay_room_type
				where ms_kubun.kubun_type = '011' and ms_kubun.kubun_id <> '01' and ms_kubun.kubun_id <> '03'
		");
		for($i = 0; $i < count($stay_room_raw1); $i++){
			if (!empty($stay_room_raw1[$i]->stay_guest_num)) {
				$stay_room = MsKubun::where('kubun_type','012')->where('kubun_id', $stay_room_raw1[$i]->stay_guest_num)->first();
				$stay_room_raw1[$i]->stay_guest_num = $stay_room->kubun_value;
			}
			if($stay_room_raw1[$i]->breakfast == '02'){
				$stay_room_raw1[$i]->breakfast = preg_replace('/[^0-9]+/', '', $stay_room_raw1[$i]->stay_guest_num);
			}else{
				$stay_room_raw1[$i]->breakfast = NULL;
			}
		}
		for($i = 0; $i < count($stay_room_raw2); $i++){
			if (!empty($stay_room_raw2[$i]->stay_guest_num)) {
				$stay_room = MsKubun::where('kubun_type','012')->where('kubun_id', $stay_room_raw2[$i]->stay_guest_num)->first();
				$stay_room_raw2[$i]->stay_guest_num = $stay_room->kubun_value;
			}
		}
		$collect_stay_room1 = collect($stay_room_raw1);
		$collect_stay_room2 = collect($stay_room_raw2);
		$data['stay_room']['A_break'] =  $collect_stay_room1->firstWhere('stay_room_type', '02');
		$data['stay_room']['B_break'] =  $collect_stay_room1->firstWhere('stay_room_type', '03');
		$data['stay_room']['C_break'] =  $collect_stay_room1->firstWhere('stay_room_type', '04');
		$data['stay_room']['A'] =  $collect_stay_room2->firstWhere('stay_room_type', '02');
		$data['stay_room']['B'] =  $collect_stay_room2->firstWhere('stay_room_type', '03');
		$data['stay_room']['C'] =  $collect_stay_room2->firstWhere('stay_room_type', '04');
	}
	private function set_lunch(&$data, $date){
		$data['lunch'] = DB::select("
		(
			SELECT  main.name,
					main.lunch,
					main.ref_booking_id,
					main.lunch_guest_num
			FROM    tr_yoyaku as main
			WHERE   (main.lunch <> '01' OR main.lunch_guest_num <> '01')
			AND main.service_date_start = $date
			AND main.history_id IS NULL
			AND main.fake_booking_flg IS NULL
			AND main.del_flg IS NULL
		)
		UNION ALL
		(
			SELECT  main.name,
					'02' AS lunch,
					main.ref_booking_id,
					main.lunch_guest_num
			FROM    tr_yoyaku as main
			WHERE   main.course in ('02','10')
			AND main.service_date_start = $date
			AND main.history_id IS NULL
			AND main.fake_booking_flg IS NULL
			AND main.del_flg IS NULL
		)
		");
		for($i = 0; $i < count($data['lunch']); $i++){
			if($data['lunch'][$i]->lunch_guest_num != NULL){
				$lunch_guest_num = MsKubun::where('kubun_type','023')->where('kubun_id', $data['lunch'][$i]->lunch_guest_num)->first();
				$data['lunch'][$i]->lunch = preg_replace('/[^0-9]+/', '', $lunch_guest_num->kubun_value);
			}
		}
		// dd($data['lunch']);
	}
	private function set_tea(&$data, $date){
		$data['tea'] = DB::select("
		(
			SELECT  main.name,
					main.tea
			FROM    tr_yoyaku as main
			WHERE   main.tea = 1
			AND main.service_date_start = $date
			AND main.history_id IS NULL
			AND main.fake_booking_flg IS NULL
			AND main.del_flg IS NULL
		)
		");
	}
	private function set_pick_up(&$data, $date){
		$data['pick_up'] = DB::select("
		SELECT  main.booking_id,
				main.bus_arrive_time_slide,
				main.name,
				main.service_guest_num,
				cou.num_user
		FROM    tr_yoyaku main
		LEFT JOIN (
		SELECT  tr_yoyaku.ref_booking_id,
				COUNT(*) as num_user
		FROM    tr_yoyaku
		WHERE tr_yoyaku.ref_booking_id IS NOT NULL
		GROUP BY tr_yoyaku.ref_booking_id
		) cou ON cou.ref_booking_id = main.booking_id
		WHERE main.transport = '02'
		AND main.ref_booking_id IS NULL
		AND main.pick_up = '01'
		AND main.service_date_start = $date
		AND main.history_id IS NULL
		AND main.fake_booking_flg IS NULL
		AND main.del_flg IS NULL
		ORDER BY main.bus_arrive_time_slide
		");
		for($i = 0; $i < count($data['pick_up']); $i++){
			$bus_slide = MsKubun::where('kubun_type','003')->where('kubun_id', $data['pick_up'][$i]->bus_arrive_time_slide)->first();
			$data['pick_up'][$i]->bus_arrive_time_slide = ltrim(explode("着", $bus_slide->kubun_value)[0], '0');
			if($data['pick_up'][$i]->service_guest_num != NULL){
				$service_guest_num = MsKubun::where('kubun_type','015')->where('kubun_id', $data['pick_up'][$i]->service_guest_num)->first();
				$data['pick_up'][$i]->num_user = preg_replace('/[^0-9]+/', '', $service_guest_num->kubun_value);
			}
		}
	}
	private function getWherePlus_course($date,$type="1") {
		$where = " AND main.history_id IS NULL AND main.del_flg IS NULL ";
		if ($type == "1")
			$where .= " AND main.service_date_start = $date ";
		else if ($type == "2")
			$where .= " AND time.service_date = $date ";
		return $where;
	}
	private function set_course(&$data, $date, $time_value){
		$sel_plus1 = $this->getSelect_search("01","1");
		$sel_plus2 = $this->getSelect_search("02","1");
		$sel_plus2_1 = $this->getSelect_search("02_1","1");
		$sel_plus3 = $this->getSelect_search("03","1");
		$sel_plus11 = $this->getSelect_search("11","1");
		$sel_plus4 = $this->getSelect_search("04","1");
		$sel_plus4_1 = $this->getSelect_search("04_1","1");
		$sel_plus6 = $this->getSelect_search("06","1"); // 2020/06/05
		$sel_plus6_1 = $this->getSelect_search("06_1","1"); // 2020/06/05
		$sel_plus7 = $this->getSelect_search("07","1");
		$sel_plus7_1 = $this->getSelect_search("07_1","1");
		$sel_plus8 = $this->getSelect_search("08","1");
		$sel_plus9 = $this->getSelect_search("09","1");
		$sel_plus10 = $this->getSelect_search("10","1");
		$sel_plus10_1 = $this->getSelect_search("10_1","1");
		$from1 = $this->getForm_search();
		$from2 = $this->getForm_search("2");
		$where1 = $this->getWherePlus_course($date);
		$where2 = $this->getWherePlus_course($date,"2");
		$course_1_to_4_query = DB::select("
		(
			$sel_plus1
			$from2
			WHERE   main.course = '01'
			$where2
		)
		UNION
		(
			$sel_plus2
			$from1
			WHERE   main.course = '02'
			$where1
		)
		UNION
		(
			$sel_plus2_1
			$from1
			WHERE   main.course = '02'
			$where1
		)
		UNION
		(
			$sel_plus3
			$from1
			WHERE   main.course = '03'
			$where1
		)
		UNION
		(
			$sel_plus11
			$from1
			WHERE   main.course = '11'
			$where1
		)
		UNION
		(
			$sel_plus4
			$from2
			WHERE   main.course = '04'
			$where2
		)
		UNION
		(
			$sel_plus4_1
			$from2
			WHERE   main.course = '04'
			$where2
		)
		UNION
		(
			$sel_plus6
			$from2
			WHERE   main.course = '06'
			$where2
		)
		UNION
		(
			$sel_plus6_1
			$from2
			WHERE   main.course = '06'
			$where2
		)
		UNION
		(
			$sel_plus7
			$from1
			WHERE   main.course = '07'
			$where1
		)
		UNION
		(
			$sel_plus7_1
			$from1
			WHERE   main.course = '07'
			$where1
		)
		UNION
		(
			$sel_plus8
			$from1
			WHERE   main.course = '08'
			$where1
		)
		UNION
		(
			$sel_plus9
			$from1
			WHERE   main.course = '09'
			$where1
		)
		UNION
		(
			$sel_plus10
			$from1
			WHERE   main.course = '10'
			$where1
		)
		UNION
		(
			$sel_plus10_1
			$from1
			WHERE   main.course = '10'
			$where1
		)
		"); // 2020/06/05
		$course_1_to_4 = collect($course_1_to_4_query);
		$course_1_unique_id = $course_1_to_4->where('course', '01')->unique('booking_id');
		foreach($course_1_unique_id as $c_1_u_id){
			$temp_set_turn = $course_1_to_4->where('booking_id', $c_1_u_id->booking_id);
			$turn = 1;
			foreach($temp_set_turn as $temp){
				if($turn == 1){
					$temp->turn = 0;
				}else{
					$temp->turn = $turn;
					$temp->transport = NULL;
					$temp->bus_arrive_time_slide = NULL;
					$temp->pick_up = NULL;
					$temp->lunch = NULL;
					$temp->tea = NULL;
					$temp->whitening = NULL;
					$temp->whitening2 = NULL;
					$temp->core_tuning = NULL;
					$temp->pet_keeping = NULL;
					$temp->stay_room_type = NULL;
					$temp->breakfast = NULL;
					$temp->phone = NULL;
					$temp->payment_method = NULL;
					$temp->whitening_repeat = NULL;
					$temp->whitening_repeat2 = NULL;
				}
				$turn++;
			}
		}
		for($i = 0; $i < count($course_1_to_4); $i++){
			switch($course_1_to_4[$i]->course){
				case '01': $course_1_to_4[$i]->course = config('const.text_simple.c01'); break;
				case '02': $course_1_to_4[$i]->course = config('const.text_simple.c02'); break;
				case '03': $course_1_to_4[$i]->course = config('const.text_simple.c03'); break;
				case '11': $course_1_to_4[$i]->course = config('const.text_simple.c11'); break;
				case '04': $course_1_to_4[$i]->course = config('const.text_simple.c04'); break;
				case '06': $course_1_to_4[$i]->course = config('const.text_simple.c06'); break;
				case '07': $course_1_to_4[$i]->course = config('const.text_simple.c07'); break;
				case '08': $course_1_to_4[$i]->course = config('const.text_simple.c08'); break;
				case '09': $course_1_to_4[$i]->course = config('const.text_simple.c09'); break;
				case '10': $course_1_to_4[$i]->course = config('const.text_simple.c10'); break;
			}
			switch($course_1_to_4[$i]->repeat_user){
				case '01': $course_1_to_4[$i]->repeat_user = config('const.text_simple.repeat'); break;
				case '02': $course_1_to_4[$i]->repeat_user = NULL; break;
			}
			switch($course_1_to_4[$i]->gender){
				case '01': $course_1_to_4[$i]->gender = '男性'; break;
				case '02': $course_1_to_4[$i]->gender = '女性'; break;
			}
			switch($course_1_to_4[$i]->transport){
				case '01': {
					$course_1_to_4[$i]->transport = '車';
					$course_1_to_4[$i]->bus_arrive_time_slide = NULL;
					$course_1_to_4[$i]->pick_up = NULL;
					break;
				}
				case '02': {
					$course_1_to_4[$i]->transport = 'バス';
					$bus_slide = MsKubun::where('kubun_type','003')->where('kubun_id',$course_1_to_4[$i]->bus_arrive_time_slide)->first();
					$course_1_to_4[$i]->bus_arrive_time_slide = ltrim(explode("着", $bus_slide->kubun_value)[0], '0')."着";
					switch($course_1_to_4[$i]->pick_up){
						case '01': $course_1_to_4[$i]->pick_up = '送迎有'; break;
						case '02': $course_1_to_4[$i]->pick_up = NULL; break;
					}
					break;
				}
			}
			if(((isset($course_1_to_4[$i]->lunch)) && ($course_1_to_4[$i]->lunch != '01')) || ((isset($course_1_to_4[$i]->lunch_guest_num)) && ($course_1_to_4[$i]->lunch_guest_num != '01'))){
				$course_1_to_4[$i]->lunch = config('const.text_simple.lunch');
			}else{
				$course_1_to_4[$i]->lunch = NULL;
			}
			switch($course_1_to_4[$i]->tea){
				case '1': $course_1_to_4[$i]->tea = config('const.text_simple.tea'); break;
				default: $course_1_to_4[$i]->tea = NULL; break;
			}
			switch($course_1_to_4[$i]->whitening){
				case '01': $course_1_to_4[$i]->whitening = NULL; break;
				case '02': $course_1_to_4[$i]->whitening = config('const.text_simple.new_scan'); break;
			}
			switch($course_1_to_4[$i]->whitening2){
				case '01': $course_1_to_4[$i]->whitening2 = NULL; break;
				case '02': $course_1_to_4[$i]->whitening2 = config('const.text_simple.whitening'); break;
			}
			switch($course_1_to_4[$i]->core_tuning){
				case '01': $course_1_to_4[$i]->core_tuning = NULL; break;
				case '02': $course_1_to_4[$i]->core_tuning = config('const.text_simple.core_tuning'); break;
			}
			switch($course_1_to_4[$i]->pet_keeping){
				case '01': $course_1_to_4[$i]->pet_keeping = NULL; break;
				case '02': $course_1_to_4[$i]->pet_keeping = 'Pet'; break;
			}
			switch($course_1_to_4[$i]->stay_room_type){
				case '01': $course_1_to_4[$i]->stay_room_type = NULL; break;
				// case '02': {
				//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$course_1_to_4[$i]->stay_room_type)->first();
				//     $course_1_to_4[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
				//     break;
				// }
				// case '03': {
				//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$course_1_to_4[$i]->stay_room_type)->first();
				//     $course_1_to_4[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
				//     break;
				// }
				// case '04': {
				//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$course_1_to_4[$i]->stay_room_type)->first();
				//     $course_1_to_4[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
				//     break;
				// }
			}
			switch($course_1_to_4[$i]->stay_guest_num){
				case '01': {
					$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$course_1_to_4[$i]->stay_guest_num)->first();
					$course_1_to_4[$i]->stay_guest_num =  $temp_kubun->notes;
					break;
				}
				case '02': {
					$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$course_1_to_4[$i]->stay_guest_num)->first();
					$course_1_to_4[$i]->stay_guest_num =  $temp_kubun->notes;
					break;
				}
				case '03': {
					$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$course_1_to_4[$i]->stay_guest_num)->first();
					$course_1_to_4[$i]->stay_guest_num =  $temp_kubun->notes;
					break;
				}
			}
			switch($course_1_to_4[$i]->breakfast){
				case '01': $course_1_to_4[$i]->breakfast = NULL; break;
				case '02': $course_1_to_4[$i]->breakfast = '朝食有'; break;
			}
			switch($course_1_to_4[$i]->payment_method){
				case '1': $course_1_to_4[$i]->payment_method = 'クレカ'; break;
				case '2': $course_1_to_4[$i]->payment_method = '現金'; break;
				case '3': $course_1_to_4[$i]->payment_method = '回数券'; break;
			}
		}
		$course_5_query = DB::select("
			SELECT  main.booking_id
					, main.ref_booking_id
					, main.repeat_user
					, main.course
					, main.age_value
					, main.name
					, main.transport
					, main.bus_arrive_time_slide
					, main.pick_up
					, main.service_pet_num
					, main.notes
					, main.phone
					, main.payment_method
					, main.service_date_start
					,CONCAT(main.service_time_1, '-', main.service_time_2) as time
			$from1
			WHERE   main.course = '05'
			$where1
		");
		$course_5 = collect($course_5_query);
		for($i = 0; $i < count($course_5); $i++){
			switch($course_5[$i]->transport){
				case '01': {
					$course_5[$i]->transport = '車';
					$course_5[$i]->bus_arrive_time_slide = NULL;
					$course_5[$i]->pick_up = NULL;
					break;
				}
				case '02': {
					$course_5[$i]->transport = 'バス';
					$bus_slide = MsKubun::where('kubun_type','003')->where('kubun_id',$course_5[$i]->bus_arrive_time_slide)->first();
					$course_5[$i]->bus_arrive_time_slide = ltrim(explode("着", $bus_slide->kubun_value)[0], '0')."着";
					switch($course_5[$i]->pick_up){
						case '01': $course_5[$i]->pick_up = '送迎有'; break;
						case '02': $course_5[$i]->pick_up = NULL; break;
					}
					break;
				}
			}
			switch($course_5[$i]->repeat_user){
				case '01': $course_5[$i]->repeat_user = config('const.text_simple.repeat'); break;
				case '02': $course_5[$i]->repeat_user = NULL; break;
			}
			switch($course_5[$i]->payment_method){
				case '1': $course_5[$i]->payment_method = 'クレカ'; break;
				case '2': $course_5[$i]->payment_method = '現金'; break;
				case '3': $course_5[$i]->payment_method = '回数券'; break;
			}
			switch($course_5[$i]->service_pet_num){
				case '01': $course_5[$i]->service_pet_num = 1; break;
				case '02': $course_5[$i]->service_pet_num = 2; break;
				case '03': $course_5[$i]->service_pet_num = 3; break;
			}
		}
		$course_wt_query = DB::select("
			SELECT  main.booking_id
					, main.ref_booking_id
					, main.repeat_user
					, main.course
					, main.gender
					, main.age_value
					, main.name
					, main.transport
					, main.bus_arrive_time_slide
					, main.pick_up
					, main.phone
					, main.payment_method
					, main.service_date_start
					, main.whitening_time as time
			$from1
			WHERE   1=1
			$where1
		");
		$course_wt = collect($course_wt_query);
		for($i = 0; $i < count($course_wt); $i++){
			switch($course_wt[$i]->transport){
				case '01': {
					$course_wt[$i]->transport = '車';
					$course_wt[$i]->bus_arrive_time_slide = NULL;
					$course_wt[$i]->pick_up = NULL;
					break;
				}
				case '02': {
					$course_wt[$i]->transport = 'バス';
					$bus_slide = MsKubun::where('kubun_type','003')->where('kubun_id',$course_wt[$i]->bus_arrive_time_slide)->first();
					$course_wt[$i]->bus_arrive_time_slide = ltrim(explode("着", $bus_slide->kubun_value)[0], '0')."着";
					switch($course_wt[$i]->pick_up){
						case '01': $course_wt[$i]->pick_up = '送迎有'; break;
						case '02': $course_wt[$i]->pick_up = NULL; break;
					}
					break;
				}
			}
			switch($course_wt[$i]->course){
				case '01': $course_wt[$i]->course = config('const.text_simple.c01'); break;
				case '02': $course_wt[$i]->course = config('const.text_simple.c02'); break;
				case '03': $course_wt[$i]->course = config('const.text_simple.c03'); break;
				case '11': $course_wt[$i]->course = config('const.text_simple.c11'); break;
				case '04': $course_wt[$i]->course = config('const.text_simple.c04'); break;
				case '06': $course_wt[$i]->course = config('const.text_simple.c06'); break;
				case '07': $course_wt[$i]->course = config('const.text_simple.c07'); break;
				case '08': $course_wt[$i]->course = config('const.text_simple.c08'); break;
				case '09': $course_wt[$i]->course = config('const.text_simple.c09'); break;
				case '10': $course_wt[$i]->course = config('const.text_simple.c10'); break;
			}
			switch($course_wt[$i]->repeat_user){
				case '01': $course_wt[$i]->repeat_user = config('const.text_simple.repeat'); break;
				case '02': $course_wt[$i]->repeat_user = NULL; break;
			}
			switch($course_wt[$i]->payment_method){
				case '1': $course_wt[$i]->payment_method = 'クレカ'; break;
				case '2': $course_wt[$i]->payment_method = '現金'; break;
				case '3': $course_wt[$i]->payment_method = '回数券'; break;
			}
			switch($course_wt[$i]->gender){
				case '01': $course_wt[$i]->gender = '男性'; break;
				case '02': $course_wt[$i]->gender = '女性'; break;
			}
		}
		for($i = 0; $i < count($time_value) ; $i++){
			$data['time_range'][$i]['data']['male_1'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '男性')->firstWhere('bed', '1');
			$data['time_range'][$i]['data']['male_2'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '男性')->firstWhere('bed', '2');
			$data['time_range'][$i]['data']['male_3'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '男性')->firstWhere('bed', '3');
			$data['time_range'][$i]['data']['female_1'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '女性')->firstWhere('bed', '1');
			$data['time_range'][$i]['data']['female_2'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '女性')->firstWhere('bed', '2');
			$data['time_range'][$i]['data']['female_3'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '女性')->firstWhere('bed', '3');
			$data['time_range'][$i]['data']['female_4'] = $course_1_to_4->where('time',  $data['time_range'][$i]['time_value'])->where('gender', '女性')->firstWhere('bed', '4');
			$data['time_range'][$i]['data']['pet'] = $course_5->firstWhere('time',   $data['time_range'][$i]['pet_time_value']);
			$data['time_range'][$i]['data']['wt'] = $course_wt->firstWhere('time',   $data['time_range'][$i]['wt_time_value']);
		}
		// dd($data['time_range']);
	}
	private function get_time_value_array($date){
		$time_range = \Helper::getTimeAdmin($date);
		$time_value = [];
		foreach($time_range as $time){
			$time_value[] = $time['time_value'];
		}
		return $time_value;
	}
	public function edit_booking (Request $request) {
		$data = $request->all();
		$booking = new BookingController();
		$booking->fetch_kubun_data($data);
		if(isset($data['booking_id']) &&($data['booking_id'] != 0)){
			$data['data_booking'] = Yoyaku::where('booking_id', $data['booking_id'])->first();
			$data['data_time'] = YoyakuDanjikiJikan::where('booking_id', $data['booking_id'])->get();
			$data['history_booking'] = Yoyaku::where('history_id', $data['booking_id'])->orderBy('booking_id', 'DESC')->get();
		}else{
			$data['data_booking'] = [];
			$data['data_time'] =  [];
			$data['booking_id'] = NULL;
		}
		// dd($data);
		return view('sunsun.admin.parts.booking',$data)->render();
	}
	// public function booking_history(Request $request){
	//     $data = $request->all();
	//     $booking = new BookingController();
	//     $booking->fetch_kubun_data($data);
	//     $data['data_booking'] = Yoyaku::where('booking_id', $data['course_history'])->first();
	//     $data['data_time'] = YoyakuDanjikiJikan::where('booking_id', $data['course_history'])->get();
	//     $data['history_booking'] = Yoyaku::where('history_id', $data['current_booking_id'])->orderBy('booking_id', 'DESC')->get();
	//     $data['booking_id'] = $data['current_booking_id'];
	//     // dd($data['data_booking']->booking_id);
	//     return view('sunsun.admin.parts.booking',$data)->render();
	// }
		public function show_history(Request $request){
			$data = $request->all();
			// $booking = new BookingController();
			// $booking->fetch_kubun_data($data);
			// $data['data_booking'] = Yoyaku::where('booking_id', $data['booking_id'])->first();
			$data['data_time'] = YoyakuDanjikiJikan::where('booking_id', $data['booking_id'])->get();
			$history_booking = Yoyaku::where('history_id', $data['booking_id'])->whereNull('fake_booking_flg')->whereNull('del_flg')->orderBy('booking_id', 'DESC')->get();
			$history_booking = collect($history_booking);
			for($i = 0; $i < count($history_booking); $i++){
				switch($history_booking[$i]->course){
					case '01': $history_booking[$i]->course = config('const.text_simple.c01'); break;
					case '02': $history_booking[$i]->course = config('const.text_simple.c02'); break;
					case '03': $history_booking[$i]->course = config('const.text_simple.c03'); break;
					case '11': $history_booking[$i]->course = config('const.text_simple.c11'); break;
					case '04': $history_booking[$i]->course = config('const.text_simple.c04'); break;
					case '06': $history_booking[$i]->course = config('const.text_simple.c06'); break;
					case '07': $history_booking[$i]->course = config('const.text_simple.c07'); break;
					case '08': $history_booking[$i]->course = config('const.text_simple.c08'); break;
					case '09': $history_booking[$i]->course = config('const.text_simple.c09'); break;
					case '10': $history_booking[$i]->course = config('const.text_simple.c10'); break;
				}
				switch($history_booking[$i]->repeat_user){
					case '01': $history_booking[$i]->repeat_user = config('const.text_simple.repeat'); break;
					case '02': $history_booking[$i]->repeat_user = NULL; break;
				}
				switch($history_booking[$i]->gender){
					case '01': $history_booking[$i]->gender = '男性'; break;
					case '02': $history_booking[$i]->gender = '女性'; break;
				}
				switch($history_booking[$i]->transport){
					case '01': {
						$history_booking[$i]->transport = '車';
						$history_booking[$i]->bus_arrive_time_slide = NULL;
						$history_booking[$i]->pick_up = NULL;
						break;
					}
					case '02': {
						$history_booking[$i]->transport = 'バス';
						$bus_slide = MsKubun::where('kubun_type','003')->where('kubun_id',$history_booking[$i]->bus_arrive_time_slide)->first();
						$history_booking[$i]->bus_arrive_time_slide = ltrim(explode("着", $bus_slide->kubun_value)[0], '0')."着";
						switch($history_booking[$i]->pick_up){
							case '01': $history_booking[$i]->pick_up = '送迎有'; break;
							case '02': $history_booking[$i]->pick_up = NULL; break;
						}
						break;
					}
				}
				if(((isset($history_booking[$i]->lunch)) && ($history_booking[$i]->lunch != '01')) || ((isset($history_booking[$i]->lunch_guest_num)) && ($history_booking[$i]->lunch_guest_num != '01'))){
					$history_booking[$i]->lunch = config('const.text_simple.lunch');
				}else{
					$history_booking[$i]->lunch = NULL;
				}
				switch($history_booking[$i]->tea){
					case '1': $history_booking[$i]->tea = config('const.text_simple.tea'); break;
					default: $history_booking[$i]->tea = NULL; break;
				}
				switch($history_booking[$i]->whitening){
					case '01': $history_booking[$i]->whitening = NULL; break;
					case '02': $history_booking[$i]->whitening = config('const.text_simple.new_scan'); break;
				}
				switch($history_booking[$i]->whitening2){
					case '01': $history_booking[$i]->whitening2 = NULL; break;
					case '02': $history_booking[$i]->whitening2 = config('const.text_simple.whitening'); break;
				}
				switch($history_booking[$i]->core_tuning){
					case '01': $history_booking[$i]->core_tuning = NULL; break;
					case '02': $history_booking[$i]->core_tuning = config('const.text_simple.core_tuning'); break;
				}
				switch($history_booking[$i]->pet_keeping){
					case '01': $history_booking[$i]->pet_keeping = NULL; break;
					case '02': $history_booking[$i]->pet_keeping = 'Pet'; break;
				}
				switch($history_booking[$i]->stay_room_type){
					case '01': $history_booking[$i]->stay_room_type = NULL; break;
					// case '02': {
					//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$history_booking[$i]->stay_room_type)->first();
					//     $history_booking[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
					//     break;
					// }
					// case '03': {
					//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$history_booking[$i]->stay_room_type)->first();
					//     $history_booking[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
					//     break;
					// }
					// case '04': {
					//     $temp_kubun = MsKubun::where('kubun_type','011')->where('kubun_id',$history_booking[$i]->stay_room_type)->first();
					//     $history_booking[$i]->stay_room_type =  substr($temp_kubun->kubun_value, 0, 1);
					//     break;
					// }
				}
				switch($history_booking[$i]->stay_guest_num){
					case '01': {
						$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$history_booking[$i]->stay_guest_num)->first();
						$history_booking[$i]->stay_guest_num =  $temp_kubun->notes;
						break;
					}
					case '02': {
						$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$history_booking[$i]->stay_guest_num)->first();
						$history_booking[$i]->stay_guest_num =  $temp_kubun->notes;
						break;
					}
					case '03': {
						$temp_kubun = MsKubun::where('kubun_type','012')->where('kubun_id',$history_booking[$i]->stay_guest_num)->first();
						$history_booking[$i]->stay_guest_num =  $temp_kubun->notes;
						break;
					}
				}
				switch($history_booking[$i]->breakfast){
					case '01': $history_booking[$i]->breakfast = NULL; break;
					case '02': $history_booking[$i]->breakfast = '朝食有'; break;
				}
				switch($history_booking[$i]->payment_method){
					case '1': $history_booking[$i]->payment_method = 'クレカ'; break;
					case '2': $history_booking[$i]->payment_method = '現金'; break;
					case '3': $history_booking[$i]->payment_method = '回数券'; break;
				}
			}
			$data['history_booking'] =  $history_booking;
			// dd($data);
			// dd($data['data_booking']->booking_id);
			return view('sunsun.admin.parts.history',$data)->render();
		}
	private function add_fake_course(&$customer_info, $data){
		$course = json_decode($data['course'], true);
		if($course['kubun_id'] == '03' || $course['kubun_id'] == '11'){
			$data['fake_booking'] = 1;
			$temp_json = json_decode($data['time'][0]['json'], true);
			// 2 ô trống đầu tiên
			$data['time_room_bed'] = 2;
			$temp_json['kubun_id_room'] = '02';
			$temp_json['kubun_value_room'] = '2';
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			$data['time_room_bed'] = 3;
			$temp_json['kubun_id_room'] = '03';
			$temp_json['kubun_value_room'] = '3';
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			// 6 ô trống phía dưới
			$time_row2 = "";
			$time_row3 = "";
			if($temp_json['notes'] == '0945'){
				$time_row2 = '1015';
				$time_row3 = '1045';
			}else if($temp_json['notes'] == '1315'){
				$time_row2 = '1345';
				$time_row3 = '1415';
			}else if($temp_json['notes'] == '1515'){
				$time_row2 = '1545';
				$time_row3 = '1615';
			}
			// row 2
			$temp_json['notes'] = $time_row2;
			$data['time_room_value'] = $time_row2;
			$data['time_room_bed'] = 1;
			$temp_json['kubun_id_room'] = '01';
			$temp_json['kubun_value_room'] = '1';
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			//row 2
			$temp_json['notes'] = $time_row2;
			$data['time_room_value'] = $time_row2;
			$data['time_room_bed'] = 2;
			$temp_json['kubun_id_room'] = '02';
			$temp_json['kubun_value_room'] = '2';
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			//row 2
			$temp_json['notes'] = $time_row2;
			$data['time_room_value'] = $time_row2;
			$data['time_room_bed'] = 3;
			$temp_json['kubun_id_room'] = '03';
			$temp_json['kubun_value_room'] = '3';
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			array_push($customer_info, $data);
		}
	}
	public function update_booking (Request $request){
		$data = $request->all();
		$send_mail = false;
		if(isset($data['send_email']) && $data['send_email'] == "on"){
			$send_mail = true;
		}
		$booking = new BookingController();
		$booking_id = isset($data['booking_id'])?$data['booking_id']:0;
		$validate_booking = $booking->validate_booking($data, $booking_id);
		$validate_payment = $booking->validate_payment_info($data, $booking_id);
		if((isset($validate_payment['error'])) || (isset($validate_booking) && (count($validate_booking) != 0))){
			return [
				'status' => false,
				'type' => 'validate',
				'message' => [
					'booking' => $validate_booking,
					'payment' => $validate_payment
				]
			];
		}else{
			$data['customer'] = $data;
			$data['customer']['info'] = ['0' => $data];
			$this->add_fake_course($data['customer']['info'], $data);
			// $booking->make_bill($data);
			// $request->session()->put($this->payment_html, view('sunsun.front.payment',$data)->render());
			// $request->session()->put($this->session_html, view('sunsun.front.confirm',$data)->render());
		}
		$admin_price = $this->admin_get_price($request, $booking_id);
		$request->session()->put($this->session_price_admin, $admin_price);
		$ref_booking_id = isset($data['ref_booking_id'])?$data['ref_booking_id']:null;
		$booking->update_or_new_booking($data, $request, true, $ref_booking_id, $send_mail);
		return [
			'status' => true,
			'type' => 'update',
			'message' => null
		];
	}
	private function admin_get_price($request, $booking_id){
		$booking = new BookingController();
		$admin_price = 0;
		$bill_text = null;
		$yo = Yoyaku::where('booking_id', $booking_id)->first();
		$list_booking = null;
		$admin_customer = [];
		if(isset($yo->ref_booking_id)){
			$yo_temp = Yoyaku::where('booking_id', $yo->ref_booking_id)->first();
			$admin_customer[] = $yo_temp;
			$list_booking = Yoyaku::where('ref_booking_id', $yo_temp->booking_id)->whereNull('history_id')->whereNull('del_flg')->get();
		}else{
			$admin_customer[] = $yo;
			$list_booking = Yoyaku::where('ref_booking_id', $booking_id)->whereNull('history_id')->whereNull('del_flg')->get();
		}
		foreach ($list_booking as $key => $li_bo) {
			$admin_customer[] = $li_bo;
		}
		// $booking->yoyaku_2_bill($request, $admin_customer, $bill_text, $admin_price);
		//Log::debug('$admin_price');
		//Log::debug($admin_price);
		return $admin_price;
	}
	public function delete_booking(Request $request) {
		$data = $request->all();
		// dd($data);
		$booking_id = isset($data['booking_id'])?$data['booking_id']:null;
		$ref_booking_id = isset($data['ref_booking_id'])?$data['ref_booking_id']:null;
		if($booking_id === null){
			return [
				'status' => false
			];
		}
		try {
			$current_booking = Yoyaku::where('booking_id',$booking_id);
			if($current_booking){
				$current_booking->update(['del_flg' => '1']);
			}
			if($ref_booking_id === null){
				$ref_booking = Yoyaku::where('ref_booking_id',$booking_id);
				if($ref_booking){
					$ref_booking->update(['del_flg' => '1']);
				}
			} else {
				// create new payments_history
				$parent_booking = Yoyaku::where('booking_id',$booking_id)->get();
				foreach ($parent_booking as $row) {
					PaymentHistory::where('booking_id',$row->ref_booking_id)->delete();
					$booking = new BookingController();
					$booking->save_tr_payments_history($row->ref_booking_id);
					break;
				}
			}
			return [
				'status' => true
			];
		} catch (\Exception $e) {
			return [
				'status' => false
			];
		}
	}
	public function weekly(Request $request) {
		$data = [];
		if ($request->has('date_from') && $request->has('date_to') && $request->date_from != '' && $request->date_to != '') {
			$date_from = new Carbon($request->date_from);
			$date_to = new Carbon($request->date_to);
			$data['date_from'] =  $date_from->format('Y/m/d');
			$data['date_to'] =  $date_to->format('Y/m/d');
			$data['date_from_txt'] = $date_from->format('Ymd');
			$data['date_to_txt'] = $date_to->format('Ymd');
		} else {
			$time_now = Carbon::now();
			$data['date_from'] =  $time_now->startOfWeek()->format('Y/m/d');
			$data['date_to'] =  $time_now->endOfWeek()->format('Y/m/d');
			$data['date_from_txt']  = $time_now->startOfWeek()->format('Ymd');
			$data['date_to_txt']  = $time_now->endOfWeek()->format('Ymd');
		}
		$data['day_range'] = $this->get_list_days($data['date_from'], $data['date_to']);
		$day_range = [];
		$day_range_normal = [];
		$day_range_type = [
			'male' => NULL,
			'female' => '×',
			'pet' => '×',
			'wt' => '×',
		];
		foreach($data['day_range'] as $dr){
			$day_range[$dr['full_date']] = $day_range_type;
			$day_range_normal[] = $dr['full_date'];
		}
		$data['time_range'] = \Helper::getTimeAdmin($data['date_from_txt']);
		for($i = 0; $i < count($data['time_range']); $i++){
			$data['time_range'][$i]['day'] = $day_range;
		}
		// dd($data);
		$this->set_week_course($data, $day_range_normal);
		//$data['data_date'] = DB::table('tr_yoyaku')->where([['service_date_start','>=',$date_from_sql],['service_date_start','<=',$date_to_sql]])->get();
		return view('sunsun.admin.weekly',$data);
	}
	private function set_week_course(&$data, $day_range_normal){
		$start_day = $data['date_from_txt'];
		$end_day = $data['date_to_txt'];
		$week_course_query = DB::select("
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, time.service_date
					, time.service_time_1 as time
					, SUBSTRING(time.notes, 1, 1) as bed
				FROM        tr_yoyaku as main
				INNER JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id
					AND time.service_date between '$start_day' and '$end_day'
				WHERE   main.course = '01'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, time.service_date
					, time.service_time_1 as time
					, SUBSTRING(time.notes, 1, 1) as bed
				FROM        tr_yoyaku as main
				INNER JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id
					AND time.service_date between '$start_day' and '$end_day'
				WHERE   main.course = '04'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, time.service_date
					, time.service_time_2 as time
					, SUBSTRING(time.notes, 3, 1) as bed
				FROM        tr_yoyaku as main
				INNER JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id
					AND time.service_date between '$start_day' and '$end_day'
				WHERE   main.course = '04'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, time.service_date
					, time.service_time_1 as time
					, SUBSTRING(time.notes, 1, 1) as bed
				FROM        tr_yoyaku as main
				INNER JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id
					AND time.service_date between '$start_day' and '$end_day'
				WHERE   main.course = '06'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, time.service_date
					, time.service_time_2 as time
					, SUBSTRING(time.notes, 3, 1) as bed
				FROM        tr_yoyaku as main
				INNER JOIN tr_yoyaku_danjiki_jikan as time
				ON          main.booking_id = time.booking_id
					AND time.service_date between '$start_day' and '$end_day'
				WHERE   main.course = '06'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '02'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '02'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '07'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '07'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '08'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '09'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, '01' as gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, main.bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '03'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, '01' as gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, main.bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '11'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '10'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start as service_date
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
				FROM        tr_yoyaku as main
				WHERE   main.course = '10'
				AND main.service_date_start between '$start_day' and '$end_day'
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
		"); // 2020/06/05 son add
		$week_course = collect($week_course_query);
		$course_5_query = DB::select("
			SELECT  main.booking_id
					, main.course
					, main.service_date_start as service_date
					,CONCAT(main.service_time_1, '-', main.service_time_2) as time
			FROM    tr_yoyaku as main
			WHERE   main.course = '05'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		");
		$course_5 = collect($course_5_query);
		$course_wt_query = DB::select("
			SELECT  main.booking_id
					, main.course
					, main.service_date_start as service_date
					, main.whitening_time as time
			FROM    tr_yoyaku as main
			WHERE main.whitening <> '01'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		");
		$course_wt = collect($course_wt_query);
		$check_room = [];
		foreach($day_range_normal as $day){
		for($i = 0; $i < count($data['time_range']); $i++){
				// $check = $week_course->where('gender', '01')->where('service_date', $day)->where('time', $data['time_range'][$i]['time_value'])->where('course','03');
				// if(count($check)){
				//     $check_room[] = $check;
				//     $data['time_range'][$i]['day'][$day]['male'] = ['1','2','3'];
				// }else{
				// }
				$data['time_range'][$i]['day'][$day]['male'] = $week_course->where('gender', '01')->where('service_date', $day)->where('time', $data['time_range'][$i]['time_value']);
				$data['time_range'][$i]['day'][$day]['female'] = $week_course->where('gender', '02')->where('service_date', $day)->where('time', $data['time_range'][$i]['time_value']);
				$data['time_range'][$i]['day'][$day]['pet'] = $course_5->where('service_date', $day)->where('time', $data['time_range'][$i]['pet_time_value']);
				$data['time_range'][$i]['day'][$day]['wt'] = $course_wt->where('service_date', $day)->where('time', $data['time_range'][$i]['wt_time_value']);
			}
		}
	}
	private function get_list_days($from , $to){
		$from = Carbon::parse($from);
		$to = Carbon::parse($to);
		$dates = [];
		for($d = $from; $d->lte($to); $d->addDay()) {
			$dates[$d->format('Ymd')]['full_date'] = $d->format('Ymd');
			$dates[$d->format('Ymd')]['day'] = ltrim($d->format('d'), "0");
			$dates[$d->format('Ymd')]['month'] = ltrim($d->format('m'), "0");
			$dates[$d->format('Ymd')]['week_day'] = $this->get_week_day($d->format('Y/m/d'));
		}
		return array_values($dates);
	}
	private function get_week_day($day){
		$weekMap = [
			0 => '日',
			1 => '月',
			2 => '火',
			3 => '水',
			4 => '木',
			5 => '金',
			6 => '土',
		];
		$day_of_week = Carbon::parse($day)->dayOfWeek;
		return $weekMap[$day_of_week];
	}
	public function monthly(Request $request) {
		$data = [];
		$time_now = Carbon::now();
		if ($request->has('date') && $request->date != '') {
			$month = substr($request->date,4);
			$year = substr($request->date,0,4);
			$time_now = Carbon::createFromDate($year, $month, 1);
		} else {
			$time_now = Carbon::now();
		}
		$data['date'] =  $time_now->format('Y/m');
		$data['date_txt'] =  $time_now->format('Ym');
		// $date = $time_now->format('Ym');
		// $year = substr($date,0,4);
		// $month = substr($date,4);
		// $data['data_date'] = DB::table('tr_yoyaku')
		//     ->select(['email as title', 'service_date_start as start', 'service_date_start as end'])
		//     ->whereYear('service_date_start',$year)
		//     ->whereMonth('service_date_start',$month)
		//     ->get()
			/*->groupBy(function($date) {
				return Carbon::parse($date->service_date_start)->format('W');
			})*/;
		//$num_weeks = $this->get_month($month, $year);
		//dd($data['data_date']->all());
		// $number_month_day = date("t",mktime(0,0,0,$month,1,$year));
		// for($i = 1; $i <= $number_month_day; $i++){
		//     $month = sprintf("%02d", $month);
		//     $day = sprintf("%02d", $i);
		//     $data['month']['number_first_day'] =
		//     $data['month']['number_week'] = $this->week_of_month(\DateTime::createFromFormat('m/Y', $month . "/" .$year), $number_month_day);
		//     $data['day'][$year.$month.$day]['day'] = $year.$month.$day;
		//     $data['day'][$year.$month.$day]['weekday'] = date('w', strtotime($year . "/" . $month . "/" . $day));
		// }
		$data['week'] = $this->get_month($month,$year);
		for($i = 0 ; $i < count($data['week']); $i++){
			$data['week_range'][$i] = $this->get_list_dates($data['week'][$i]['start'], $data['week'][$i]['end'], $month);
		}
		$this->set_monthly_course($data);
		// dd($data);
		return view('sunsun.admin.monthly',['data' => $data, 'month' => $month, 'year' => $year]);
	}
	private function set_monthly_course(&$data){
		$start_day = $data['date_txt']."01";
		$end_day = $data['date_txt']."31";
		$week_course_query = DB::select("
		(SELECT main.booking_id
				  , main.course
				  , main.gender
				  , time.service_date
				  , time.service_time_1 as time
				  , SUBSTRING(time.notes, 1, 1) as bed
			FROM        tr_yoyaku as main
			INNER JOIN tr_yoyaku_danjiki_jikan as time
			ON          main.booking_id = time.booking_id
				AND time.service_date between '$start_day' and '$end_day'
			WHERE   main.course = '01'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '02'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '02'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, '01' AS gender
					, main.service_date_start
					, main.service_time_1 as time
					, main.bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '03'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, '01' AS gender
					, main.service_date_start
					, main.service_time_1 as time
					, main.bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '11'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
				  , main.course
				  , main.gender
				  , time.service_date
				  , time.service_time_1 as time
				  , SUBSTRING(time.notes, 1, 1) as bed
			FROM        tr_yoyaku as main
			INNER JOIN tr_yoyaku_danjiki_jikan as time
			ON          main.booking_id = time.booking_id
				AND time.service_date between '$start_day' and '$end_day'
			WHERE   main.course = '04'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
				  , main.course
				  , main.gender
				  , time.service_date
				  , time.service_time_2 as time
				  , SUBSTRING(time.notes, 3, 1) as bed
			FROM        tr_yoyaku as main
			INNER JOIN tr_yoyaku_danjiki_jikan as time
			ON          main.booking_id = time.booking_id
				AND time.service_date between '$start_day' and '$end_day'
			WHERE   main.course = '04'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
			AND main.fake_booking_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
				  , main.course
				  , main.gender
				  , time.service_date
				  , time.service_time_1 as time
				  , SUBSTRING(time.notes, 1, 1) as bed
			FROM        tr_yoyaku as main
			INNER JOIN tr_yoyaku_danjiki_jikan as time
			ON          main.booking_id = time.booking_id
				AND time.service_date between '$start_day' and '$end_day'
			WHERE   main.course = '06'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
				  , main.course
				  , main.gender
				  , time.service_date
				  , time.service_time_2 as time
				  , SUBSTRING(time.notes, 3, 1) as bed
			FROM        tr_yoyaku as main
			INNER JOIN tr_yoyaku_danjiki_jikan as time
			ON          main.booking_id = time.booking_id
				AND time.service_date between '$start_day' and '$end_day'
			WHERE   main.course = '06'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
			AND main.fake_booking_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '07'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '07'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '08'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '09'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_1 as time
					, SUBSTRING(main.bed, 1, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '10'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		UNION
		(
			SELECT  main.booking_id
					, main.course
					, main.gender
					, main.service_date_start
					, main.service_time_2 as time
					, SUBSTRING(main.bed, 3, 1) as bed
			FROM        tr_yoyaku as main
			WHERE   main.course = '10'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		)
		"); // 2020/06/05 son add
		$week_course = collect($week_course_query);
		$course_5_query = DB::select("
			SELECT  main.booking_id
					, main.course
					, main.service_date_start as service_date
					, main.service_time_1 as time
			FROM    tr_yoyaku as main
			WHERE   main.course = '05'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
			AND main.fake_booking_flg IS NULL
		");
		$course_5 = collect($course_5_query);
		$course_wt_query = DB::select("
			SELECT  main.booking_id
					, main.course
					, main.service_date_start as service_date
					, SUBSTRING(main.whitening_time, 1, 4) as time
			FROM    tr_yoyaku as main
			WHERE main.whitening <> '01'
			AND main.service_date_start between '$start_day' and '$end_day'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
			AND main.fake_booking_flg IS NULL
		");
		$course_wt = collect($course_wt_query);
		$monthly_data = [];
		// $monthly_data2 = [];
		// $week_list_day = [];
		foreach($week_course as $course){
			$day = $course->service_date;
			// $week_list_day[] = $day;
			$gender = $course->gender;
			$time = $course->time;
			if ($time >= '0930' && $time <= '1330') {
				if ($gender == '01') {
					$monthly_data[$day]['male'][0][] = $course;
				} else {
					$monthly_data[$day]['female'][0][] = $course;
				}
			} else if ($time > '1330' && $time <= '1800') {
				if ($gender == '01') {
					$monthly_data[$day]['male'][1][] = $course;
				} else {
					$monthly_data[$day]['female'][1][] = $course;
				}
			} else if ($time > '1800' && $time <= '2030') {
				if ($gender == '01') {
					$monthly_data[$day]['male'][2][] = $course;
				} else {
					$monthly_data[$day]['female'][2][] = $course;
				}
			}
		}
		foreach($course_5 as $course){
			$day = $course->service_date;
			// $week_list_day[] = $day;
			$time = $course->time;
			if ($time >= '0930' && $time <= '1330') {
				$monthly_data[$day]['pet'][0][] = $course;
			} else if ($time > '1330' && $time <= '1800') {
				$monthly_data[$day]['pet'][1][] = $course;
			}
		}
		foreach($course_wt as $course){
			$day = $course->service_date;
			// $week_list_day[] = $day;
			$time = $course->time;
			if ($time >= '0930' && $time <= '1330') {
				$monthly_data[$day]['wt'][0][] = $course;
			} else if ($time > '1330' && $time <= '1800') {
				$monthly_data[$day]['wt'][1][] = $course;
			}
		}
		// echo Carbon::now();
		// foreach($week_list_day as $day){
		//     $monthly_data2[$day]['male'][0] = $week_course->where('service_date', $day)->where('gender', '01')->where('time','>=' , '0930')->where('time','<=' , '1330');
		//     $monthly_data2[$day]['female'][0] = $week_course->where('service_date', $day)->where('gender', '02')->where('time','>=' , '0930')->where('time','<=' , '1330');
		//     $monthly_data2[$day]['male'][1] = $week_course->where('service_date', $day)->where('gender', '01')->where('time','>=' , '1330')->where('time','<=' , '1800');
		//     $monthly_data2[$day]['female'][1] = $week_course->where('service_date', $day)->where('gender', '02')->where('time','>=' , '1330')->where('time','<=' , '1800');
		//     $monthly_data2[$day]['male'][2] = $week_course->where('service_date', $day)->where('gender', '01')->where('time','>=' , '1800')->where('time','<=' , '2030');
		//     $monthly_data2[$day]['female'][2] = $week_course->where('service_date', $day)->where('gender', '02')->where('time','>=' , '1800')->where('time','<=' , '2030');
		//     $monthly_data2[$day]['pet'][0] = $course_5->where('service_date', $day)->where('time','>=' , '0930')->where('time','<=' , '1330');
		//     $monthly_data2[$day]['pet'][1] = $course_5->where('service_date', $day)->where('time','>=' , '1330')->where('time','<=' , '1800');
		//     $monthly_data2[$day]['wt'][0] = $course_wt->where('service_date', $day)->where('time','>=' , '0930')->where('time','<=' , '1330');
		//     $monthly_data2[$day]['wt'][1] = $course_wt->where('service_date', $day)->where('time','>=' , '1330')->where('time','<=' , '1800');
		// }
		//Log::debug("abc");
		//Log::debug($monthly_data);
		$data['monthly_data'] = $monthly_data;
	}
	private function get_list_dates($from , $to, $month){
		$from = Carbon::parse($from);
		$to = Carbon::parse($to);
		$dates = [];
		for($d = $from; $d->lte($to); $d->addDay()) {
			if($d->format('m') == $month){
				$dates[$d->format('Ymd')]['full_date'] = $d->format('Ymd');
				$dates[$d->format('Ymd')]['day'] = ltrim($d->format('d'), "0");
			}else{
				$dates[$d->format('Ymd')]['full_date'] = NULL;
			}
		}
		return array_values($dates);
	}
	private function get_month ($month, $year){
		$date = Carbon::createFromDate($year,$month);
		$data = [];
		for ($i=1; $i <= $date->daysInMonth ; $i++) {
			Carbon::createFromDate($year,$month,$i);
			$week_num = Carbon::createFromDate($year,$month,$i)->format('W');
			$data[$week_num]['start']= Carbon::createFromDate($year,$month,$i)->startOfWeek()->format('Y/m/d');
			$data[$week_num]['end']= Carbon::createFromDate($year,$month,$i)->endOfweek()->format('Y/m/d');
		}
		return array_values($data);
	}
	// user
	public function user(Request $request)
	{
		$data = null;
		if($request->ajax())
		{
			$data = $request->all();
		}
		$list_data = $this->get_list_search_user($data);
		// return create
		if ($data == null) return view('sunsun.admin.user', ['data' => $list_data, 'type' => 0]);
		// return ajax
					$request->session()->forget('key');
					$request->session()->put('key',$data);
		$view = view('sunsun.admin.layouts.user_data',['data' => $list_data, 'type' => 1])->render();
				return [
					'status' => true,
					'data' => $view
				];
	}
	public function get_list_search_user($data=null, $type=null){
		$where = array();
		if ($data != null) {
			$bookCon = new BookingController();
			$data['username'] = $bookCon->change_value_kana($data['username']);
			$username = $data['username'];
			$phone = $data['phone'];
			$email = $data['email'];
			if (!empty($username)) {
				$username = '%'.$username.'%';
				$username = str_replace("*","%", $username);
				$where[] = ['username', 'LIKE', $username];
			}
			if(!empty($phone)){
				$phone = '%'.$phone.'%';
				$phone = str_replace("*","%", $phone);
				$where[] = ['tel', 'LIKE', $phone];
			}
			if (!empty($email)) {
				$email = '%'.$email.'%';
				$email = str_replace("*","%", $email);
				$where[] = ['email', 'LIKE', $email];
			}
			if (isset($data['notshowdeleted']) && $data['notshowdeleted'] == 1) {
				$where[] = ['deleteflg', '=', 0];
			}
		}
		$id_not_get = [1];
			if (!empty($where)) {
			$data = MsUser::where($where)->whereNotIn('ms_user_id', $id_not_get);
				}else{
			$data = MsUser::whereNotIn('ms_user_id', $id_not_get);
				}
		$sub_where = " where ty.ref_booking_id is null ";
		$data = $data->selectRaw(" *,
			(
				SELECT GROUP_CONCAT(DISTINCT date_used
						   ORDER BY date_used ASC SEPARATOR ', ') AS date_used
				FROM
					(SELECT service_date_start AS date_used,
						 ty.ms_user_id
					FROM tr_yoyaku ty
					$sub_where
					UNION SELECT service_date_end AS date_used,
							   ty.ms_user_id
					FROM tr_yoyaku ty
					$sub_where
					UNION SELECT stay_checkin_date AS date_used,
							   ty.ms_user_id
					FROM tr_yoyaku ty
					$sub_where
					UNION SELECT stay_checkout_date AS date_used,
							   ty.ms_user_id
					FROM tr_yoyaku ty
					WHERE ty.ref_booking_id IS NULL
					UNION SELECT service_date AS date_used,
							   ty.ms_user_id
					FROM tr_yoyaku ty
					JOIN (select booking_id,service_date from tr_yoyaku_danjiki_jikan) tydj ON tydj.booking_id = ty.booking_id
					$sub_where ) AS db_temp
				WHERE db_temp.ms_user_id = ms_user.ms_user_id
			) AS date_used
		");
		if (!empty($type)) {
			$data = $data->get();
			}else{
			$data = $data->paginate(20);
		}
		return $data;
	}
	public function update_user(Request $request){
		$responsive_array = array();
		$data = $request->all();
		$validation = Validator::make($data, [
			'username' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:ms_user,email,'.$data['user_id'].',ms_user_id|regex:/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/',
			'tel' => 'required|string|max:11|min:10|regex:/[0-9]{10,11}/',
			// 'password' => 'required|string|min:1', //'confirmed'
		]);
		$bookCon = new BookingController();
		$check_kata = $bookCon->check_is_katakana($data['username']);
		$check_vali = $validation->fails();
		if ($check_vali || !$check_kata) {
			$error_messages = "";
			if ($check_vali) $error_messages = implode('', $validation->errors()->all());
			if (!$check_kata) {
				$error_messages .= config('const.error.katakana');
			}
			$responsive_array = array('status' => false, 'type' => 'update', 'message' => $error_messages);
		}else{
			$ms_user = $this->update_msuser($data, $request);
			$responsive_array = array('status' => true, 'type' => 'update', 'message' => null, 'data' => $ms_user);
		}
		return $responsive_array;
	}
	// update user
	private function update_msuser($data, $request){
		$result = [];
		if(isset($data['user_id'])){
			try{
				if ($data['checkdelete'] == 'true') {
					$deleteflg = "1";
				}else{
					$deleteflg = "0";
				}
				$bookCon = new BookingController();
				$data['username'] = $bookCon->change_value_kana($data['username']);
				MsUser::where('ms_user_id', $data['user_id'])->update(['username' => $data['username'], /*'password' => $data['password'], */'email' => $data['email'], 'tel' => $data['tel'], 'deleteflg' => $deleteflg]);
				$result = [
					'status' => true
				];
			} catch (\Exception $failed) {
				$result = [
					'status' => false
				];
			}
			return  $result;
		}
	}
	public function get_data_search_pagination(Request $request)
	{
		if($request->ajax())
		{
			$data = $request->all();
			$_data_paginate = $this->get_list_search_user($data);
			$view = view('sunsun.admin.layouts.user_data',['data' => $_data_paginate,'type' => 1])->render();
			return [
				'status' => true,
				'data' => $view
			];
		}
	}
	public function export(Request $request){
		$data = $request->session()->get('key');
		$date_down = now();
		if (!empty($data)) {
			$checkdelete = (isset($data['notshowdeleted'])) ? $data['notshowdeleted'] : '';
			$request->session()->forget('key');
			return Excel::download(new UsersExport($data['username'], $data['phone'], $data['email'], $checkdelete), $date_down.'.csv');
		}else{
			$request->session()->forget('key');
			return Excel::download(new UsersExport(), $date_down.'.csv');
		}
	}
	// ms_holiday
	public function ms_holiday(Request $request)
	{
		if($request->ajax())
		{
			$data = $request->all();
			$list_search = $this->get_list_search_holiday($data);
			$view = view('sunsun.admin.layouts.holiday_data',['data' => $list_search, 'type' => 1])->render();
			return [
				'status' => true,
				'data' => $view
			];
		}
		else
		{
			$list_data = $this->get_data_holiday();
			return view('sunsun.admin.msholiday', ['data' => $list_data, 'type' => 0]);
		}
	}
	// add holiday
	public function add_holiday(Request $request)
	{
		$data = array();
		$data['holiday_add_date_year'] = $request->holiday_add_date_year;
		$data['holiday_add_date_month'] = $request->holiday_add_date_month;
		$data['holiday_add_date_day'] = $request->holiday_add_date_day;
		if (strlen($data['holiday_add_date_month']) == 1) {
			$data['holiday_add_date_month'] = '0'.$data['holiday_add_date_month'];
		}
		if (strlen($data['holiday_add_date_day']) == 1) {
			$data['holiday_add_date_day'] = '0'.$data['holiday_add_date_day'];
		}
		$data['holiday_date'] = $data['holiday_add_date_year'].$data['holiday_add_date_month'].$data['holiday_add_date_day'];
		$data['note_holiday'] = $request->holiday_note;
		$ms_holiday = $this->insert_holiday($data, $request);
		if ($ms_holiday == false) {
			return redirect()->back()->withInput()->with('error_holiday', 'Time exits?');;
		}else{
			return redirect()->route('admin.msholiday');
		}
	}
	private function insert_holiday($data, $request){
		if(isset($data['holiday_date'])){
			$check_row_exits = MsHoliday::where('date_holiday','=', $data['holiday_date'])->first();
			if ($check_row_exits === null) {
				$bookCon = new BookingController();
				$data['note_holiday'] = $bookCon->change_value_kana($data['note_holiday']);
				MsHoliday::insert(['date_holiday' => $data['holiday_date'], 'note_holiday' => $data['note_holiday']]);
				$result = true;
			}else{
				$result = false;
			}
		}
		return $result;
	}
	public function update_holiday(Request $request)
	{
		$data = $request->all();
		if ($data['checkdelete'] == 'true') {
			MsHoliday::where('ms_holiday_id', $data['holiday_id'])->delete();
			$responsive_array = array('status' => true);
		}else{
			$holiday_date_old = str_replace('/','',$data['holiday_date_old']);
			$holiday_date = explode('/',$data['date_holiday']);
			if (strlen($holiday_date[1]) == 1) {
				$holiday_date[1] = '0'.$holiday_date[1];
			}
			if (strlen($holiday_date[2]) == 1) {
				$holiday_date[2] = '0'.$holiday_date[2];
			}
			$holiday_date_update = $holiday_date[0].$holiday_date[1].$holiday_date[2];
			$bookCon = new BookingController();
			$data['holiday_note'] = $bookCon->change_value_kana($data['holiday_note']);
			$check_exits = MsHoliday::where('date_holiday' ,'=', $holiday_date_update)->first();
			if ($check_exits != null && $check_exits['ms_holiday_id'] == $data['holiday_id']) {
				MsHoliday::where('date_holiday', $holiday_date_old)->update(['date_holiday' => $holiday_date_update, 'note_holiday' => $data['holiday_note']]);
				$responsive_array = array('status' => true);
			}else if($check_exits != null && $check_exits['ms_holiday_id'] != $data['holiday_id']){
				$responsive_array = array('status' => false);
			}else{
				MsHoliday::where('date_holiday', $holiday_date_old)->update(['date_holiday' => $holiday_date_update, 'note_holiday' => $data['holiday_note']]);
				$responsive_array = array('status' => true);
			}
		}
		return $responsive_array;
	}
	private function get_data_holiday(){
		$result = MsHoliday::where('time_holiday', null)->orderBy('ms_holiday_id', 'DESC')->paginate(1);
		return $result;
	}
	private function get_list_search_holiday($data){
		if(isset($data) && !empty($data)){
			$date_start_year = $data['holiday_start_date_year'];
			$date_start_month = $data['holiday_start_date_month'];
			$date_start_day = $data['holiday_start_date_day'];
			$date_end_year = $data['holiday_end_date_year'];
			$date_end_month = $data['holiday_end_date_month'];
			$date_end_day = $data['holiday_end_date_day'];
			if (strlen($date_start_month) == 1) {
				$date_start_month = '0'.$date_start_month;
			}
			if (strlen($date_start_day) == 1) {
				$date_start_day = '0'.$date_start_day;
			}
			if (strlen($date_end_month) == 1) {
				$date_end_month = '0'.$date_end_month;
			}
			if (strlen($date_end_day) == 1) {
				$date_end_day = '0'.$date_end_day;
			}
			$date_start = $date_start_year.$date_start_month.$date_start_day;
			$date_end = $date_end_year.$date_end_month.$date_end_day;
			$data = MsHoliday::whereBetween('date_holiday', [$date_start, $date_end])->orderby('ms_holiday_id', 'DESC')->paginate(1);
			return $data;
		}
	}
	public function get_data_holiday_search_pagination(Request $request)
	{
		if($request->ajax())
		{
			$data = $request->all();
			$_data_paginate = $this->get_list_search_holiday($data);
			$view = view('sunsun.admin.layouts.holiday_data',['data' => $_data_paginate,'type' => 1])->render();
			return [
				'status' => true,
				'data' => $view
			];
		}
	}
	public function setting() {
		$data['data'] = Setting::find(1)->pluck('accommodation_flg')->first();
		return view('sunsun.admin.setting_accommodation_flg',$data);
	}
	public function update_setting(Request $request) {
		$data = $request->all();
		Setting::where('ms_setting_id', "1")->update(['accommodation_flg' => $data["accommodation_flg"]]);
		return redirect()->route('admin.setting');
	}
	public function ajax_save_notes(Request $request)
	{
		$status = true;
		try {
			$date_notes = str_replace("/","",str_replace("-","",$request->date_notes));
			$rowNotes = TrNotes::where('date_notes','=', $date_notes)->first();
			if ($rowNotes === null) {
				$rowNotes = new TrNotes;
				$rowNotes->date_notes = $date_notes;
			}
			$rowNotes->txt_notes = $request->txt_notes;
			$rowNotes->save();
		} catch (Exception $ex) {
			$status = false;
			//Log::debug($ex->getMessage());
		}
		return [
			'status' => $status
		];
	}
	public function ajax_name_search(Request $request)
	{
		$result = "";
		try {
			$result = $this->get_search_expert($request->name);
		} catch (Exception $ex) {
			$result = null;
		}
		return [
			'result' => $result
		];
	}
	// payments_history
	public function create_payments_history()
	{
		#get booking id is not exit tr_payemnts_history
		$list_id = DB::select("
			SELECT DISTINCT booking_id
			FROM tr_yoyaku
			WHERE history_id is null and ref_booking_id is null and name is not null
			and not exists (select 1 from tr_payments_history ph where ph.booking_id = tr_yoyaku.booking_id )
		");
		foreach($list_id as $row) {
			#create tr_payments_history
			$booking = new BookingController();
			$booking->save_tr_payments_history($row->booking_id);
		}
	}
	public function sales_list(Request $request)
	{
		$check_view = true;
		$data = null;
		if($request->ajax())
		{
			$data = $request->all();
			$check_view = false;
		}
		// return create
		if ($data == null) {
			$data = $request->all();
			$page = isset($data["page"]) ? $data["page"] : 1;
			if ($request->session()->has('key_sales_list')) {
				$data = $request->session()->get('key_sales_list');
			} else {
				$data = [
					'date_start' => date("Y/m/d"),
					'date_end' => date("Y/m/d"),
					'notshowdeleted' => '1'
				];
			}
			$data["page"] = $page;
			$data['date_start_view'] = $this->get_week_day(Carbon::parse($data['date_start']));
			$data['date_end_view'] = $this->get_week_day(Carbon::parse($data['date_end']));
		}
		$list_data = $this->get_salse_list($data);
		if ($check_view) return view('sunsun.admin.sales_list', ['data' => $list_data, 'type' => 0, 'data_search' => $data]);
		// return ajax
		$request->session()->forget('key_sales_list');
		$request->session()->put('key_sales_list',$data);
		$view = view('sunsun.admin.layouts.sales_list_data',['data' => $list_data, 'type' => 1])->render();
		return [
			'status' => true,
			'data' => $view
		];
	}
	public function pagination_sales_list(Request $request)
	{
		if($request->ajax())
		{
			$data = $request->all();
			$_data_paginate = $this->get_salse_list($data);
			$view = view('sunsun.admin.layouts.sales_list_data',['data' => $_data_paginate,'type' => 1])->render();
			return [
				'status' => true,
				'data' => $view
			];
		}
	}
	public function export_sales_list(Request $request){
		$data = $request->session()->get('key_sales_list');
		$date_down = now();
		return Excel::download(new SalesListExport($data), $date_down.'_sales_list.csv');
	}
	public function get_salse_list($data=null, $type=null){
		$where = "";
		$where_date = "";
		if ($data != null) {
			$date_start = isset($data['date_start']) ? $data['date_start'] : '';
			$date_start = str_replace("/","",$date_start);
			$date_end = isset($data['date_end']) ? $data['date_end'] : '';
			$date_end = str_replace("/","",$date_end);
			$check_del = isset($data['notshowdeleted']) ? $data['notshowdeleted'] : 0;
			$where_join = "";
			if (!empty($date_start) && !empty($date_end)) {
				$where_date = " (yo.service_date_end is null and yo.service_date_start >= '$date_start' and yo.service_date_start <= '$date_end')
								or
								(yo.service_date_end is not null and
									(
										(yo.service_date_start <= '$date_start' and yo.service_date_end >= '$date_start')
										or
										(yo.service_date_start <= '$date_end' and yo.service_date_end >= '$date_end')
										or
										(yo.service_date_start >= '$date_start' and yo.service_date_end <= '$date_end')
									)
								) ";
			}
			else if (!empty($date_start)) {
				$where_date = " (yo.service_date_end is null and yo.service_date_start >= '$date_start' )
								or
								(yo.service_date_end is not null and
									(
										(yo.service_date_start <= '$date_start' and yo.service_date_end >= '$date_start')
									)
								) ";
			}
			else if (!empty($date_end)) {
				$where_date = " (yo.service_date_end is null and yo.service_date_start <= '$date_end' )
								or
								(yo.service_date_end is not null and
									(
										(yo.service_date_start <= '$date_end' and yo.service_date_end >= '$date_end')
									)
								) ";
			}
			if (!empty($where_date)) {
				$where_date = " ($where_date) ";
			}
			if ($check_del == 1) {
				$where = " (yo.del_flg is null) ";
			}
		}
		$data = DB::table('tr_payments_history as ph')->selectRaw("
					yo.booking_id
					,yo.created_at
					,yo.name
					, yo.phone
					, yo.email
					, ph.gender
					, ph.age_value
					, ph.date_value
					, ph.repeat_user
					, mk1.kubun_value as transport
					, ph.product_name
					, ph.price
					, CONCAT(ph.quantity,ph.unit) as quantity
					, ph.money
					, mk2.kubun_value as payment_method
				")
				->join('tr_yoyaku as yo', function($join) use ($where,$where_date)
				{
					$join->on('yo.booking_id', '=', 'ph.booking_id');
					$join->whereNull('yo.history_id')->whereNull('yo.ref_booking_id');
					if (!empty($where)) {
						$join->whereRaw($where);
					}
					if (!empty($where_date)) {
						$join->whereRaw($where_date);
					}
				})
				->leftJoin('ms_kubun as mk1', function($join)
				{
					$join->on('mk1.kubun_id','=','yo.transport')
						->where('mk1.kubun_type','=','002');
				})
				->leftJoin('ms_kubun as mk2', function($join)
				{
					$join->on('mk2.kubun_id','=','yo.payment_method')
						->where('mk2.kubun_type','=','032');
				})
				->orderBy('yo.service_date_start', 'DESC')
				->orderBy('ph.booking_id', 'DESC')
				->orderBy('ph.tr_payments_history_id', 'ASC');
		//$booking = new BookingController();
		//Log::debug($booking::getEloquentSqlWithBindings($data));
		if (!empty($type)) {
			$data = $data->get();
		}else{
			$data = $data->paginate(20);
		}
		return $data;
	}
}