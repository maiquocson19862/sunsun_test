<?php
namespace App\Http\Controllers\Sunsun\Front;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sunsun\Front\BookingRequest;
use App\Jobs\ConfirmJob;
use App\Jobs\ReminderJob;
use App\Models\MsKubun;
use App\Models\Yoyaku;
use App\Models\YoyakuDanjikiJikan;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\ConfirmMail;
use Illuminate\Support\Facades\Mail;
use \Session;
use App\Models\Setting;
use App\Models\PaymentHistory;
class BookingController extends Controller
{
	private $session_info = 'SESSION_BOOKING_USER';
	private $session_html = 'SESSION_BOOKING_DATA';
	private $payment_html = 'SESSION_PAYMENT_DATA';
	private $session_price = "SESSION_PRICE";
	private $session_price_admin = "SESSION_PRICE_ADMIN";
	public function index(Request $request){
		$request->session()->forget($this->session_info);
		return view('sunsun.front.booking.index');
	}
	public function clear_session(Request $request){
		$request->session()->forget($this->session_info);
		return redirect()->route('home');
	}
	public function booking(Request $request){
		$data = $request->all();
		$data['new'] = 1;
		$this->fetch_kubun_data($data);
		// dd($data);
		if (isset($request->add_new_user)) {
			$data['add_new_user'] = $request->add_new_user;
		} else {
			$request->session()->forget($this->session_info);
		}
		$data['customer'] = $this->get_booking($request);
		return view('sunsun.front.booking',$data);
	}
	public function back_2_booking(Request $request){
		if($this->check_pop_booking($request) == true){
			$data_temp1 = $request->all();
			$this->fetch_kubun_data($data_temp1);
			$data_temp2 = $this->pop_booking($request);
			$data = array_merge($data_temp2, $data_temp1);
			$data = array_merge($data, $data_temp2['pop_data']['customer']);
			if(isset($data_temp2['pop_data']['add_new_user'])){
				$data['add_new_user'] = $data_temp2['pop_data']['add_new_user'];
			}
			return view('sunsun.front.booking',$data);
		}else{
			return redirect()->route('.booking');
		}
	}
	public function add_new_booking(Request $request) {
		$data = $request->all();
		// dd($data);
		if (isset($data['transport'])) {
			$this->save_session($request, $data);
			$this->add_fake_booking($request, $data);
		}
		return ['status'=> 'OK'];
	}
	public function validate_before_booking(Request $request){
		$data = $request->all();
		$error = $this->validate_booking($data);
		return $error;
	}
	public function save_booking(Request $request) {
		$data = $request->all();
		// dd($data);
		$error = $this->validate_booking($data);
		if (count($error) > 0) {
			return $error;
		}
		$this->save_session($request, $data);
		$this->add_fake_booking($request, $data);
		return ['status'=> 'OK'];
	}
	private function add_fake_booking($request, &$data){
		$course = json_decode($data['course'], true);
		if($course['kubun_id'] == '03'){
			$data['fake_booking'] = 1;
			$temp_json = json_decode($data['time'][0]['json'], true);
			// 2 ô trống đầu tiên
			$data['time_room_bed'] = 2;
			$temp_json['kubun_id_room'] = '02';
			$temp_json['kubun_value_room'] = '2';
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
			$data['time_room_bed'] = 3;
			$temp_json['kubun_id_room'] = '03';
			$temp_json['kubun_value_room'] = '3';
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
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
			$this->save_session($request, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
			//row 2
			$temp_json['notes'] = $time_row2;
			$data['time_room_value'] = $time_row2;
			$data['time_room_bed'] = 2;
			$temp_json['kubun_id_room'] = '02';
			$temp_json['kubun_value_room'] = '2';
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
			//row 2
			$temp_json['notes'] = $time_row2;
			$data['time_room_value'] = $time_row2;
			$data['time_room_bed'] = 3;
			$temp_json['kubun_id_room'] = '03';
			$temp_json['kubun_value_room'] = '3';
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
			//row 3
			$temp_json['notes'] = $time_row3;
			$data['time_room_value'] = $time_row3;
			$data['time'][0]['json'] = json_encode($temp_json);
			$this->save_session($request, $data);
		}
	}
	public function validate_booking($data, $booking_id = 0) {
		$error = [];
		$transport = $data['transport'];
		$transport = json_decode($transport, true);
		$repeat_user = json_decode($data['repeat_user'], true);
		$course = json_decode($data['course'], true);
		$check_bus = false;
		if ($transport['kubun_id'] == config('const.db.kubun_id_value.transport.BUS') ) { // nếu đi xe bus'02'
			if ($repeat_user['kubun_id'] == config('const.db.kubun_id_value.repeat_user.NEW')) { // はじめて
				$time_required_arrived = config('const.db.time_validate.transport.bus.NEW'); // người tắm lần đầu 01
			} else { //　リピート
				$time_required_arrived = config('const.db.time_validate.transport.bus.OLD');  // người cũ 02
			}
			$bus_arrive_time_slide = $data['bus_arrive_time_slide'];
			$bus_arrive_time_slide = json_decode($bus_arrive_time_slide, true);
			$time_bus_arrived = $bus_arrive_time_slide['notes'];
			$time_required = $this->plus_time_string ($time_bus_arrived, $time_required_arrived); // time required go early for BUS
			$check_bus = true;
		}
		$check_gender = false;
		$kubun_type_bed = '';
		if ($course['kubun_id'] == config('const.db.kubun_id_value.course.NORMAL')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH_NOON')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.REFRESH')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.SKIN')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.UP')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN2')) { // 2020/06/05
			$gender = json_decode($data['gender'], true);
			if($gender['kubun_id'] == '01'){ // for man
				$kubun_type_bed = config('const.db.kubun_type_value.bed_male'); // 017 kubun_type
			}else{ //for woman
				$kubun_type_bed = config('const.db.kubun_type_value.bed_female'); // 018 kubun_type
			}
			$check_gender = true;
			if ($course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN') || $course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN2')) { // 2020/06/05
				$check = $this->validate_holyday($data["plan_date_start-value"], $data["plan_date_end-value"]);
				if (!$check) {
					$error['error_fasting_plan_holyday']['start']['element'] = 'plan_date_start';
					$error['error_fasting_plan_holyday']['end']['element'] = 'plan_date_end';
				}
			}
		}
		// whitening
		// if (isset($data['whitening_data'])) {
		// 	$whitening = json_decode($data['whitening'], true);
		// 	if ($whitening['kubun_id'] == '02') {
		// 		$data_json_time = json_decode($data['whitening_data']['json'], true );
		// 		if ($data_json_time == null) {
		// 			$error['error_time_empty']['whitening']['element'] = $data['whitening_data']['element'];
		// 			//return $error;
		// 		} else {
		// 			if ($check_bus && $whitening['kubun_id'] == '02') {
		// 				$time_start_whitening = substr($data_json_time['notes'], 0,4);
		// 				if ($time_start_whitening < $time_required) { // check required time. time choice always >= time required
		// 					$error['error_time_transport'][]['element'] = $data['whitening_data']['element'];
		// 				} else {
		// 					$error['clear_border_red'][]['element'] = $data['whitening_data']['element'];
		// 				}
		// 			} else {
		// 				$error['clear_border_red']['whitening']['element'] = $data['whitening_data']['element'];
		// 			}
		// 		}
		// 	}
		// }
		if (isset($data['time'])) {
			$time_customer_choice = $data['time'];
			//return $time_customer_choice;
			foreach ($time_customer_choice as $key => $time) {
				if ($course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN')
				|| $course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN2')) { // 2020/06/05
					$data_json_time_1 = json_decode($time['from']['json'], true );//dd($time);
					$data_json_time_2 = json_decode($time['to']['json'], true );
					if ($data_json_time_1 == null) {
						$error['error_time_empty'][]['element'] = $time['from']['element'];
					} else {
						if($data_json_time_1['date_booking'] === $data['plan_date_start-value']) {
							if ($check_bus) { // validate nếu đi xe bus
								if ($data_json_time_1['notes'] < $time_required) { // check required time. time choice always >= time required
									$error['error_time_transport'][]['element'] = $time['from']['element'];
								} else {
									$error['clear_border_red'][]['element'] = $time['from']['element'];
								}
							}
							if ($check_gender) {
								if ($kubun_type_bed != $data_json_time_1['gender_type']) {
									//$error['error_time_transport'][$key]['data'] = $data_json_time;
									$error['error_time_gender'][]['element'] = $time['from']['element'];
								} else {
									$error['clear_border_red'][]['element'] = $time['from']['element'];
								}
							}
						}
					}
					if ($data_json_time_2 == null) {
						$error['error_time_empty'][]['element'] = $time['to']['element'];
					} else {
						if($data_json_time_2['date_booking'] === $data['plan_date_start-value']){
							if ($check_bus) { // validate nếu đi xe bus
								if ($data_json_time_2['notes'] < $time_required) { // check required time. time choice always >= time required
									$error['error_time_transport'][]['element'] = $time['to']['element'];
								} else {
									$error['clear_border_red'][]['element'] = $time['to']['element'];
								}
							}
						}
						if ($check_gender) {
							if ($kubun_type_bed < $data_json_time_2['gender_type']) { // check required time. time choice always >= time required
								$error['error_time_transport'][]['element'] = $time['to']['element'];
							} else {
								$error['clear_border_red'][]['element'] = $time['to']['element'];
							}
						}
					}
				} else {
					$data_json_time = json_decode($time['json'], true );
					if ($data_json_time == null) { // check nếu chưa chọn time
						$error['error_time_empty'][$key]['element'] = $time['element'];
					} else {
						if ($check_bus) { // validate nếu đi xe bus
							if ($course['kubun_id'] == config('const.db.kubun_id_value.course.PET')){
								$time_start_whitening = substr($data_json_time['notes'], 0,4);
								if ($time_start_whitening < $time_required) { // check required time. time choice always >= time required
									$error['error_time_transport'][$key]['element'] = $time['element'];
								} else {
									$error['clear_border_red'][$key]['element'] = $time['element'];
								}
							} else {
								if ($data_json_time['notes'] < $time_required) { // check required time. time choice always >= time required
									//$error['error_time_transport'][$key]['data'] = $data_json_time;
									$error['error_time_transport'][$key]['element'] = $time['element'];
								} else {
									$error['clear_border_red'][$key]['element'] = $time['element'];
								}
							}
						}
						if ($check_gender) { // check gender
							if ($kubun_type_bed != $data_json_time['gender_type']) {
								$error['error_time_gender'][$key]['element'] = $time['element'];
							} else {
								$error['clear_border_red'][$key]['element'] = $time['element'];
							}
						}
					}
				}
			}
		}
		$stay_room_type = isset($data['stay_room_type'])?json_decode($data['stay_room_type'], true)['kubun_id']:'01';
		if($stay_room_type != '01'){
			$range_date_start = $data['range_date_start-value'];
			$range_date_end = $data['range_date_end-value'];
			$check = $this->validate_holyday($range_date_start, $range_date_end,$stay_room_type);
			if (!$check) {
				$error['room_error_holiday'] = "1";
				$error['room_select_error']['start']['element'] = 'range_date_start';
				$error['room_select_error']['end']['element'] = 'range_date_end';
			} else {
				//Log::debug($range_date_start);
				//Log::debug($range_date_end);
				$number_dup = DB::select("
				SELECT  main.booking_id,
						main.stay_checkin_date,
						main.stay_checkout_date
				FROM    tr_yoyaku main
				WHERE   main.stay_room_type = $stay_room_type
				AND ( NOT  ( main.stay_checkin_date >= $range_date_end OR main.stay_checkout_date <= $range_date_start )
					)
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
				AND main.booking_id <> $booking_id
				");
				//Log::debug('$number_dup');
				//Log::debug($number_dup);
				if((count($number_dup) != 0) || ($range_date_start >= $range_date_end)){
					$error['room_error_holiday'] = "0";
					$error['room_select_error']['start']['element'] = 'range_date_start';
					$error['room_select_error']['end']['element'] = 'range_date_end';
				}else{
					$error['clear_border_red']['start']['element'] = 'range_date_start';
					$error['clear_border_red']['end']['element'] = 'range_date_end';
				}
			}
		}
//         dd($error);
		if (isset($error['error_time_transport']) == false
			&& isset($error['error_time_gender']) == false
			&& isset($error['room_select_error']) == false
			&& isset($error['error_time_empty']) == false
			&& isset($error['error_fasting_plan_holyday']) == false) {
			$error = [];
		}
		return $error;
	}
	public function plus_time_string ($time, $plus) {
		$time_hour = substr($time, 0,2);
		$time_minutes = substr($time, 2,4);
		$bus_minutes_more = (int)$time_minutes + $plus;
		$hours_more = floor($bus_minutes_more / 60);
		$minutes_more = ($bus_minutes_more % 60);
		$time_bus_hour = (int)$time_hour + $hours_more;
		$time_required = (string)sprintf('%02d', $time_bus_hour). (string)sprintf('%02d', $minutes_more);
		return $time_required;
	}
	public function minus_time_string ($time, $minus) {
		$time_hour = (int)substr($time, 0,2);
		$time_minutes = substr($time, 2,4);
		$hours = 0;
		if ($minus >= 60) {
			$hours = (int)floor($minus / 60);
			$minus = ($minus % 60);
		}
		if ($minus > $time_minutes) {
			$time_minutes += 60;
			$hours += 1;
		}
		$hours = $time_hour - $hours;
		$minus = $time_minutes - $minus;
		$time_required = (string)sprintf('%02d', $hours). (string)sprintf('%02d', $minus);
		return $time_required;
	}
	public function check_pop_booking($request){
		$info = [];
		if ($request->session()->has($this->session_info)) {
			$info = $request->session()->get($this->session_info);
		}
		if(isset($info['info']) && count($info['info']) > 0){
			return true;
		}else{
			return false;
		}
	}
	public function pop_booking($request) {
		$info = [];
		if ($request->session()->has($this->session_info)) {
			$info = $request->session()->get($this->session_info);
		}
		$return_data['pop_data'] = array_pop($info['info']);
		if(json_decode($return_data['pop_data']['course'], true)['kubun_id'] === '03'){
			for($i = 0; $i < 8; $i++){
				$return_data['pop_data'] = array_pop($info['info']);
			}
		}
		$return_data['pop_data']['customer']['customer'] = $info;
		if(count($info['info']) == 0){
			$request->session()->forget($this->session_info);
		}else{
			$request->session()->put($this->session_info,$info);
		}
		return $return_data;
	}
	/**
	 * @param $request Request
	 * @return array
	 */
	public function get_booking($request) {
		$info = [];
		if ($request->session()->has($this->session_info)) {
			$info = $request->session()->get($this->session_info);
		}
		return $info;
	}
	/**
	 * @param $request Request
	 * @param $data
	 */
	public function save_session($request, $data) {
		$info_customer = [];
		if ($request->session()->exists($this->session_info)) {
			$info_customer = $request->session()->get($this->session_info);
		} else {
			$info_customer['transport'] =  $data['transport'];
			if ($data['transport'] !== config('booking.transport.options.car')) {
				$info_customer['bus_arrive_time_slide'] = $data['bus_arrive_time_slide'];
				$info_customer['pick_up'] = $data['pick_up'];
				$info_customer['pick_up'] = $data['pick_up'];
			}
			if (isset($data['date-view'])) {
				$info_customer['date-view'] = $data['date-view'];
			} else {
				$info_customer['date-view-from'] = $data['plan_date_start-view'];
				$info_customer['date-view-to'] = $data['plan_date_end-view'];
			}
			if (isset($data['date-value'])) { // Tắm bt (01),
				$info_customer['date-value'] = $data['date-value'];
			}
			if (isset($data['date-value'])) {
				$info_customer['date-value'] = $data['date-value'];
			} elseif (isset($data['plan_date_start-value'])){
				$info_customer['date-value'] = $data['plan_date_start-value'];
			} elseif (isset($data['date-value-new'])){
				$info_customer['date-value'] = $data['date-value-new'];
			};
			if (isset($data['time'])) {
				$info_customer['time_first_booking'] = $data['time'];
			}
		}
		$uuid = \Str::uuid();
		$info_customer['info']["$uuid"] = $data;
		//Log::debug($info_customer);
		$request->session()->put($this->session_info,$info_customer);
	}
	public function confirm(Request $request){
		$data['customer'] = $this->get_booking($request);
		if (count($data['customer']) == 0) {
			return redirect()->route('home');
		}
		// dd($data);
		$data['customer']['info'] = array_values($data['customer']['info']);
		$request->session()->put($this->session_html, view('sunsun.front.confirm',$data)->render());
		return view('sunsun.front.confirm',$data);
	}
	public function payment(Request $request){
		$data = $request->all();
		$data['customer'] = $this->get_booking($request);
		if(\Auth::check()){
			$data['auth_username'] = \Auth::user()->username;
			$data['auth_email'] = \Auth::user()->email;
			$data['auth_tel'] = \Auth::user()->tel;
		}
		if($data['customer'] == null) {
			return redirect()->route('home');
		}
		$this->make_bill($data);
		//Log::debug('$data');
		//Log::debug($data);
		$request->session()->put($this->session_price, $data['total']);
		//Log::debug($this->session_price);
		$check_using_coupon = false;
		foreach ($data['customer']['info'] as $value) {
			if(json_decode($value['course'], true)['kubun_id'] == '01'){
				$check_using_coupon = true;
			}
		}
		$data['check_using_coupon'] = $check_using_coupon;
		$request->session()->put($this->payment_html, view('sunsun.front.payment',$data)->render());
		return view('sunsun.front.payment',$data);
	}
	public function make_bill (&$data) {
		//dd($data);
		$info_booking = $data['customer'];
		$bill = [];
		$bill['course_1']['name'] = "酵素浴";
		$bill['course_1']['quantity'] = 0;
		$bill['course_1']['price'] = 0;
		$bill['course_2']['name'] = "朝からリフレッシュコース";
		$bill['course_2']['quantity'] = 0;
		$bill['course_2']['price'] = 0;
		$bill['course_3']['name'] = "酵素部屋１部屋貸切コース";
		$bill['course_3']['quantity'] = 0;
		$bill['course_3']['price'] = 0;
		$bill['course_4']['name'] = "断食コース（初めて）";
		$bill['course_4']['quantity'] = 0;
		$bill['course_4']['price'] = 0;
		$bill['course_6']['name'] = "断食コース（リピート）";
		$bill['course_6']['quantity'] = 0;
		$bill['course_6']['price'] = 0;
		$bill['course_5']['name'] = "ペット酵素浴";
		$bill['course_5']['quantity'] = 0;
		$bill['course_5']['price'] = 0;
		$bill['course_7']['name'] = "お昼からのスイーツコース";
		$bill['course_7']['quantity'] = 0;
		$bill['course_7']['price'] = 0;
		$bill['course_8']['name'] = "美肌コース";
		$bill['course_8']['quantity'] = 0;
		$bill['course_8']['price'] = 0;
		$bill['course_9']['name'] = "免疫力アップコース";
		$bill['course_9']['quantity'] = 0;
		$bill['course_9']['price'] = 0;
		$bill['course_10']['name'] = "お昼からリフレッシュコース";
		$bill['course_10']['quantity'] = 0;
		$bill['course_10']['price'] = 0;
		$bill['options'] = [];
		$bill['price_option'] = 0;
		$new_bill = [];
		$kubun_type_price = \Helper::getKubunTypePrice($info_booking['date-value']);
		$sort_no_temp = MsKubun::whereRaw("kubun_type = '$kubun_type_price'")->orderBy('sort_no', 'ASC')->get();
		foreach ($sort_no_temp as $row) {
			$new_bill[$row->sort_no]['name'] = $row->notes;
			$new_bill[$row->sort_no]['unit'] = preg_replace('/[0-9]/', '', $row->kubun_value);
			$new_bill[$row->sort_no]['price'] = 0;
			$new_bill[$row->sort_no]['quantity'] = 0;
		}
		foreach ($info_booking['info'] as $booking) {
			if((!isset($booking['fake_booking'])) || ($booking['fake_booking'] != '1')){
				$booking_course = json_decode($booking['course'], true);
				//Log::debug($booking);
				if($booking_course['kubun_id'] == '01'){
					foreach($booking['time'] as $booking_t){
						if(isset($booking['age_type']) && $booking['age_type'] == 3){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','01']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($booking['age_type']) && $booking['age_type'] == 2){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','02']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($booking['age_type']) && $booking['age_type'] == 1){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','03']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($booking['age_type']) && $booking['age_type'] == 30){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','30']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($booking['age_type']) && $booking['age_type'] == 31){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','31']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}
					}
				}else if($booking_course['kubun_id'] == '02'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','05']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($booking_course['kubun_id'] == '07'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','21']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($booking_course['kubun_id'] == '08'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','22']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($booking_course['kubun_id'] == '09'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','23']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($booking_course['kubun_id'] == '10'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','24']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($booking_course['kubun_id'] == '03'){
					$quantity = json_decode( $booking['service_guest_num'], true);
					$number_customer = (int)$quantity['notes'];
					//Log::debug('$number_customer');
					//Log::debug($number_customer);
					// Lon hon 3 nguoi thi tinh them phu phi
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','06']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
					if($number_customer > 3){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','07']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += ($number_customer - 3) * $this->get_price_course($booking, $bill, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $number_customer - 3;
					}
				}else if($booking_course['kubun_id'] == '04' || $booking_course['kubun_id'] == '06'){ // 2020/06/05
					//Bang 5 ngay thi tinh rieng
					$day = count($booking['date']);
					if($day < 5){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','08']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += $day *  $this->get_price_course($booking, $bill);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $day;
					}else if($day === 5){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','09']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] ++;
					}
				}else if($booking_course['kubun_id'] == '05'){
					$quantity = json_decode( $booking['service_pet_num'], true);
					$pet_number = (int)$quantity['notes'];
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','16']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course($booking, $bill);
					$new_bill[$sort_no_temp->sort_no]['quantity'] ++;
					if($pet_number > 1){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','17']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += ($pet_number - 1) * $this->get_price_course($booking, $bill, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $pet_number - 1;
					}
				}
				$this->get_price_option($booking, $new_bill);
				//Log::debug($booking);
				//dd($info_booking['info']);
			}
		}
		//Log::debug('$new_bill');
		//Log::debug($new_bill);
		//dd($bill);
		$total = 0;
		foreach ($new_bill as $bi){
			$total += $bi['price'];
		}
		$data['total'] = $total;
		$data['new_bill'] = $new_bill;
	}
	public function get_free_holiday($year = null) {
		$date_selected = [];
		$where_year = "";
		if (!empty($year))
			$where_year = " and SUBSTR(date_holiday,1,4) = '$year'";
		$range_day = DB::select("
			SELECT  date_holiday
			FROM    ms_holiday
			WHERE   time_holiday is null and type_holiday is null $where_year
		");
		if (!empty($range_day)) {
			foreach($range_day as $da){
				array_push($date_selected, Carbon::parse($da->date_holiday)->format('Y/m/d'));
			}
		}
		return $date_selected;
	}
	public function get_free_holiday_acom($year = null) {
		$date_selected = [];
		$where_year = "";
		if (!empty($year))
			$where_year = " WHERE SUBSTR(date_holiday,1,4) = '$year'";
		$range_day = DB::select("
			SELECT  date_holiday
			FROM    ms_holiday_acom
			$where_year
		");
		if (!empty($range_day)) {
			foreach($range_day as $da){
				array_push($date_selected, Carbon::parse($da->date_holiday)->format('Y/m/d'));
			}
		}
		return $date_selected;
	}
	public function get_free_holiday_hotel($time_holiday = null) {
		$date_selected = [];
		$where_year = "";
		if (!empty($time_holiday))
			$where_time_holiday = " WHERE time_holiday = '$time_holiday'";
		$range_day = DB::select("
			SELECT  date_holiday
			FROM    ms_holiday
			$where_time_holiday
		");
		if (!empty($range_day)) {
			foreach($range_day as $da){
				array_push($date_selected, Carbon::parse($da->date_holiday)->format('Y/m/d'));
			}
		}
		return $date_selected;
	}
	public function get_free_room(Request $request){
		$data = $request->all();
		$room_type = $data['room'];
		$range_day = DB::select("
		SELECT  main.stay_checkin_date,
				main.stay_checkout_date
		FROM    tr_yoyaku main
		WHERE   main.stay_room_type = '$room_type'
		AND main.history_id IS NULL
		AND main.del_flg IS NULL
		");
		$date_selected = $this->get_free_holiday();
		$date_selected_acom = $this->get_free_holiday_acom();
		$date_selected_hotel = $this->get_free_holiday_hotel($data['room']);
		$date_selected = array_merge($date_selected,$date_selected_acom);
		$date_selected = array_merge($date_selected,$date_selected_hotel);
		$date_selected_end = $date_selected;
		foreach($range_day as $da){
			foreach($this->get_list_days($da->stay_checkin_date, $da->stay_checkout_date) as $d){
				array_push($date_selected, $d);
			}
			foreach($this->get_list_days_end($da->stay_checkin_date, $da->stay_checkout_date) as $d){
				array_push($date_selected_end, $d);
			}
		}
		return [
			'now' => $this->get_nearly_active(count($date_selected)!=0?max($date_selected):null, $date_selected),
			'date_selected' => $date_selected,
			'date_selected_end' => $date_selected_end
		];
	}
	private function get_nearly_active($max_date_selected, $date_selected){
		$now = Carbon::now();
		$active_day = Carbon::parse($max_date_selected);
		for($d = $now; $d->lte($active_day->addDay(7)); $d->addDay(1)) {
			if(!in_array($d->format('Y/m/d'), $date_selected) && ($d->dayOfWeek != 3) && ($d->dayOfWeek != 4)){
				return $d->format('Y/m/d');
			}
		}
	}
	private function get_list_days($from , $to){
		$from = Carbon::parse($from);
		$to = Carbon::parse($to)->add(-1, 'day');
		$dates = [];
		for($d = $from; $d->lte($to); $d->addDay()) {
			$dates[] = $d->format('Y/m/d');
		}
		return $dates;
	}
	private function get_list_days_end($from , $to){
		$from = Carbon::parse($from)->add(+1, 'day');
		$to = Carbon::parse($to);
		$dates = [];
		for($d = $from; $d->lte($to); $d->addDay()) {
			$dates[] = $d->format('Y/m/d');
		}
		return $dates;
	}
	public function get_price_option ($booking, &$new_bill) {
		$date_value_kbn = !empty($booking['date-value']) ? $booking['date-value'] :
			(!empty($booking['plan_date_start-value']) ? $booking['plan_date_start-value'] :
				(!empty($booking['date-value-new']) ? $booking['date-value-new'] : NULL)
			)
		;
		$kubun_type_price = \Helper::getKubunTypePrice($date_value_kbn);
		$course = json_decode($booking['course'], true);
		$c_id = $course['kubun_id'];
		$check_46 = ($c_id == "04" || $c_id == "06") ? true : false;
		$check_46_5 = false;
		if ($check_46) {
			if (count($booking['date']) >= 5)
				$check_46_5 = true;
		}
		if (isset($booking['lunch']) || isset($booking['lunch_guest_num'])) { // an trua 01
			$price_luch = 0;
			if (isset($booking['lunch'])) {
				$lunch = json_decode($booking['lunch'], true);
			} else { // th book all room 03
				$lunch = json_decode($booking['lunch_guest_num'], true);
			}
			if ($lunch['kubun_id'] !== "01") {
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','04']])->get()->first();
				$price_lunch_each_people = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$num_person = 1;
				if (isset($booking['lunch_guest_num'])) {
					$num_person = (int) $lunch['notes'];
				}
				$price_luch = $num_person * (int) $price_lunch_each_people;
				$new_bill[$course_price_op->sort_no]['price'] += $price_luch;
				$new_bill[$course_price_op->sort_no]['quantity'] += $num_person;
			}
		}
		if (isset($booking['stay_room_type'])) {
			$price_stay = 0;
			$stay_room_type = json_decode($booking['stay_room_type'], true);
			if ($stay_room_type['kubun_id'] !== '01') {
				if (isset($booking['stay_guest_num'])) {
					$stay_guest_num = json_decode($booking['stay_guest_num'], true);
					$date_end = new Carbon();
					$date_start = new Carbon();
					$date_end->setDate(substr($booking['range_date_end-value'], 0 , 4),substr($booking['range_date_end-value'], 4 , 2),substr($booking['range_date_end-value'], 6 , 2));
					$date_start->setDate(substr($booking['range_date_start-value'], 0 , 4),substr($booking['range_date_start-value'], 4 , 2),substr($booking['range_date_start-value'], 6 , 2));
					$all_dates = array();
					while ($date_start->lte($date_end)){
						$all_dates[] = $date_start->toDateString();
						if($date_start->dayOfWeek == Carbon::THURSDAY || $date_start->dayOfWeek == Carbon::TUESDAY) {
							$date_off[] = $date_start;
						}
						$date_start->addDay();
					}
					if ($stay_room_type['kubun_id'] === '02') {
						if ($stay_guest_num['notes'] == 1) {
							$kb_id = ($check_46 && $check_46_5) ? '25' : '10';
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates);
						}else{
							$kb_id = ($check_46 && $check_46_5) ? '26' : '11';
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_people = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $room_a_people;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates);
						}
					} else if ($stay_room_type['kubun_id'] === '03') {
						if ($stay_guest_num['notes'] == 1) {
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','12']])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates);
						}else{
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','13']])->get()->first();
							$room_a_people = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $room_a_people;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates);
						}
					} else if ($stay_room_type['kubun_id'] === '04') {
						if ($stay_guest_num['notes'] == 1) {
							$kb_id = ($check_46 && $check_46_5) ? '27' : '14';
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates);
						}
					}
					if (isset($booking['breakfast'])) {
						$breakfast = json_decode($booking['breakfast'], true);
						if ($breakfast['kubun_id'] !== '01') {
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','15']])->get()->first();
							$morning_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$new_bill[$course_price_op->sort_no]['price'] += count($all_dates) * $stay_guest_num['notes'] * $morning_price;
							$new_bill[$course_price_op->sort_no]['quantity'] += count($all_dates) * $stay_guest_num['notes'];
						}
					}
				}
			}
		}
		if (isset($booking['pet_keeping'])) {
			$price_keep_pet = 0;
			$pet_keeping = json_decode($booking['pet_keeping'], true);
			if ($pet_keeping['kubun_id'] !== '01') {
				$turn_number = 1;
				$course_price_op = [];
				if($course['kubun_id'] == '02'){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','19']])->get()->first();
				}else if($course['kubun_id'] == '07'){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','21']])->get()->first();
				}else if($course['kubun_id'] == '08'){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','22']])->get()->first();
				}else if($course['kubun_id'] == '09'){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','23']])->get()->first();
				}else if($course['kubun_id'] == '10'){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','24']])->get()->first();
				}else {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
				}
				if($course['kubun_id'] == '01'){
					$turn_number = count($booking['time']);
				}else if($course['kubun_id'] == '04' || $course['kubun_id'] == '06'){ // 2020/06/05
					$turn_number = 2 * count($booking['date']);
				}
				$price_keep_pet = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$new_bill[$course_price_op->sort_no]['price'] += $turn_number * $price_keep_pet;
				$new_bill[$course_price_op->sort_no]['quantity'] += $turn_number;
			}
		}
		if (isset($booking['whitening'])) {
			$price_white = 0;
			$whitening = json_decode($booking['whitening'], true);
			if ($whitening['kubun_id'] !== '01') {
				$number_customer = 1;
				if($course['kubun_id'] == '03'){
					$quantity = json_decode( $booking['service_guest_num'], true);
					$number_customer = (int)$quantity['notes'];
				}
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','20']])->get()->first();
				$price_white = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$new_bill[$course_price_op->sort_no]['price'] += $price_white * $number_customer;
				$new_bill[$course_price_op->sort_no]['quantity'] += $number_customer;
			}
		}
		if (isset($booking['whitening2'])) {
			$price_white = 0;
			$whitening = json_decode($booking['whitening2'], true);
			if ($whitening['kubun_id'] !== '01') {
				$number_customer = 1;
				if($course['kubun_id'] == '03'){
					$quantity = json_decode( $booking['service_guest_num'], true);
					$number_customer = (int)$quantity['notes'];
				}
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','28']])->get()->first();
				$price_white = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$new_bill[$course_price_op->sort_no]['price'] += $price_white * $number_customer;
				$new_bill[$course_price_op->sort_no]['quantity'] += $number_customer;
			}
		}
		if (isset($booking['core_tuning'])) {
			$price_white = 0;
			$whitening = json_decode($booking['core_tuning'], true);
			if ($whitening['kubun_id'] !== '01') {
				$number_customer = 1;
				if($course['kubun_id'] == '03'){
					$quantity = json_decode( $booking['service_guest_num'], true);
					$number_customer = (int)$quantity['notes'];
				}
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','29']])->get()->first();
				$price_white = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$new_bill[$course_price_op->sort_no]['price'] += $price_white * $number_customer;
				$new_bill[$course_price_op->sort_no]['quantity'] += $number_customer;
			}
		}
	}
	public function get_price_course ($booking, &$bill, $overflow = false) {
		$date_value_kbn = !empty($booking['date-value']) ? $booking['date-value'] :
			(!empty($booking['plan_date_start-value']) ? $booking['plan_date_start-value'] :
				(!empty($booking['date-value-new']) ? $booking['date-value-new'] : NULL)
			)
		;
		$kubun_type_price = \Helper::getKubunTypePrice($date_value_kbn);
		$course = json_decode($booking['course'], true);
		$course_price = 0;
		switch ($course['kubun_id']){
			case '01': // bình thường
				$course_price_op = null;
				if ($booking['age_type'] == '3') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','01']])->get()->first();
				}
				else if ($booking['age_type'] == '2') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','02']])->get()->first();
				}
				else if ($booking['age_type'] == '1') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','03']])->get()->first();
				}
				else if ($booking['age_type'] == '30') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','30']])->get()->first();
				}
				else if ($booking['age_type'] == '31') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','31']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '02': // 1 day refresh
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','05']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '03': // nguyên sàn
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','06']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','07']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '04': case '06': // ăn kiêng // 2020/06/05
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','08']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','09']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case "05": // pet
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','16']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','17']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '07':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','21']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '08':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','22']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '09':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','23']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '10': // 1 day refresh noon
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','24']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
		}
		return $course_price;
	}
	public function make_payment(Request $request){
		$data = $request->all();
		//Log::debug('payment_data');
		//Log::debug($data);
		// end convert name
		$error = $this->validate_payment_info($data);
		if (isset($error['error']) && (count($error['error']) != 0)){
			return $error;
		}
		$data['customer'] = $this->get_booking($request);
		if(
			(isset($data['customer']['info']) == false)
			|| ((isset($data['customer']['info'])) &&(count($data['customer']['info']) == 0))
		){
			return [
				'status' => 'error',
				'message' => '予約されたデータが見つかりません。'
			];
		}
		// convert name
		$data["name"] = $this->change_value_kana($data["name"]);
		$value = 'success';
		$message = null;
		$success =[
			'status' => $value,
			'message' => $message
		];
		$result = $this->update_or_new_booking($data, $request);
		//Log::debug('$result');
		//Log::debug($result);
		return isset($result)?$result:$success;
	}
	private function check_valid_email($email){
		$re = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/Dm';
		preg_match_all($re, $email, $matches, PREG_SET_ORDER, 0);
		//Log::debug('$matches');
		//Log::debug($matches);
		if(count($matches) > 0){
			return true;
		}
		return false;
	}
	public function check_is_katakana($name) {
		$name = $this->remove_space($name);
		$check_name = preg_replace("/[^ァ-ヾｧ-ﾝﾞﾟヽ゛゜ー]/u", "", $name);
		//Log::debug('check_name');
		//Log::debug($check_name);
		//Log::debug($name);
		if(strlen($check_name) == strlen($name)){
			return true;
		}
		return false;
	}
	/*public function check_is_empty($name) {
		return (preg_match('/( )|(　)/', $name) > 0) ? true : false;
	}*/
	public function validate_payment_info(&$data, $booking_id = NULL){
		$error = [];
		if(($data['name'] == null) || (!$this->check_is_katakana($data['name']))){
			$error['error'][] = 'name';
		}else{
			$error['clear'][] = 'name';
		}
		if($data['phone'] == null){
			$error['error'][] = 'phone';
		}else{
			$phone = preg_replace("/[^0-9]/", "", $data['phone'] );
			if((strlen($phone) > 11) || (strlen($phone) < 10)){
				$error['error'][] = 'phone';
			}else{
				$error['clear'][] = 'phone';
			}
		}
		if(($data['email'] == null)||(!$this->check_valid_email($data['email']))){
			$error['error'][] = 'email';
		}else{
			$error['clear'][] = 'email';
		}
		return $error;
	}
	public function update_or_new_booking($data, $request, $from_admin = false, $ref_booking_id = null, $send_mail = false){
		//Log::debug(count($data['customer']['info']));
		$result = [];
		// check time off def
		$off_def = "" ;
		if (isset($data["customer"]["date-value"])) {
			$check = $this->validate_holyday_def($data["customer"]["date-value"]);
			if (!$check) {
				$result_arr = [
					'status' => 'error',
					'message' => '申し訳ありません。別の日時で予約してください。'
				];
				$request->session()->forget($this->session_info);
				return $result_arr;
			}
		}
		//Update
		if(isset($data['booking_id'])){
			try{
				// Tạo booking_id mới để gán cho quan hệ người đi cùng và lịch sử
				$booking_id = $this->get_booking_id();
				Yoyaku::where('booking_id', $data['booking_id'])->update(['history_id' => $booking_id]);
				Yoyaku::where('ref_booking_id', $data['booking_id'])->update(['ref_booking_id' => $booking_id]);
				Yoyaku::where('history_id', $data['booking_id'])->update(['history_id' => $booking_id]);
				$name = isset($data['name'])?$data['name']:null;
				$phone = isset($data['phone'])?$data['phone']:null;
				$email = isset($data['email'])?$data['email']:null;
				$payment_method = isset($data['payment-method'])?$data['payment-method']:null;
				if(isset($data['ref_booking_id']) === false) {
					Yoyaku::where('ref_booking_id', $booking_id)
					->update(['name' => $name, 'phone' => $phone, 'email' => $email, 'payment_method' => $payment_method]);
				} else {
					Yoyaku::where('booking_id', $data['ref_booking_id'])
					->update(['name' => $name, 'phone' => $phone, 'email' => $email, 'payment_method' => $payment_method]);
					Yoyaku::where('ref_booking_id', $data['ref_booking_id'])
					->update(['name' => $name, 'phone' => $phone, 'email' => $email, 'payment_method' => $payment_method]);
				}
				// Check thời gian xe bus và thời gian của các hành khách đi cùng có hợp lệ không.
				$this->new_booking($data, $request, $send_mail, $from_admin, $booking_id, $data['booking_id'], $ref_booking_id);
			} catch (\Exception $failed) {
				// Nếu lưu yoyaku lỗi thì clear booking_id và history_id
				Yoyaku::where('booking_id', $data['booking_id'])->update(['history_id' => null]);
				Yoyaku::where('history_id', $data['booking_id'])->update(['history_id' => null]);
			}
		//New
		}else{
			//Log::debug('$request booking');
			//Log::debug($request);
			$result = $this->new_booking($data, $request, $send_mail, $from_admin);
			//Log::debug("new booking");
			//Log::debug($result);
		}
		if(isset($result['bookingID'])){
			$result = [
				'status' => 'success',
				'message' => $result
			];
		} else if(isset($result)){
			//Log::debug('$result');
			//Log::debug($result);
			$result_arr = [
				'status' => 'error'
			];
			if($result == 'booking_error'){
				$request->session()->forget($this->session_info);
				$result_arr['message'] = '申し訳ありません。別の日時で予約してください。';
			}else if($result == 'payment_error'){
				$result_arr['message'] = '支払処理が失敗しました。';
			}else if($result == 'booking_error_holiday'){
				$request->session()->forget($this->session_info);
				$result_arr['message'] = '定休日が含まれているため予約できません。';
			}else if($result == 'booking_error_stay'){
				$request->session()->forget($this->session_info);
				$result_arr['message'] = '選択された日は予約できません。';
			}
			$this->add_column_null($this->get_booking_id());
			$result = $result_arr;
		}
		return  $result;
	}
	private function new_booking($data, $request, $send_mail = false, $from_admin = false, $booking_id = 0, $old_booking_id = null, $ref_booking_id = null){
		$created_at_old = null;
		if (!empty($old_booking_id)) {
			$data_old = Yoyaku::where('booking_id', $data['booking_id'])->get();
			$created_at_old = $data_old[0]->created_at;
		}
		$parent = true;
		$parent_id = NULL;
		$parent_date = NULL;
		$result = [];
		$return_booking_id = null;
		$return_date = null;
		$email = null;
		DB::unprepared("LOCK TABLE tr_yoyaku READ, tr_yoyaku_danjiki_jikan READ");
		try{
			$email = trim($data['email']);
			//Log::debug('TRACE BEFORE' . $email);
			DB::beginTransaction();
			if(isset($data['customer']['info']) == false){
				throw new \Exception('Course not found!');
			}
			//Log::debug('TRACE HAS CUSTOMER');
			try {
				foreach($data['customer']['info'] as $customer){
					//Log::debug('data number : '.$i);
					//Log::debug($customer);
					$Yoyaku = new Yoyaku;
					if($booking_id == 0){
						$booking_id = $this->get_booking_id();
					}
					//Log::debug('TRACE BOOKING_ID: ' . $booking_id);
					if($parent){
						//Log::debug('parent');
						$parent_id = $booking_id;
						//Log::debug('$ref_booking_id');
						//Log::debug($ref_booking_id);
						$Yoyaku->booking_id = $booking_id;
						$Yoyaku->ref_booking_id = isset($ref_booking_id)?$ref_booking_id:NULL;
						$return_booking_id = $parent_id;
						$parent_date = isset($customer['date-value']) ? $customer['date-value'] :
							(isset($customer['plan_date_start-value']) ? $customer['plan_date_start-value'] :
								(isset($customer['date-value-new']) ? $customer['date-value-new'] : NULL)
							)
						;
						$return_date = $parent_date;
						//Log::debug('set_booking_course ' . $return_booking_id);
						$this->set_booking_course($Yoyaku, $data, $customer,$parent, NULL);
						//Log::debug('set_yoyaku_danjiki_jikan ' . $return_booking_id);
						$this->set_yoyaku_danjiki_jikan($customer, $parent, $parent_id, $parent_date);
						$parent = false;
						//Log::debug('TRACE SET PARENT');
					}else{
						//Log::debug('di kem');
						//Log::debug($parent_date);
						if(isset($customer['fake_booking'])){
							// $return_booking_id = $booking_id;
							$Yoyaku->booking_id = $booking_id;
						}else{
							$booking_id = $this->get_booking_id();
							// $return_booking_id = $booking_id;
							$Yoyaku->booking_id = $booking_id;
						}
						$Yoyaku->ref_booking_id = $parent_id;
						//Log::debug('set_booking_course ' . $return_booking_id);
						$this->set_booking_course($Yoyaku, $data, $customer,$parent, $parent_date);
						//Log::debug('set_yoyaku_danjiki_jikan ' . $return_booking_id);
						$this->set_yoyaku_danjiki_jikan($customer, $parent, $booking_id, $parent_date);
						//Log::debug('finish set_yoyaku_danjiki_jikan');
					}
					if(\Auth::check()){
						$Yoyaku->ms_user_id = \Auth::user()->ms_user_id;
						//Log::debug('TRACE USER_ID' . \Auth::user()->ms_user_id);
					}
					if ($created_at_old != null) {
						$Yoyaku->created_at = $created_at_old;
					}
					$Yoyaku->save();
					//Log::debug('TRACE SAVE YOYAKU');
				}
				//Log::debug('TRACE CHECK PAYMENT');
				if($from_admin === false){
					$result = $this->call_payment_api($request, $data, $return_booking_id, $old_booking_id);
				}
				//Log::debug('TRACE FINISH PAYMENT');
				$bill_text = null;
				$admin_data = null;
				$admin_price = null;
				if ($return_booking_id != null) {
					$result_save = $this->save_tr_payments_history($return_booking_id);
					$bill_text = $result_save["bill_text"];
					$admin_data = $result_save["admin_data"];
					$admin_price = $result_save["admin_price"];
				}
				DB::commit();
				//Log::debug('$request before send mail');
				//Log::debug($request);
				//Log::debug('TRACE CHECK SENDMAIL');
				if(($send_mail === true) || ($from_admin === false)){
					$this->send_email($request, $data, $return_booking_id, $return_date, $email, $from_admin, $admin_data, $bill_text, $admin_price);
				}
				//Log::debug('TRACE FINISH SENDMAIL');
			} catch (\Exception $e1) {
				//Log::debug("before error");
				DB::rollBack();
				$this->add_column_null($return_booking_id);
				DB::unprepared("UNLOCK TABLE");
				//Log::debug('$e1->getMessage()');
				//Log::debug($e1->getMessage());
				$result = $e1->getMessage();
				return  $result;
			}
		}catch(\Exception $e2){
			//Log::debug('$e2->getMessage()');
			//Log::debug($e2->getMessage());
		}
		DB::unprepared("UNLOCK TABLE");
		//Log::debug($result);
		return  $result;
	}
	private function send_email($request, $data, $booking_id, $return_date, $email, $from_admin, $admin_data = null, $bill_text = null, $admin_price = null){
		$end = Carbon::createFromFormat('Ymd h:i:s', $return_date."09:00:00")->subDays(2);
		//Log::debug('time end');
		//Log::debug($end->toDateTimeString());
		$start = Carbon::now();
		//Log::debug('time start');
		//Log::debug($start->toDateTimeString());
		//Log::debug('time sub');
		$delay_time = 3;
		if($end->gt($start)){
			$delay_time = $start->diffInMinutes($end);
		}
		$check_has_note = false;
		$check_has_couse_oneday = false;
		$check_note_mail = false;
		if($from_admin === false){
			$booking_data = new \stdClass();
			$booking_data->booking_id = $booking_id;
			$booking_data->booking_data = $data;
			$booking_data->booking_html = $request->session()->get($this->session_html);
			$booking_data->payment_html = $request->session()->get($this->payment_html);
			foreach ($data['customer']['info'] as $value) {
				if(
					(json_decode($value['course'] , true)['kubun_id'] === '01')
					||(json_decode($value['course'] , true)['kubun_id'] === '02')
					||(json_decode($value['course'] , true)['kubun_id'] === '07')
					||(json_decode($value['course'] , true)['kubun_id'] === '10')
					||(json_decode($value['course'] , true)['kubun_id'] === '03')
					||(json_decode($value['course'] , true)['kubun_id'] === '04')
					||(json_decode($value['course'] , true)['kubun_id'] === '06') // 2020/06/05
				){
					$check_has_note = true;
				}
				if((json_decode($value['course'] , true)['kubun_id'] === '02')
					||(json_decode($value['course'] , true)['kubun_id'] === '07')
					||(json_decode($value['course'] , true)['kubun_id'] === '10')
					||(json_decode($value['course'] , true)['kubun_id'] === '04')
					||(json_decode($value['course'] , true)['kubun_id'] === '06')){ // 2020/06/05
					$check_has_couse_oneday = true;
				}
				if ((json_decode($value['course'] , true)['kubun_id'] === '04')) {
					$check_note_mail = true;
				}
			}
			$booking_data->check_has_note = $check_has_note;
			$booking_data->check_has_couse_oneday = $check_has_couse_oneday;
			$booking_data->check_note_mail = $check_note_mail;
			ConfirmJob::dispatch($email, $booking_data);
			ReminderJob::dispatch($email, $booking_data)->delay(now()->addMinutes($delay_time));
			$request->session()->forget($this->session_html);
			$request->session()->forget($this->payment_html);
		}else{
			$booking_data = new \stdClass();
			$booking_data->booking_id = $booking_id;
			$booking_data->booking_data = $data;
			$booking_data->booking_html = view('sunsun.front.confirm',$admin_data)->render();
			$booking_data->payment_text = $bill_text;
			$booking_data->check_has_note = $check_has_note;
			$booking_data->check_has_couse_oneday = $check_has_couse_oneday;
			$booking_data->check_note_mail = $check_note_mail;
			ConfirmJob::dispatch($email, $booking_data);
			ReminderJob::dispatch($email, $booking_data)->delay(now()->addMinutes($delay_time));
		}
	}
	public function yoyaku_2_bill($yoyaku, &$bill_text, &$admin_price){
		$kubun_type_price = \Helper::getKubunTypePrice($yoyaku[0]['service_date_start'],$yoyaku[0]['created_at']);
		$new_bill = [];
		$MsKubun = MsKubun::all();
		$sort_no_temp = MsKubun::whereRaw("kubun_type = '$kubun_type_price'")->orderBy('sort_no', 'ASC')->get();
		foreach ($sort_no_temp as $row) {
			$new_bill[$row->sort_no]['name'] = $row->notes;
			$new_bill[$row->sort_no]['unit'] = preg_replace('/[0-9]/', '', $row->kubun_value);
			$new_bill[$row->sort_no]['price'] = 0;
			$new_bill[$row->sort_no]['quantity'] = 0;
		}
		$bill_tmp = null;
		$check_new = true;
		foreach ($yoyaku as $key => $yo) {
			if((!isset($yo->fake_booking_flg)) || ($yo->fake_booking_flg != '1')){
				if($yo->course == '01'){
					$time = YoyakuDanjikiJikan::where('booking_id', $yo->booking_id)->get();
					foreach ($time as $t) {
						if(isset($yo->age_type) && ($yo->age_type == 3)){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','01']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($yo->age_type) && $yo->age_type == 2){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','02']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($yo->age_type) && $yo->age_type == 1){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','03']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($yo->age_type) && $yo->age_type == 30){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','30']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}else if(isset($yo->age_type) && $yo->age_type == 31){
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','31']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
							$new_bill[$sort_no_temp->sort_no]['quantity']++;
						}
					}
				}else if($yo->course == '02'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','05']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($yo->course == '03'){
					$quantity = $MsKubun->where('kubun_type','015')->where('kubun_id', $yo->service_guest_num)->first();
					$number_customer = (int)$quantity->notes;
					//Log::debug('$number_customer');
					//Log::debug($number_customer);
					// Lon hon 3 nguoi thi tinh them phu phi
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','06']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
					if($number_customer > 3){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','07']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += ($number_customer - 3) * $this->get_price_course_admin($yo, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $number_customer - 3;
					}
				}else if($yo->course == '04' || $yo->course == '06'){ // 2020/06/05
					$time = YoyakuDanjikiJikan::where('booking_id', $yo->booking_id)->get();
					$day = count($time);
					if($day < 5){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','08']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += $day *  $this->get_price_course_admin($yo);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $day;
					}else{
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','09']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] ++;
					}
				}else if($yo->course == '05'){
					$quantity = $MsKubun->where('kubun_type','016')->where('kubun_id', $yo->service_pet_num)->first();
					$pet_number = (int)$quantity->notes;
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','16']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity'] ++;
					if($pet_number > 1){
						$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','17']])->get()->first();
						$new_bill[$sort_no_temp->sort_no]['price'] += ($pet_number - 1) * $this->get_price_course_admin($yo, true);
						$new_bill[$sort_no_temp->sort_no]['quantity'] += $pet_number - 1;
					}
				}else if($yo->course == '07'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','21']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($yo->course == '08'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','22']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($yo->course == '09'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','23']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}else if($yo->course == '10'){
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','24']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $this->get_price_course_admin($yo);
					$new_bill[$sort_no_temp->sort_no]['quantity']++;
				}
				$this->get_price_option_admin($yo, $new_bill);
			}
			foreach ($new_bill as $key_bill => $row_bill) {
				if ($row_bill["price"] != 0) {
					if ($check_new
						|| ($bill_tmp != null && $row_bill["price"] != $bill_tmp[$key_bill]["price"]) ){
						if (isset($new_bill[$key_bill][config('const.db.tr_payments_history.YOYAKU_INDEX')]))
							$new_bill[$key_bill][config('const.db.tr_payments_history.YOYAKU_INDEX')] .= "," . $key;
						else
							$new_bill[$key_bill][config('const.db.tr_payments_history.YOYAKU_INDEX')] = $key;
					}
				}
			}
			if ($bill_tmp == null) {
				$bill_tmp = $new_bill;
				$check_new = false;
			}
		}
		$total = 0;
		foreach ($new_bill as $bi){
			$total += $bi['price'];
		}
		if($admin_price  !== null){
			$admin_price = $total;
		}
		// $request->session()->put($this->session_price_admin, $total);
		foreach($new_bill as $key => $n_bill){
			if($n_bill['quantity'] > 0){
				$bill_text .= $n_bill['name'] . "：" . $n_bill['quantity'].$n_bill['unit'] . " " . number_format($n_bill['price']) ."円
";
			}
		}
		$bill_text .= config('booking.total.label') . " " . $total ."円";
		return $new_bill;
	}
	public function get_price_course_admin ($booking, $overflow = false) {
		$kubun_type_price = \Helper::getKubunTypePrice($booking['service_date_start'],$booking['created_at']);
		$course = $booking->course;
		$course_price = 0;
		switch ($course){
			case '01': // bình thường
				$course_price_op = null;
				if ($booking->age_type == '3') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','01']])->get()->first();
				}
				else if ($booking->age_type == '2') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','02']])->get()->first();
				}
				else if ($booking->age_type == '1') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','03']])->get()->first();
				}
				else if ($booking->age_type == '30') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','30']])->get()->first();
				}
				else if ($booking->age_type == '31') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','31']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '02': // 1 day refresh
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','05']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '03': // nguyên sàn
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','06']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','07']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '04': case '06': // ăn kiêng // 2020/06/05
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','08']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','09']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case "05": // pet
				$course_price_op = null;
				if($overflow === false){
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','16']])->get()->first();
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','17']])->get()->first();
				}
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '07':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','21']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '08':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','22']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '09':
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','23']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
			case '10': // 1 day refresh noon
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','24']])->get()->first();
				$course_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				break;
		}
		return $course_price;
	}
	public function get_price_option_admin ($booking, &$new_bill) {
		$kubun_type_price = \Helper::getKubunTypePrice($booking['service_date_start'],$booking['created_at']);
		$course = $booking->course;
		$check_46 = ($course == "04" || $course == "06") ? true : false;
		$check_46_5 = false;
		if ($check_46) {
			$time = YoyakuDanjikiJikan::where('booking_id', $booking->booking_id)->get();
			$check_46_5 = count($time) < 5 ? false : true;
		}
		if (isset($booking->lunch) || isset($booking->lunch_guest_num)) { // an trua 01
			$price_luch = 0;
			if (isset($booking->lunch)) {
				$lunch = $booking->lunch;
			} else { // th book all room 03
				$lunch = $booking->lunch_guest_num;
			}
			if ($lunch !== "01") {
				$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','04']])->get()->first();
				$price_lunch_each_people = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
				$num_person = 1;
				if (isset($booking->lunch_guest_num)) {
					$lunch_guest_num = MsKubun::where('kubun_type','023')->where('kubun_id', $booking->lunch_guest_num)->first();
					$num_person = (int) $lunch_guest_num->notes;
				}
				$price_luch = $num_person * (int) $price_lunch_each_people;
				$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','04']])->get()->first();
				$new_bill[$sort_no_temp->sort_no]['price'] += $price_luch;
				$new_bill[$sort_no_temp->sort_no]['quantity'] += $num_person;
			}
		}
		if (isset($booking->stay_room_type)) {
			$price_stay = 0;
			$stay_room_type = $booking->stay_room_type;
			if ($stay_room_type !== '01') {
				if (isset($booking->stay_guest_num)) {
					$stay_guest_num = MsKubun::where('kubun_type','012')->where('kubun_id', $booking->stay_guest_num)->first();
					$date_end = new Carbon();
					$date_start = new Carbon();
					$date_end->setDate(substr($booking->stay_checkout_date, 0 , 4),substr($booking->stay_checkout_date, 4 , 2),substr($booking->stay_checkout_date, 6 , 2));
					$date_start->setDate(substr($booking->stay_checkin_date, 0 , 4),substr($booking->stay_checkin_date, 4 , 2),substr($booking->stay_checkin_date, 6 , 2));
					$all_dates = array();
					while ($date_start->lte($date_end)){
						$all_dates[] = $date_start->toDateString();
						if($date_start->dayOfWeek == Carbon::THURSDAY || $date_start->dayOfWeek == Carbon::TUESDAY) {
							$date_off[] = $date_start;
						}
						$date_start->addDay();
					}
					if ($stay_room_type === '02') {
						if ($stay_guest_num->notes == 1) {
							$kb_id = ($check_46 && $check_46_5) ? '25' : '10';
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates);
						}else{
							$kb_id = ($check_46 && $check_46_5) ? '26' : '11';
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_people = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $room_a_people;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates);
						}
					} else if ($stay_room_type === '03') {
						if ($stay_guest_num->notes == 1) {
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','12']])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','12']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates);
						}else{
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','13']])->get()->first();
							$room_a_people = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','13']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $room_a_people;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates);
						}
					} else if ($stay_room_type === '04') {
						if ($stay_guest_num->notes == 1) {
							$kb_id = ($check_46 && $check_46_5) ? '27' : '14';
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id',$kb_id]])->get()->first();
							$room_a_alone = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $room_a_alone;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates);
						}
					}
					if (isset($booking->breakfast)) {
						$breakfast = $booking->breakfast;
						if ($breakfast == '02') {
							$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','15']])->get()->first();
							$morning_price = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
							$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','15']])->get()->first();
							$new_bill[$sort_no_temp->sort_no]['price'] += count($all_dates) * $stay_guest_num['notes'] * $morning_price;
							$new_bill[$sort_no_temp->sort_no]['quantity'] += count($all_dates) * $stay_guest_num['notes'];
						}
					}
				}
			}
		}
		if (isset($booking->pet_keeping)) {
			$price_keep_pet = 0;
			$pet_keeping = $booking->pet_keeping;
			if($course == '02' || $course == '10'){
				if ($pet_keeping !== '01') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','19']])->get()->first();
					$price_keep_pet = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','19']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_keep_pet;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += 1;
				}
			}else if($course == '01'){
				$time = YoyakuDanjikiJikan::where('booking_id', $booking->booking_id)->get();
				$turn_number = count($time);
				if ($pet_keeping !== '01') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$price_keep_pet = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $turn_number * $price_keep_pet;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += $turn_number;
				}
			}else if($course == '04' || $course == '06'){ // 2020/06/05
				$time = YoyakuDanjikiJikan::where('booking_id', $booking->booking_id)->get();
				$turn_number = count($time)*2;
				if ($pet_keeping !== '01') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$price_keep_pet = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $turn_number * $price_keep_pet;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += $turn_number;
				}
			}else{
				if ($pet_keeping !== '01') {
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$price_keep_pet = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','18']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_keep_pet;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += 1;
				}
			}
		}
		if (isset($booking->whitening)) { // tắm trắng 06
			$price_white = 0;
			$whitening = $booking->whitening;
			if ($whitening !== '01') {
				if($course == '03'){
					$quantity = MsKubun::where('kubun_type','015')->where('kubun_id', $booking->service_guest_num)->first();
					$number_customer = (int)$quantity->notes;
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','20']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','20']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white * $number_customer;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += $number_customer;
				}else{
					$course_price_op = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','20']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $course_price_op->kubun_value);
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','20']])->get()->first();
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += 1;
				}
			}
		}
		if (isset($booking->whitening2)) {
			$price_white = 0;
			$whitening = $booking->whitening2;
			if ($whitening !== '01') {
				if($course == '03'){
					$quantity = MsKubun::where('kubun_type','015')->where('kubun_id', $booking->service_guest_num)->first();
					$number_customer = (int)$quantity->notes;
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','28']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white * $number_customer;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += $number_customer;
				}else{
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','28']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += 1;
				}
			}
		}
		if (isset($booking->core_tuning)) {
			$price_white = 0;
			$core_tuning = $booking->core_tuning;
			if ($core_tuning !== '01') {
				if($course == '03'){
					$quantity = MsKubun::where('kubun_type','015')->where('kubun_id', $booking->service_guest_num)->first();
					$number_customer = (int)$quantity->notes;
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','29']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white * $number_customer;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += $number_customer;
				}else{
					$sort_no_temp = MsKubun::where([['kubun_type',$kubun_type_price],['kubun_id','29']])->get()->first();
					$price_white = preg_replace( '/[^0-9]/', '', $sort_no_temp->kubun_value);
					$new_bill[$sort_no_temp->sort_no]['price'] += $price_white;
					$new_bill[$sort_no_temp->sort_no]['quantity'] += 1;
				}
			}
		}
	}
	private function convert_booking_2_value($data, &$return){
		$MsKubun = MsKubun::all();
		foreach ($data as $key => $value) {
			$repeat_user = $MsKubun->where('kubun_type','001')->where('kubun_id', $value->repeat_user)->first();
			$return['admin_value_customer'][$key]['repeat_user'] = isset($repeat_user)?$repeat_user->kubun_value:null;
			$transport = $MsKubun->where('kubun_type','002')->where('kubun_id', $value->transport)->first();
			$return['admin_value_customer'][$key]['transport'] = isset($transport)?$transport->kubun_value:null;
			$bus_arrive_time_slide = $MsKubun->where('kubun_type','003')->where('kubun_id', $value->bus_arrive_time_slide)->first();
			$return['admin_value_customer'][$key]['bus_arrive_time_slide'] = isset($bus_arrive_time_slide)?$bus_arrive_time_slide->kubun_value:null;
			$return['admin_value_customer'][$key]['date-view'] = Carbon::createFromFormat('Ymd', $value->service_date_start)->format('Y/m/d');
			$gender = $MsKubun->where('kubun_type','006')->where('kubun_id', $value->gender)->first();
			$return['admin_value_customer'][$key]['gender'] = isset($gender)?$gender->kubun_value:null;
			$course = $MsKubun->where('kubun_type','005')->where('kubun_id', $value->course)->first();
			$return['admin_value_customer'][$key]['course'] = isset($course)?$course->kubun_value:null;
			$return['admin_value_customer'][$key]['time'] = YoyakuDanjikiJikan::where('booking_id', $value->booking_id)->get();
			$lunch = $MsKubun->where('kubun_type','008')->where('kubun_id', $value->lunch)->first();
			$return['admin_value_customer'][$key]['lunch'] = isset($lunch)?$lunch->kubun_value:null;
			$pet_keeping = $MsKubun->where('kubun_type','010')->where('kubun_id', $value->pet_keeping)->first();
			$return['admin_value_customer'][$key]['pet_keeping'] = isset($pet_keeping)?$pet_keeping->kubun_value:null;
			$stay_room_type = $MsKubun->where('kubun_type','011')->where('kubun_id', $value->stay_room_type)->first();
			$return['admin_value_customer'][$key]['stay_room_type'] = isset($stay_room_type)?$stay_room_type->kubun_value:null;
			$stay_guest_num = $MsKubun->where('kubun_type','012')->where('kubun_id', $value->stay_guest_num)->first();
			$return['admin_value_customer'][$key]['stay_guest_num'] = isset($stay_guest_num)?$stay_guest_num->kubun_value:null;
			if($value->breakfast == '02'){
				$breakfast = $MsKubun->where('kubun_type','022')->where('kubun_id', $value->breakfast)->first();
				$return['admin_value_customer'][$key]['breakfast'] = isset($breakfast)?$breakfast->kubun_value:null;
			}else{
				$return['admin_value_customer'][$key]['breakfast'] = null;
			}
			$service_guest_num = $MsKubun->where('kubun_type','015')->where('kubun_id', $value->service_guest_num)->first();
			$return['admin_value_customer'][$key]['service_guest_num'] = isset($service_guest_num)?$service_guest_num->kubun_value:null;
			$lunch_guest_num = $MsKubun->where('kubun_type','023')->where('kubun_id', $value->lunch_guest_num)->first();
			$return['admin_value_customer'][$key]['lunch_guest_num'] = isset($lunch_guest_num)?$lunch_guest_num->kubun_value:null;
			$return['admin_value_customer'][$key]['date'] = YoyakuDanjikiJikan::where('booking_id', $value->booking_id)->get();
			$service_pet_num = $MsKubun->where('kubun_type','016')->where('kubun_id', $value->service_pet_num)->first();
			$return['admin_value_customer'][$key]['service_pet_num'] = isset($service_pet_num)?$service_pet_num->kubun_value:null;
		}
	}
	private function add_column_null($booking_id){
		$Yoyaku = new Yoyaku;
		$Yoyaku->booking_id = $booking_id;
		$Yoyaku->course = 0;
		$Yoyaku->save();
	}
	private function call_payment_api($request, &$data, $booking_id, $old_booking_id = null){
		//Log::debug('TRACE OPEN PAYMENT API');
		if((isset($data['payment-method']) === true) && ($data['payment-method'] == 1)){
			//Log::debug('$old_booking_id');
			//Log::debug($old_booking_id);
			// $amount = 1;
			if ($request->session()->has($this->session_price)) {
				$amount = $request->session()->get($this->session_price);
			} else {
				throw new \ErrorException('Token error!');
			}
			if(isset($old_booking_id)){
				$accessID = null;
				$accessPass = null;
				$old_payment = Payment::where('booking_id', $old_booking_id)->first();
				if($old_payment){
					//Log::debug('change');
					$accessID = $old_payment->access_id;
					$accessPass = $old_payment->access_pass;
					$this->change_tran($accessID, $accessPass, $amount, $booking_id);
				}
			}else{
				if(!isset($data['Token'])){
					throw new \ErrorException('Token error!');
				}
				//Log::debug('TRACE OPEN TRAN API');
				return $this->create_tran($request, $booking_id, $amount, $data['Token']);
			}
		}else{
			return [
				'bookingID' => $booking_id
			];
		}
	}
	// Header payment
	protected $headers = array(
		'Connection' => 'keep-alive',
		'Pragma' => 'no-cache',
		'Cache-Control' => 'no-cache',
		'Accept' => 'text/plain, */*; q=0.01',
		'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36',
		'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
		'Sec-Fetch-Site' => 'cross-site',
		'Sec-Fetch-Mode' => 'cors',
		'Accept-Encoding' => 'gzip, deflate, br',
		'Accept-Language' => 'vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5,zh-CN;q=0.4,zh;q=0.3'
	);
	private function create_tran($request, $booking_id, $amount, $token){
		$data = array(
			'ShopID' =>  env("SHOP_ID"),
			'ShopPass' => env("SHOP_PASS"),
			'OrderID' => $booking_id,
			'Amount' => $amount,
			'JobCd' => 'CAPTURE'
		);
		//Log::debug('data');
		//Log::debug($data);
		$response = \Requests::post('https://p01.mul-pay.jp/payment/EntryTran.idPass', $this->headers, $data);
		parse_str($response->body, $params);
		if(!isset($params['AccessID']) || !isset($params['AccessPass'])){
			//Log::debug('Create tran body');
			//Log::debug($response->body);
			throw new \ErrorException('payment_error');
		}
		return $this->exec_tran($request, $params['AccessID'], $params['AccessPass'], $booking_id, $token);
	}
	private function exec_tran($request, $accessID, $accessPass, $booking_id, $token){
		$data = array(
			'AccessID' => $accessID,
			'AccessPass' => $accessPass,
			'OrderID' => $booking_id,
			'Method' => '1',
			'PayTimes' => '1',
			'Token' => $token
		);
		$response = \Requests::post('https://p01.mul-pay.jp/payment/ExecTran.idPass', $this->headers, $data);
		//Log::debug('Exec tran body');
		//Log::debug($response->body);
		parse_str($response->body, $params);
		if(isset($params['ACS']) && ($params['ACS'] == 0)){
			// Lưu lại access_id và access_pass dùng cho change price
			//Log::debug('$params');
			//Log::debug($params);
			$payment = new Payment();
			$payment->booking_id =  $booking_id;
			$payment->access_id = $accessID;
			$payment->access_pass = $accessPass;
			$payment->payment_id = $params['TranID'];
			$payment->save();
			$request->session()->forget($this->session_price);
			return [
				'tranID' => $params['TranID'],
				'bookingID' => $booking_id
			];
		}else{
			throw new \ErrorException('payment_error');
		}
	}
	private function change_tran($accessID, $accessPass, $amount, $booking_id){
		$data = array(
			'AccessID' => $accessID,
			'AccessPass' => $accessPass,
			'ShopID' => env("SHOP_ID"),
			'ShopPass' => env("SHOP_PASS"),
			'Amount' => $amount,
			'JobCd' => 'CAPTURE'
		);
		$response = \Requests::post('https://p01.mul-pay.jp/payment/ChangeTran.idPass', $this->headers, $data);
		//Log::debug('Exec tran body');
		parse_str($response->body, $params);
		//Log::debug('$params');
		//Log::debug($params);
		if(isset($params['AccessID'])){
			$payment = new Payment();
			$payment->booking_id =  $booking_id;
			$payment->access_id = $accessID;
			$payment->access_pass = $accessPass;
			$payment->payment_id = $params['TranID'];
			$payment->save();
			return [
				'tranID' => $params['TranID'],
				'bookingID' => $booking_id
			];
		}else{
			throw new \ErrorException('payment_error');
		}
	}
	private function set_yoyaku_danjiki_jikan($customer, $parent, $parent_id, $parent_date){
		$course = json_decode($customer['course']);
		if($course->kubun_id == '01'){
			$gender = json_decode($customer['gender']);
			foreach($customer['time'] as $time){
				$this->validate_course_human($gender->kubun_id, $parent_date, $time['value'], $time['bed']);
				$YoyakuDanjikiJikan = new YoyakuDanjikiJikan;
				$YoyakuDanjikiJikan->booking_id = $parent_id;
				$YoyakuDanjikiJikan->service_date = $parent_date;
				$YoyakuDanjikiJikan->service_time_1 = $time['value'];
				$YoyakuDanjikiJikan->notes = $time['bed'];
				$YoyakuDanjikiJikan->time_json = $time['json'];
				$YoyakuDanjikiJikan->save();
			}
		}elseif($course->kubun_id == '04' || $course->kubun_id == '06'){ // 2020/06/05
			$plan_date_start = isset($customer['plan_date_start-value'])?$customer['plan_date_start-value']:"";
			$plan_date_end = isset($customer['plan_date_end-value'])?$customer['plan_date_end-value']:"";
			$gender = json_decode($customer['gender']);
			foreach($customer['date'] as $index => $date){
				//Log::debug('course 4', $customer);
				$this->validate_course_human($gender->kubun_id, $date['day']['value'],  $date['from']['value'], $date['from']['bed']);
				$this->validate_course_human($gender->kubun_id, $date['day']['value'],  $date['to']['value'], $date['to']['bed']);
				$YoyakuDanjikiJikan = new YoyakuDanjikiJikan;
				$YoyakuDanjikiJikan->booking_id = $parent_id;
				$YoyakuDanjikiJikan->service_date = $date['day']['value'];
				$YoyakuDanjikiJikan->service_time_1 = $date['from']['value'];
				$YoyakuDanjikiJikan->service_time_2 = $date['to']['value'];
				$YoyakuDanjikiJikan->notes = $date['from']['bed'] . "-" . $date['to']['bed'];
				$YoyakuDanjikiJikan->time_json = $customer['time'][$index]['from']['json'] . "-" . $customer['time'][$index]['to']['json'];
				$YoyakuDanjikiJikan->save();
			}
		}
	}
	private function validate_course_human($gender, $date, $time, $bed){
		$course_1_2_4_validate = DB::select("
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				INNER JOIN      (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) time
				ON              main.booking_id = time.booking_id
				WHERE           main.course = '01'
				AND             main.gender = $gender
				AND             time.service_date = $date
				AND             time.service_time_1 = $time
				AND             time.notes = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '02'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             SUBSTRING(main.bed, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '02'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_2 = $time
				AND             SUBSTRING(main.bed, 3, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				INNER JOIN      (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) time
				ON              main.booking_id = time.booking_id
				WHERE           main.course = '04'
				AND             main.gender = $gender
				AND             time.service_date = $date
				AND             time.service_time_1 = $time
				AND             SUBSTRING(time.notes, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				INNER JOIN      (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) time
				ON              main.booking_id = time.booking_id
				WHERE           main.course = '04'
				AND             main.gender = $gender
				AND             time.service_date = $date
				AND             time.service_time_2 = $time
				AND             SUBSTRING(time.notes, 3, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				INNER JOIN      (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) time
				ON              main.booking_id = time.booking_id
				WHERE           main.course = '06'
				AND             main.gender = $gender
				AND             time.service_date = $date
				AND             time.service_time_1 = $time
				AND             SUBSTRING(time.notes, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				INNER JOIN      (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) time
				ON              main.booking_id = time.booking_id
				WHERE           main.course = '06'
				AND             main.gender = $gender
				AND             time.service_date = $date
				AND             time.service_time_2 = $time
				AND             SUBSTRING(time.notes, 3, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '07'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             SUBSTRING(main.bed, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '07'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_2 = $time
				AND             SUBSTRING(main.bed, 3, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '08'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             SUBSTRING(main.bed, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '09'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             SUBSTRING(main.bed, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '10'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             SUBSTRING(main.bed, 1, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
			UNION
			(
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '10'
				AND             main.gender = $gender
				AND             main.service_date_start = $date
				AND             main.service_time_2 = $time
				AND             SUBSTRING(main.bed, 3, 1) = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			)
		");
		if($gender == '01'){
			$course_3_validate = DB::select("
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '03'
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time
				AND             main.bed = $bed
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			");
		}
		if(
			(isset($course_1_2_4_validate) && (count($course_1_2_4_validate) != 0))
			|| (isset($course_3_validate) && (count($course_3_validate) != 0))
		){
			throw new \ErrorException('booking_error');
		}
	}
	private function set_booking_course(&$Yoyaku, $data, $customer,$parent, $parent_date){
		//Log::debug("start set_booking_course");
		//General payment
		$Yoyaku->name = isset($data['name'])?$data['name']:null;
		$Yoyaku->phone = isset($data['phone'])?$data['phone']:null;
		$Yoyaku->email = isset($data['email'])?$data['email']:null;
		$Yoyaku->payment_method = isset($data['payment-method'])?$data['payment-method']:null;
		//General info
		$repeat_user = isset($customer['repeat_user'])?json_decode($customer['repeat_user']):null;
		$transport = isset($customer['transport'])?json_decode($customer['transport']):null;
		$bus_arrive_time_slide = isset($customer['bus_arrive_time_slide'])?json_decode($customer['bus_arrive_time_slide']):null;
		$pick_up = isset($customer['pick_up'])?json_decode($customer['pick_up']):null;
		//Option
		$course = isset($customer['course'])?json_decode($customer['course']):null;
		$Yoyaku->repeat_user = $repeat_user->kubun_id;
		$Yoyaku->transport = $transport->kubun_id;
		if($transport->kubun_id == '02'){
			$Yoyaku->bus_arrive_time_slide = $bus_arrive_time_slide->kubun_id;
			$Yoyaku->pick_up = $pick_up->kubun_id;
		}
		$Yoyaku->course = $course->kubun_id;
		if($course->kubun_id == '01'){
			$this->set_course_1($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '02'){
			$this->set_course_2($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '03'){
			$this->set_course_3($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '04' || $course->kubun_id == '06'){ // 2020/06/05
			$this->set_course_4($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '05'){
			$this->set_course_5($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '07'){
			$this->set_course_7($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '08'){
			$this->set_course_8($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '09'){
			$this->set_course_9($parent, $parent_date, $customer, $Yoyaku);
		}elseif($course->kubun_id == '10'){
			$this->set_course_10($parent, $parent_date, $customer, $Yoyaku);
		}
		//Log::debug("--------set_booking_course-------");
	}
	private function isDate($date, $format = 'Y-m-d')
	{
		$d = Carbon::createFromFormat($format, $date);
		return $d && $d->format($format) === $date;
	}
	private function convertStringToDate($date)
	{
		return (strlen($date) >= 8) ? substr($date,0,4).'-'.substr($date,4,2).'-'.substr($date,6,2) : false;
	}
	private function validate_holyday_def($checkin) {
		if (empty($checkin)) return true;
		$begin = $this->convertStringToDate($checkin);
		if (!$begin || !$this->isDate($begin)) return false;
		return $this->checkDateOff($begin);
	}
	private function checkDateOff($begin) {
		$begin = str_replace("-","/",$begin);
		$date_enable = \Helper::getDayOn();
		if (in_array($begin, $date_enable) ) {
			return true;
		}
		$what_day = date('w', strtotime($begin));
		if (in_array($what_day, config('const.off_def')) ) {
			return false;
		}
		return true;
	}
	private function validate_holyday($checkin, $checkout, $room_type = null) {
		$checkin_tmp = $checkin;
		$checkout_tmp = $checkout;
		while ($checkin <= $checkout) {
			$begin = $this->convertStringToDate($checkin);
			//Log::debug($begin);
			if (!$begin || !$this->isDate($begin)) return false;
			$check_off = $this->checkDateOff($begin);
			if (!$check_off) return false;
			$checkin = date('Ymd', strtotime($begin. ' + 1 days'));
		};
		return $this->validate_holyday_special($checkin_tmp, $checkout_tmp, $room_type);
	}
	private function validate_holyday_special($checkin, $checkout, $room_type = null) {
		$where_room_type = "  and mh.time_holiday is null ";
		if ($room_type != null) {
			$where_room_type = " and mh.time_holiday = '$room_type' ";
		}
		$sql_check = "
			SELECT  1
			FROM    ms_holiday mh
			WHERE mh.date_holiday >= $checkin AND mh.date_holiday <= $checkout
			$where_room_type
		";
		//Log::debug($sql_check);
		$room_validate = DB::select($sql_check);
		 //Log::debug('$room_validate');
		 //Log::debug($room_validate);
		if(isset($room_validate) && (count($room_validate) > 0)){
			return false;
		}
		return true;
	}
	private function validate_stay_room($room_type, $checkin, $checkout){
		$check = $this->validate_holyday($checkin, $checkout, $room_type);
		if (!$check) throw new \ErrorException('booking_error_holiday');
		else {
			//Log::debug($room_type);
			//Log::debug($checkin);
			//Log::debug($checkout);
			$room_validate = DB::select("
				SELECT  main.booking_id,
						main.stay_checkin_date,
						main.stay_checkout_date
				FROM    tr_yoyaku main
				WHERE   main.stay_room_type = $room_type
				AND ( NOT  ( main.stay_checkin_date >= $checkout OR main.stay_checkout_date <= $checkin )
					)
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
			");
			//Log::debug('$room_validate');
			//Log::debug($room_validate);
			if(isset($room_validate) && (count($room_validate) != 0)){
				throw new \ErrorException('booking_error_stay');
			}
		}
	}
	private function whitening_validate($date, $time){
		//Log::debug('wt_validate');
		//Log::debug($date);
		//Log::debug($time);
		$wt_validate = DB::select("
			SELECT  main.booking_id
			FROM    tr_yoyaku main
			WHERE   main.service_date_start = '$date'
			AND     main.whitening_time = '$time'
			AND main.history_id IS NULL
			AND main.del_flg IS NULL
		");
		////Log::debug($wt_validate);
		if(isset($wt_validate) && (count($wt_validate) != 0)){
			//Log::debug("da co nguoi dat");
			//Log::debug($wt_validate);
			throw new \ErrorException('booking_error');
		}
	}
	private function set_course_1($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_type = isset($customer['age_type'])?$customer['age_type']:"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$lunch = isset($customer['lunch'])?json_decode($customer['lunch']):"";
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$bed = isset($customer['bed'])?$customer['bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		$Yoyaku->gender = $gender->kubun_id;
		$Yoyaku->age_type = $age_type;
		// if($age_type == 3 || $age_type == 30){
		// 	$Yoyaku->age_value = $age_value;
		// }else{
		// 	$Yoyaku->age_value = NULL;
		// }
		$Yoyaku->bed = $bed;
		$Yoyaku->lunch = $lunch->kubun_id;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_2($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		//Date_Time
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time1-value'])?$customer['time1-value']:"";
		$time2 = isset($customer['time2-value'])?$customer['time2-value']:"";
		$bed1 = isset($customer['time1-bed'])?$customer['time1-bed']:"";
		$bed2 = isset($customer['time2-bed'])?$customer['time2-bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time0_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$time1_json = isset($customer['time'][1]['json'])?$customer['time'][1]['json']:"";
		$time_json = $time0_json . "-" . $time1_json;
		//Log::debug('course 2 debug');
		//Log::debug($customer);
		$Yoyaku->time_json = $time_json;
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->service_time_2 = $time2;
		$Yoyaku->bed = $bed1 . "-" . $bed2;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human($gender->kubun_id, $date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_3($parent, $parent_date, $customer, &$Yoyaku){
		//Log::debug("start set_course_3");
		//Fake booking
		$fake_booking_flg = isset($customer['fake_booking'])?$customer['fake_booking']:NULL;
		//Basic
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time = isset($customer['time_room_value'])?$customer['time_room_value']:"";
		$bed = isset($customer['time_room_bed'])?$customer['time_room_bed']:"";
		$service_guest_num = isset($customer['service_guest_num'])?json_decode($customer['service_guest_num']):"";
		$lunch_guest_num = isset($customer['lunch_guest_num'])?json_decode($customer['lunch_guest_num']):"";
		//Option
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$Yoyaku->time_json = $time_json;
		$Yoyaku->fake_booking_flg = $fake_booking_flg;
		$Yoyaku->service_time_1 = $time;
		$Yoyaku->bed = $bed;
		$Yoyaku->service_guest_num = $service_guest_num->kubun_id;
		$Yoyaku->lunch_guest_num = $lunch_guest_num->kubun_id;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			//Log::debug("check parent");
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human('01', $date,  $time, $bed);
			// if($whitening->kubun_id == '02'){
			// 	//Log::debug("parent date $date , whitening_time $whitening_time" );
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			//Log::debug("check child");
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human('01', $parent_date,  $time, $bed);
			// if($whitening->kubun_id == '02' && $fake_booking_flg == null){
			// 	//Log::debug("parent date $parent_date , whitening_time $whitening_time" );
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_4($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		$plan_date_start = isset($customer['plan_date_start-value'])?$customer['plan_date_start-value']:"";
		$plan_date_end = isset($customer['plan_date_end-value'])?$customer['plan_date_end-value']:"";
		//Option
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		$Yoyaku->service_date_start = $plan_date_start;
		$Yoyaku->service_date_end = $plan_date_end;
		if($parent){
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
				}
			}
		}else{
			$Yoyaku->service_date_start = $parent_date;
		}
	}
	private function set_course_5($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time_room_time1'])?$customer['time_room_time1']:"";
		$time2 = isset($customer['time_room_time2'])?$customer['time_room_time2']:"";
		$service_pet_num = isset($customer['service_pet_num'])?json_decode($customer['service_pet_num']):"";
		$notes = isset($customer['notes'])?$customer['notes']:"";
		//Time Json
		$time_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$Yoyaku->time_json = $time_json;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->service_time_2 = $time2;
		$Yoyaku->service_pet_num = $service_pet_num->kubun_id;
		$Yoyaku->notes = $notes;
		if($parent){
			$result = $this->validate_course_pet($date, $time1, $time2);
			$Yoyaku->service_date_start = $date;
		}else{
			$result = $this->validate_course_pet($parent_date, $time1, $time2);
			$Yoyaku->service_date_start = $parent_date;
		}
	}
	private function set_course_7($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		//Date_Time
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time1-value'])?$customer['time1-value']:"";
		$time2 = isset($customer['time2-value'])?$customer['time2-value']:"";
		$bed1 = isset($customer['time1-bed'])?$customer['time1-bed']:"";
		$bed2 = isset($customer['time2-bed'])?$customer['time2-bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time0_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$time1_json = isset($customer['time'][1]['json'])?$customer['time'][1]['json']:"";
		$time_json = $time0_json . "-" . $time1_json;
		//Log::debug('course 2 debug');
		//Log::debug($customer);
		$Yoyaku->time_json = $time_json;
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->service_time_2 = $time2;
		$Yoyaku->bed = $bed1 . "-" . $bed2;
		$Yoyaku->tea = 1;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human($gender->kubun_id, $date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_8($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$lunch = isset($customer['lunch'])?json_decode($customer['lunch']):"";
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		//Date_Time
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time1-value'])?$customer['time1-value']:"";
		$bed1 = isset($customer['time1-bed'])?$customer['time1-bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time0_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$time1_json = isset($customer['time'][1]['json'])?$customer['time'][1]['json']:"";
		$time_json = $time0_json . "-" . $time1_json;
		//Log::debug('course 2 debug');
		//Log::debug($customer);
		$Yoyaku->time_json = $time_json;
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->bed = $bed1;
		$Yoyaku->lunch = $lunch->kubun_id;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human($gender->kubun_id, $date,  $time1, $bed1);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time1, $bed1);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_9($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$lunch = isset($customer['lunch'])?json_decode($customer['lunch']):"";
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		//Date_Time
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time1-value'])?$customer['time1-value']:"";
		$bed1 = isset($customer['time1-bed'])?$customer['time1-bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time0_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$time1_json = isset($customer['time'][1]['json'])?$customer['time'][1]['json']:"";
		$time_json = $time0_json . "-" . $time1_json;
		//Log::debug('course 2 debug');
		//Log::debug($customer);
		$Yoyaku->time_json = $time_json;
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->bed = $bed1;
		$Yoyaku->lunch = $lunch->kubun_id;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human($gender->kubun_id, $date,  $time1, $bed1);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time1, $bed1);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function set_course_10($parent, $parent_date, $customer, &$Yoyaku){
		//Basic
		$gender = isset($customer['gender'])?json_decode($customer['gender']):"";
		$age_value = isset($customer['age_value'])?$customer['age_value']:"";
		//Option
		$whitening = isset($customer['whitening'])?json_decode($customer['whitening']):"";
		$whitening2 = isset($customer['whitening2'])?json_decode($customer['whitening2']):"";
		$core_tuning = isset($customer['core_tuning'])?json_decode($customer['core_tuning']):"";
		$pet_keeping = isset($customer['pet_keeping'])?json_decode($customer['pet_keeping']):"";
		//Stay
		$stay_room_type = isset($customer['stay_room_type'])?json_decode($customer['stay_room_type']):null;
		$stay_guest_num = isset($customer['stay_guest_num'])?json_decode($customer['stay_guest_num']):"";
		$stay_checkin_date = isset($customer['range_date_start-value'])?$customer['range_date_start-value']:"";
		$stay_checkout_date = isset($customer['range_date_end-value'])?$customer['range_date_end-value']:"";
		$breakfast = isset($customer['breakfast'])?json_decode($customer['breakfast']):"";
		//Date_Time
		$date = isset($customer['date-value'])?$customer['date-value']:"";
		$time1 = isset($customer['time1-value'])?$customer['time1-value']:"";
		$time2 = isset($customer['time2-value'])?$customer['time2-value']:"";
		$bed1 = isset($customer['time1-bed'])?$customer['time1-bed']:"";
		$bed2 = isset($customer['time2-bed'])?$customer['time2-bed']:"";
		$whitening_time_json = $customer['whitening_data']['json'];
		$Yoyaku->whitening_time_json = $whitening_time_json;
		//Time Json
		$time0_json = isset($customer['time'][0]['json'])?$customer['time'][0]['json']:"";
		$time1_json = isset($customer['time'][1]['json'])?$customer['time'][1]['json']:"";
		$time_json = $time0_json . "-" . $time1_json;
		//Log::debug('course 2 debug');
		//Log::debug($customer);
		$Yoyaku->time_json = $time_json;
		$Yoyaku->gender = $gender->kubun_id;
		// $Yoyaku->age_value = $age_value;
		$Yoyaku->service_time_1 = $time1;
		$Yoyaku->service_time_2 = $time2;
		$Yoyaku->bed = $bed1 . "-" . $bed2;
		$Yoyaku->whitening = $whitening->kubun_id;
		if($whitening->kubun_id == '02'){
			$whitening_time = isset($customer['whitening-time_value'])?$customer['whitening-time_value']:"";
			$whitening_repeat = isset($customer['whitening_repeat'])?$customer['whitening_repeat']:"";
			$Yoyaku->whitening_time = $whitening_time;
			$Yoyaku->whitening_repeat = $whitening_repeat;
		}
		$Yoyaku->whitening2 = $whitening2->kubun_id;
		if($whitening2->kubun_id == '02'){
			$whitening_repeat2 = isset($customer['whitening_repeat2'])?$customer['whitening_repeat2']:"";
			$Yoyaku->whitening_repeat2 = $whitening_repeat2;
		}
		$Yoyaku->core_tuning = $core_tuning->kubun_id;
		$Yoyaku->pet_keeping = $pet_keeping->kubun_id;
		if($parent){
			$Yoyaku->service_date_start = $date;
			if(isset($stay_room_type)){
				$Yoyaku->stay_room_type = $stay_room_type->kubun_id;
				if($stay_room_type->kubun_id != '01'){
					$this->validate_stay_room($stay_room_type->kubun_id, $stay_checkin_date, $stay_checkout_date);
					$Yoyaku->stay_guest_num = $stay_guest_num->kubun_id;
					$Yoyaku->stay_checkin_date = $stay_checkin_date;
					$Yoyaku->stay_checkout_date = $stay_checkout_date;
					$Yoyaku->breakfast = $breakfast->kubun_id;
				}
			}
			$this->validate_course_human($gender->kubun_id, $date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($date, $whitening_time);
			// }
		}else{
			$Yoyaku->service_date_start = $parent_date;
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time1, $bed1);
			$this->validate_course_human($gender->kubun_id, $parent_date,  $time2, $bed2);
			// if($whitening->kubun_id == '02'){
			// 	$this->whitening_validate($parent_date, $whitening_time);
			// }
		}
	}
	private function validate_course_pet($date, $time1, $time2){
		$course_5_validate = DB::select("
				SELECT          main.booking_id
				FROM            tr_yoyaku main
				WHERE           main.course = '05'
				AND             main.service_date_start = $date
				AND             main.service_time_1 = $time1
				AND             main.service_time_2 = $time2
				AND main.history_id IS NULL
				AND main.del_flg IS NULL
		");
		if(isset($course_5_validate) && (count($course_5_validate) != 0)){
			throw new \ErrorException('Course 05 error');
		}
	}
	public function get_booking_id(){
		$like_booking_id = date("Ymd");
		$result = Yoyaku::query()->where('booking_id', 'LIKE', $like_booking_id."%");
		return $like_booking_id.sprintf('%04d', ($result->count() + 1) );
	}
	public function get_time_validate_bus ($repeat_user, $transport, $bus_arrive_time_slide ) {
		if ($transport['kubun_id'] == config('const.db.kubun_id_value.transport.BUS') ) { // nếu đi xe bus'02'
			if ($repeat_user['kubun_id'] == config('const.db.kubun_id_value.repeat_user.NEW')) { // はじめて
				$time_required_min = config('const.db.time_validate.transport.bus.NEW'); // người tắm lần đầu 01
			} else { //　リピート
				$time_required_min = config('const.db.time_validate.transport.bus.OLD');  // người cũ 02
			}
			$time_bus_arrived = $bus_arrive_time_slide['notes'];
			return $this->plus_time_string ($time_bus_arrived, $time_required_min); // time required go early for BUS
		}
		return false;
	}
	public function get_time_room(Request $request){
		$data = $request->all();
		$repeat_user = json_decode($data['repeat_user'], true);
		$course = json_decode($data['course'], true);
		$data_get_attr = json_decode($data['data_get_attr'], true);
		$validate_time = null;
		if ($course['kubun_id'] == '03') {
			$gender = [];
			$gender['kubun_id'] = '01';
		} else {
			$gender = json_decode($data['gender'], true);
		}
		//dd($sections_booking);
		$day_book_time = '';
		$validate_ss_time = [];
		$range_time_validate = [];
		if ($day_book_time == '')
		if (isset($data['date-value'])) { // date Tắm bình thường 1 day refresh
			$day_book_time = $data['date-value'];
		} else if ($day_book_time == '' && isset($data_get_attr['date']))  {
			$day_book_time = $data_get_attr['date'];
		}
		$sections_booking = $this->get_booking($request);
		if (count($sections_booking) > 0) { // Th add thêm người
			$validate_ss_time['date-value-first'] = $sections_booking["date-value"];
			$transport = json_decode($sections_booking['transport'], true);
			$bus_arrive_time_slide = json_decode($sections_booking['bus_arrive_time_slide'], true);
			//dd($sections_booking['info']);
			$day_book_time_ss = $sections_booking['date-value'];
			if ($day_book_time == ''){
				$day_book_time = $day_book_time_ss;
			}
			$owner_pet = 0;
			$range_time_validate_pet = [];
			// check

			foreach ($sections_booking['info'] as $key => $booking_ss) {
				$course_ss = json_decode($booking_ss['course'], true);
				if ($course_ss['kubun_id'] !== config('const.db.kubun_id_value.course.PET')) {
					if ($course_ss['kubun_id'] == config('const.db.kubun_id_value.course.BOTH_ALL_ROOM')) { // fasting plan
						$gender_ss_id = '01';
					} else {
						$gender_ss = json_decode($booking_ss['gender'], true);
						$gender_ss_id = $gender_ss['kubun_id'];
					}
					//dd($gender_ss_id);
					if ($gender['kubun_id'] == $gender_ss_id) {
						foreach ($booking_ss['time'] as $k_time => $v_time) {
							if ($course_ss['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN')
							|| $course_ss['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN2')) { // fasting plan // 2020/06/05
								$tmp_time = json_decode($v_time['from']['json'], true);
								if ($tmp_time['date_booking'] == $day_book_time) {
									$from = json_decode($v_time['from']['json'], true);
									$to = json_decode($v_time['to']['json'], true);
									$validate_ss_time[$key]['time_from']['time'] = $from['notes'];
									$validate_ss_time[$key]['time_to']['time'] = $to['notes'];
									$validate_ss_time[$key]['time_from']['bed'] = $from['kubun_id_room'];
									$validate_ss_time[$key]['time_to']['bed'] = $to['kubun_id_room'];
								}
							} else {
								if ($day_book_time == $day_book_time_ss) {
									$ss_time = json_decode($v_time['json'], true);
									// if ($course_ss['kubun_id'] == config('const.db.kubun_id_value.course.BOTH_ALL_ROOM')) {
									//     $range_time_validate[$key]['start_time'] = $ss_time['notes'];
									//     $range_time_validate[$key]['end_time'] =  $this->plus_time_string($ss_time['notes'], 60);
									//     $range_time_validate[$key]['bed'] = $ss_time['kubun_id_room'];
									// } else { rm thanh tv
										$validate_ss_time[$key][$k_time]['time'] = $ss_time['notes'];
										$validate_ss_time[$key][$k_time]['bed'] = $ss_time['kubun_id_room'];
									//}
								}
							}
						}
					}
					//dd($validate_ss_time);
					$owner_pet = 1;
				} else {
					if ($day_book_time == $day_book_time_ss) { // get time pet ss
						foreach ($booking_ss['time'] as $key_time_pet => $value_time_pet) {
							$time_json = json_decode($value_time_pet['json'], true);
							$range_time_validate_pet[$key]['start_time'] = substr($time_json['notes'], 0, 4);
							$range_time_validate_pet[$key]['end_time'] = substr($time_json['notes'], 5, 4);
						}
					}
				}
			}
			if ($owner_pet == 0 && count($range_time_validate_pet) > 0) {
				$validate_ss_time['pet_ss'] = $range_time_validate_pet;
			}
			//dd($validate_ss_time);
		} else { // th booking mới
			$transport = json_decode($data['transport'], true);
			$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
		}
		$this->fetch_kubun_data($data);
		$validate_time = [];

		$this->get_validate_time_choice ($course, $data, $data_get_attr, $validate_time, $day_book_time);
		if(($course['kubun_id'] == '04' || $course['kubun_id'] == '06') && (isset($data['bus_first']))
			&& ($data['bus_first'] == "0")){ // 2020/06/05
			$time_bus = null;
		}else{
			$time_bus = $this->get_time_bus_customer($repeat_user, $transport, $bus_arrive_time_slide);
		}
		// get time whitten
		$data["time_whitening"] = null;
		if (isset($data["whitening_data"]["json"]) && !empty($data["whitening_data"]["json"])) {
			// $whitening_data = json_decode($data["whitening_data"]["json"], true);
			// $time_whitening_book = explode("-",$whitening_data["notes"])[0];
			// $time_wait_bath_max = 75;
			// $time_wait_bath_min = 75; // tam trang binh thuong va repeat giong nhau
			// $whitening_repeat = $data["whitening_repeat"];
			// if ($whitening_repeat == '0') { // tam trang repeat
			// 	if ($repeat_user['kubun_id'] == '01') { // first time bath
			// 		$time_wait_bath_max = 45;
			// 	} else {
			// 		$time_wait_bath_max = 30;
			// 	}
			// } else { // tam trang lan dau
			// 	if ($repeat_user['kubun_id'] == '01') { // first time bath
			// 		$time_wait_bath_max = 75;
			// 	} else {
			// 		$time_wait_bath_max = 60;
			// 	}
			// }
			// $data["time_whitening"]["max"] = $this->plus_time_string($time_whitening_book, $time_wait_bath_max);
			// //Log::debug($time_whitening_book);
			// //Log::debug($time_wait_bath_min);
			// $data["time_whitening"]["min"] = $this->minus_time_string($time_whitening_book, $time_wait_bath_min);
			// //Log::debug($data["time_whitening"]["min"]);
		}

        //2023.7.2 ここでレスポンスなし
		$data_time = $this->get_date_time($gender, $data, $validate_time, $day_book_time, $time_bus, $validate_ss_time, $course, $data_get_attr, $range_time_validate,$data["time_whitening"]);

		$data_time['course'] = $course['kubun_id'];
		$data_time['week_day'] = Carbon::createFromFormat('Ymd', $day_book_time)->dayOfWeek;
		return view('sunsun.front.parts.booking_time',$data_time)->render();
	}
	public function get_date_time($gender, $data, $validate_time, $day_book_time, $time_bus, $validate_ss_time, $course, $data_get_attr, $range_time_validate, $time_whitening = null ) {
		$kubun_type_time = config('const.db.kubun_type_value.TIME'); // 013 kubun_type

		$data_time = [];
		if ($course['kubun_id'] == '03') {
			$time_kubun_type_book_room = config('const.db.kubun_type_value.TIME_BOOK_ROOM'); //014
			$kubun_type_bed_male = config('const.db.kubun_type_value.bed_male'); // 017
			$data_time['beds'] = $data['bed_male'];
			$data_time_room = $this->get_time_room_booking($time_kubun_type_book_room, $kubun_type_bed_male, $validate_time, $day_book_time, $time_bus, $validate_ss_time, [], $range_time_validate, $course, $time_whitening);
		} else {
			if($gender['kubun_id'] == '01'){ // for man
				$kubun_type_bed_male = config('const.db.kubun_type_value.bed_male'); // 017 kubun_type
				$data_time['beds'] = $data['bed_male'];
				$data_time_room = $this->get_time_room_booking($kubun_type_time, $kubun_type_bed_male, $validate_time, $day_book_time, $time_bus, $validate_ss_time, [], $range_time_validate, [], $time_whitening);
			}else{ //for woman
				$kubun_type_bed_female = config('const.db.kubun_type_value.bed_female'); // 018 kubun_type
				$data_time['beds'] = $data['bed_female'];
				$data_time_room = $this->get_time_room_booking($kubun_type_time, $kubun_type_bed_female, $validate_time, $day_book_time, $time_bus, $validate_ss_time, [], $range_time_validate, [], $time_whitening);
			}
		}
		//dd($data_time_room);
		$tmp_time_room = [];
		foreach ($data_time_room as $key => $time_room) {
			$tmp_time_room[$time_room->kubun_id][$time_room->kubun_value_room] = $time_room;
		}
		$data_time['time_room'] = $tmp_time_room;
		//dd($data);
		$data_time['gender'] = $gender;
		if (isset($data_get_attr['new'])) {
			$data_time['new'] = 1;
		}
		return $data_time;
	}
	public function get_time_bus_customer($repeat_user, $transport, $bus_arrive_time_slide) {
		// check time bus
		$time_bus = null;
		if ($bus_arrive_time_slide != null) {
			$check_bus = $this->get_time_validate_bus($repeat_user, $transport, $bus_arrive_time_slide );
			if ($check_bus !== false) {
				$time_bus = $check_bus;
			}
		}
		//Log::debug($current_min_time);
		return $time_bus;
	}
	public function get_validate_time_choice ($course, $data, $data_get_attr, &$validate_time, &$day_book_time) {
		if ($course['kubun_id'] == config('const.db.kubun_id_value.course.NORMAL')) { // Tắm bình thường
			//Log::debug(' check normal');
			if ((count($data['time']) > 0 && isset($data_get_attr['new'])) || (count($data['time']) > 1)) {
				foreach ($data['time'] as $key => $time_book) {
					if ($time_book['value'] != '0') {
						$validate_time[$key]['max'] = $this->plus_time_string($time_book['value'], 120); // plus 2h between 2 times shower
						$validate_time[$key]['min'] = $this->minus_time_string($time_book['value'], 120); // minus 2h between 2 times shower
					}
				}
			}
		} else if ($course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH')
			 || $course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH_NOON')
			 || $course['kubun_id'] == config('const.db.kubun_id_value.course.REFRESH')) { // 1 day refresh
			if ($data_get_attr['date_type'] == 'shower_1' && $data['time2-value'] != '0') {
				$validate_time['1_DAY_REFRESH']['max'] = $this->plus_time_string($data['time2-value'], 120); // plus 2h between 2 times shower
				$validate_time['1_DAY_REFRESH']['min'] = $this->minus_time_string($data['time2-value'], 120); // plus 2h between 2 times shower
			} else if ($data_get_attr['date_type'] == 'shower_2' && $data['time1-value'] != '0') {
				$validate_time['1_DAY_REFRESH']['max'] = $this->plus_time_string($data['time1-value'], 120); // plus 2h between 2 times shower
				$validate_time['1_DAY_REFRESH']['min'] = $this->minus_time_string($data['time1-value'], 120); // plus 2h between 2 times shower
			}
			//dd($data);
		} else if ($course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.FASTING_PLAN2')) { // fasting plan // 2020/06/05
			//Log::debug($data_get_attr);
			$type = $data_get_attr['date_type'];
			$day_book_time = $data_get_attr['date'];
			//if ($type == 'to') {
				$date_select = $data['date'];
				$tmp_time = [];
				foreach ($date_select as $key => $value) {
					$date = $data_get_attr['date'];
					if ($date == $value['day']['value']) {
						$tmp_time[] = ($type == 'to') ? $value['from']['value'] : $value['to']['value'] ;
					}
				}
				$time_validate = collect($tmp_time)->max();
				$validate_time['FASTING_PLAN']['max'] = $this->plus_time_string($time_validate, 120); // plus 2h between 2 times shower
				$validate_time['FASTING_PLAN']['min'] = $this->minus_time_string($time_validate, 120); // plus 2h between 2 times shower
			//}
		}
		//return $validate_time;
	}
	/**
	 * @param $time_kubun_type
	 * @param $room_kubun_type
	 * @param null $time_bath is the time validate if shower many times
	 * @param $day_book_time
	 * @param $time_bus
	 * @param $validate_ss_time
	 * @param $data_course array
	 * @param array $range_time_validate
	 * @param array $course
	 * @param null $time_whitening
	 * @return array
	 * @parram $course array
	 */
	public function get_time_room_booking ($time_kubun_type, $room_kubun_type, $time_bath
		, $day_book_time, $time_bus, $validate_ss_time, $data_course = array(), $range_time_validate = [], $course = array(), $time_whitening = null) {
		$gender = '';
		$sql_join_on = '';
		$sql_where_yoyaku = '';
		$sql_where = '';
		$time_date_booking = $day_book_time;
		$config_time = config('const.db.kubun_type_value.TIME');
		$config_room = config('const.db.kubun_type_value.TIME_BOOK_ROOM');
		$config_white = config('const.db.kubun_type_value.TIME_WHITENING');
		$config_pet = config('const.db.kubun_type_value.TIME_PET');
		if ($room_kubun_type == '017') {
			$gender = '01';
		} else if ($room_kubun_type == '018') {
			$gender = '02';
		} else if ($time_kubun_type == $config_pet) {
			$sql_join_on .= " OR (ytm.course = '05' AND mk1.notes = CONCAT(ytm.service_time_1,'-', ytm.service_time_2)) ";
			$sql_pet_where = " OR (ty.course = '05') ";
			$sql_where_yoyaku = $sql_pet_where;
		} else if ($time_kubun_type == $config_white)  { // 021
			$sql_join_on .= " OR SUBSTRING(mk1.notes, 1 ,9) = ytm.whitening_time ";
			$hand_code = $data_course['whitening_repeat'];
			$sql_where .= " AND SUBSTRING(mk1.notes, 11) = $hand_code ";
			if ($hand_code == 0) {
				$sql_where .= "
				OR SUBSTRING(mk1.notes, 11) = '1'
			";
			}
			$sql_where_yoyaku =  " OR ty.whitening = '02' "; // có chọn tắm trắng
		}
		$sql_get_booking = $this->sql_get_booking_yoyaku($sql_where_yoyaku);
		$sql_get_booking = str_replace(':date_booking', $time_date_booking, $sql_get_booking);
		$sql_get_booking = str_replace(':gender_booking', $gender, $sql_get_booking);
		if (isset($course['kubun_id']) && $course['kubun_id'] == '03') {
			/**
			 * book all room
			 *  time book all room block 2 time continually so if time after time all room can book is not empty
			 * => can not book this time
			 */
			$sql_join_on .= "
			OR  (
					(  ytm.service_time_1 < (mk1.notes + '0130')  AND  ytm.service_time_1 >= mk1.notes  AND mk2.kubun_value = ytm.bed_service_1 AND mk2.kubun_type = '017')
					OR ( ytm.service_time_2  < (mk1.notes + '0130') AND ytm.service_time_2 >= mk1.notes AND mk2.kubun_value = ytm.bed_service_2 AND mk2.kubun_type = '017')
				) ";
		}
		$data_sql = [
			'time_kubun_type' => $time_kubun_type,
			'room_kubun_type' => $room_kubun_type,
			'bed_male' => '017',
			'bed_female' => '018',
			'date_booking' => $time_date_booking
		];
		if ($time_bus !== null) {
			$data_sql['time_bus'] = $time_bus;
		}
		$sql_select = "
				-- SELECT time & room
			   SELECT
				mk1.ms_kubun_id, mk1.kubun_type, mk1.kubun_id, mk1.kubun_value, mk1.sort_no, mk1.notes
				, mk2.kubun_id as kubun_id_room
				, mk2.kubun_value as kubun_value_room
				, mk2.notes as notes_room
				, CASE
					WHEN mk2.kubun_type = :bed_male THEN 'male'
					WHEN mk2.kubun_type = :bed_female THEN 'female'
					ELSE 'other'
					END as gender
				, mk2.kubun_type as gender_type
				, ytm.gender_ytm
				, ytm.course as course_ytm
				, ytm.booking_id
				, :date_booking as date_booking
				, mk2.sort_no
		";
		//Log::debug('$time_date_booking');
		//Log::debug($time_date_booking);
		$sql_bus = "";
		if ($time_bus !== null) {
			if ($time_kubun_type == $config_white) { // 021
				$sql_bus = "
				 AND SUBSTRING(mk1.notes, 1 ,4) > :time_bus
				";
			} else {
				$sql_bus = "
				 AND mk1.notes > :time_bus
			";
			}
		}
		date_default_timezone_set('Asia/Tokyo');
		//$timecurrent = $this->plus_time_string(date('Hi'), 20);
		$timecurrent = date('Hi');
		$date_current = date("Ymd");
		if ($date_current == $day_book_time) {
			$data_sql['timecurrent'] = $timecurrent;
			if ($time_kubun_type == $config_white) { // 021
				$sql_bus .= "
				 AND SUBSTRING(mk1.notes, 1 ,4) > :timecurrent
				";
			} else {
				$sql_bus .= "
				 AND mk1.notes > :timecurrent
			";
			}
		}
		$sql_time_path = "";
		if ($time_bath != null && count($time_bath) > 0) { // mỗi lần tắm cách nhau 2h
			foreach ($time_bath as $time) {
				$time_max = $time['max'];
				$time_min = $time['min'];
				if ($time_kubun_type == $config_white) { // 021
					$sql_time_path .= " AND ( SUBSTRING(mk1.notes, 1 ,4) >= '$time_max' OR SUBSTRING(mk1.notes, 1 ,4) <= '$time_min') ";
				} else {
					$sql_time_path .= " AND ( mk1.notes >= '$time_max' OR mk1.notes <= '$time_min') ";
				}
			}
		}
		$sql_whitening ="";
		//Log::debug($time_whitening);
		if ($time_whitening != null) {
			$time_max = $time_whitening['max'];
			$time_min = $time_whitening['min'];
			if ($time_kubun_type == $config_white) {
				$sql_whitening = " AND ( SUBSTRING(mk1.notes, 1 ,4) >= '$time_max' OR SUBSTRING(mk1.notes, 1 ,4) <= '$time_min') ";
			} else {
				$sql_whitening = " AND ( mk1.notes >= '$time_max' OR mk1.notes <= '$time_min') ";
			}
		}
		$sql_time_path .= $sql_whitening;
		//Log::debug($sql_time_path);
		$sql_validate_ss = "";
		if (count($validate_ss_time) > 1) { // time book lần sau không trùng time book trước
			$sql_validate_ss_tmp = "";
			$count = 0;
			foreach ($validate_ss_time as $key_ss_t => $value_ss_t) {
				if ($key_ss_t == 'date-value-first') continue;
				foreach ($value_ss_t as $key_ss => $value_ss) {
					$or = '';
					if ($count != 0) {
						$or = " OR ";
					}
					if ($key_ss_t == 'boss_time_pet' && count($validate_ss_time[$key_ss_t]) > 0) { // time pet not concomitant width time human book at the first
						$time_boss_start = $value_ss['time'];
						$time_boss_end = $this->plus_time_string($time_boss_start, 120);
						$time_boss_start_validate = $this->minus_time_string($time_boss_start, 60);
						$sql_validate_ss_tmp .= " $or ( SUBSTRING(mk1.notes, 1 ,4) >= '$time_boss_start_validate' AND SUBSTRING(mk1.notes, 1 ,4) <= '$time_boss_end') ";
					} else if ($key_ss_t == 'pet_ss' && count($validate_ss_time[$key_ss_t]) > 0) { // time pet not concomitant width time human book at the first
						$time_start_pet_validate = $this->minus_time_string($value_ss['start_time'], 120); // - 120 p time tắm
						$time_end_pet_validate = $value_ss['end_time'];
						$sql_validate_ss_tmp .= " $or ( SUBSTRING(mk1.notes, 1 ,4) >= '$time_start_pet_validate' AND SUBSTRING(mk1.notes, 1 ,4) <= '$time_end_pet_validate') ";
					} else {
						$time = $value_ss['time'];
					$sql_bed = "";
					if (isset($value_ss['bed']) && $value_ss['bed'] != '') {
						$bed = $value_ss['bed'];
						$sql_bed = " AND mk2.kubun_id =  '$bed' ";
					}
					if (isset($course['kubun_id']) && $course['kubun_id'] == '03') {
						$time_2 = $this->minus_time_string($time, 90 - 1);
						$sql_validate_ss_tmp .= "
						$or ( mk1.notes <= '$time' AND mk1.notes >= '$time_2'  $sql_bed )
					";
					} else {
						$sql_validate_ss_tmp .= "
						$or ( mk1.notes = '$time'  $sql_bed )
					";
					}
					}
					$count ++;
				}
			}
			if (!empty($sql_validate_ss_tmp)) {
				$setdate = "WHEN '01' = '01' ";
				if (isset($validate_ss_time["date-value-first"])) {
					$setdate =  "WHEN '".$day_book_time."' = '".$validate_ss_time["date-value-first"]."' ";
				}
				$sql_validate_ss .= "$setdate  ";
				$sql_validate_ss .= " and ( $sql_validate_ss_tmp ) then 0 ";
			}
		}
		//dd($sql_validate_ss);
		$sql_range = '';
		if (count($range_time_validate) > 0) { // validate book nguyen room
			$sql_range_tmp = "";
			$count_range = 0;
			foreach ($range_time_validate as $key_range => $value_range) {
				$start_time = $value_range['start_time'];  $or = '';
				$end_time = $value_range['end_time'];
				$bed = $value_range['bed'];
				if ($count_range != 0) {
					$or = " OR ";
				}
				$sql_range_tmp .= "
						$or ( mk1.notes >= '$start_time' AND mk1.notes < '$end_time' AND mk2.kubun_id =  '$bed' AND mk2.kubun_type = '017' )
					";
				$count_range ++;
			}
			if (!empty($sql_range_tmp)) {
				$sql_range .= " WHEN  $sql_range_tmp THEN 0 ";
			}
		}
		$data_sql['date_holiday'] = $time_date_booking;
		$sql_holiday = " WHEN exists (  select 1 from ms_holiday mh
										where mh.date_holiday = :date_holiday and
											(
												mh.time_holiday = mk1.time_holiday or mh.time_holiday is null
											)
											and mh.type_holiday =
											(
												case
													when mk1.kubun_type = '020' then '2'
													when mk1.kubun_type = '021' then '3'
													when (mk1.kubun_type = '013' and '018' = mk2.kubun_type) then '4'
													else '1'
												end
											)
									) THEN 0 ";
		// sql display time
		// check $time_date_booking off
		$off_def = "" ;
		$check = $this->validate_holyday_def($time_date_booking);
		if (!$check) {
			$off_def ="
				when 1=1 then 0
			";
		}
		$sql_get_check_room_free ="
			, CASE
					$off_def
					when mk1.kubun_type = '013' AND mk1.kubun_id IN ('10','11') then '0'
					$sql_holiday
					$sql_range
					$sql_validate_ss
					WHEN ytm.course IS NULL $sql_bus $sql_time_path THEN 1
					-- WHEN ytm.course = '03' AND ytm.service_time_2 = mk1.notes THEN 1 -- course 03 lost 2 time
					ELSE 0
					END as status_time_validate
		";
		// sql condition join
		$sql_join = "
			FROM ms_kubun mk1
			INNER JOIN ms_kubun mk2 ON mk2.kubun_type = :room_kubun_type
			LEFT JOIN
				 (
					$sql_get_booking
				 )as ytm
				ON
					(mk1.notes = ytm.service_time_1 AND mk2.kubun_value = ytm.bed_service_1)
					OR (mk1.notes = ytm.service_time_2 AND mk2.kubun_value = ytm.bed_service_2 )
					-- OR (
					--        ytm.course = '03' AND mk2.kubun_type = '017' AND mk2.kubun_value = ytm.bed_service_2
					--        AND mk1.notes >= ytm.service_time_1 AND mk1.notes < ytm.service_time_2
					--   )
					$sql_join_on
		";
		$sql_where = "
			WHERE mk1.kubun_type = :time_kubun_type
			$sql_where
		";
		$sql_group = "
			GROUP BY mk1.ms_kubun_id, mk1.kubun_type, mk1.kubun_id, mk1.kubun_value, mk1.sort_no, mk1.notes, mk2.kubun_id
					, mk2.kubun_value, mk2.notes, gender, gender_type, status_time_validate, mk2.sort_no
					, ytm.booking_id,  ytm.gender_ytm, course_ytm
			ORDER BY mk2.sort_no, mk1.sort_no
		";
		$sql = "$sql_select
				$sql_get_check_room_free
				$sql_join
				$sql_where
				$sql_group
		";
		//DB::connection()->enableQueryLog();
        //dd($sql);
		$time_request = DB::select($sql, $data_sql);
        //dd($time_request);
		//$queries = DB::getQueryLog();
		//dd($queries);
		if ($time_kubun_type == $config_room)  { // 014
			$time_request = $this->fix_3_bed_to_1($time_request);
		}
		return $time_request;
	}
	private function fix_3_bed_to_1(&$time_request){
		$time_temp = [];
		$check_0945 = 1;
		$check_1315 = 1;
		$check_1515 = 1;
		foreach($time_request as $time){
			if(($time->notes == '0945') || ($time->notes == '1315') || ($time->notes == '1515')){
				if(($check_0945) && ($time->notes == '0945')){
					$time_temp['0945'] = $time;
					$check_0945 = 0;
				}else if(($check_1315) && ($time->notes == '1315')){
					$time_temp['1315'] = $time;
					$check_1315 = 0;
				}else if(($check_1515) && ($time->notes == '1515')){
					$time_temp['1515'] = $time;
					$check_1515 = 0;
				}else{
					if($time->status_time_validate == 0){
						$time_temp["$time->notes"]->status_time_validate = 0;
					}
				}
			}
		}
		return $time_temp;
	}
	public function sql_get_booking_yoyaku ($sql_where = "") {
		return "
			SELECT
				CASE
					WHEN ty.course = '01' OR ty.course = '04' OR ty.course = '06' THEN tydj.service_time_1
					ELSE ty.service_time_1
					END AS service_time_1
				, CASE
					WHEN ty.course = '01' OR ty.course = '04' OR ty.course = '06' THEN tydj.service_time_2
					-- WHEN  ty.course = '03' THEN ty.service_time_1 + '0100' - '0001' Remove Thanhtv 20191230
					ELSE ty.service_time_2
					END AS service_time_2
				, CASE
					WHEN ty.course = '01' OR ty.course = '04' OR ty.course = '06'  THEN SUBSTRING(tydj.notes, 1 ,1)
					ELSE SUBSTRING(ty.bed, 1 ,1)
					END AS bed_service_1
				, CASE
					WHEN ty.course = '01' OR ty.course = '04' OR ty.course = '06'   THEN SUBSTRING(tydj.notes, 3 ,1)
					WHEN  ty.course = '03' THEN SUBSTRING(ty.bed, 1 ,1)
					ELSE SUBSTRING(ty.bed, 3 ,1)
					END AS bed_service_2
				, CASE
					WHEN ty.course = '01' OR ty.course = '04'  OR ty.course = '06'  THEN tydj.service_date
					ELSE ty.service_date_start
					END AS service_date
				,
				   ty.course
				, ty.service_time_1 as service_time_1_ty
				, ty.service_time_2 as service_time_2_ty
				, ty.service_date_start as service_date_start
				, ty.service_date_end as service_date_end
				, tydj.service_time_1 as service_time_1_tydj
				, tydj.service_time_2 as service_time_2_tydj
				, tydj.service_date  as service_date_tydj
				, ty.gender as gender_ytm
				, ty.booking_id
				, ty.whitening
				, ty.whitening_time
				, ty.whitening2
				, ty.core_tuning
			FROM tr_yoyaku  ty
			LEFT JOIN (select booking_id,service_date,notes,service_time_1,service_time_2 from tr_yoyaku_danjiki_jikan) tydj ON ty.booking_id = tydj.booking_id
			WHERE (
					ty.gender = ':gender_booking' -- 01 for male 02 for female
					OR ( '01' = ':gender_booking' AND    ty.gender IS NULL AND ty.course = '03'  ) -- book all room for bed male
					$sql_where
				  )
			AND
				(
					( (ty.course = '01' OR ty.course = '04' OR ty.course = '06')  AND ( tydj.service_date = ':date_booking') )
					OR  ( (ty.course <> '04' AND ty.course <> '06') AND (ty.service_date_start =  ':date_booking' ) )
					-- OR (ty.service_date_start <=  ':date_booking' AND ty.service_date_end >= ':date_booking' )
				)
			AND ty.history_id IS NULL
			AND ty.del_flg IS NULL
		"; // 2020/06/05
	}
	/**
	 * Combines SQL and its bindings
	 *
	 * @param \Eloquent $query
	 * @return string
	 */
	public static function getEloquentSqlWithBindings($query)
	{
		return vsprintf(str_replace('?', '%s', $query->toSql()), collect($query->getBindings())->map(function ($binding) {
			return  "'{$binding}'";
		})->toArray());
	}
	/*public function book_room (Request $request) {
		$data = $request->all();
		$repeat_user = json_decode($data['repeat_user'], true);
		$course = json_decode($data['course'], true);
		$data_get_attr = json_decode($data['data_get_attr'], true);
		$time_bus = null;
		$time_bath = [];
		$sections_booking = $this->get_booking($request);
		//dd($sections_booking);
		$day_book_time = '';
		$validate_ss_time = [];
		if (count($sections_booking) > 0) { // Th add thêm người
			$transport = json_decode($sections_booking['transport'], true);
			$bus_arrive_time_slide = json_decode($sections_booking['bus_arrive_time_slide'], true);
			//$day_book_time = $sections_booking['date-value'];
			if (isset($sections_booking['date-value'])) { // date Tắm bình thường 1 day refresh
				$day_book_time = $sections_booking['date-value'];
			} else {
				$day_book_time = '01';
			}
		} else { // th booking mới
			$transport = json_decode($data['transport'], true);
			$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
			if (isset($data['date-value'])) { // date Tắm bình thường 1 day refresh
				$day_book_time = $data['date-value'];
			}
		}
		// check time bus
		if ($bus_arrive_time_slide != null) {
			$check_bus = $this->get_time_validate_bus($repeat_user, $transport, $bus_arrive_time_slide );
			if ($check_bus !== false) {
				$time_bus = $check_bus;
			}
		}
		// dd($data);
		$time_kubun_type_book_room = config('const.db.kubun_type_value.TIME_BOOK_ROOM'); //014
		$kubun_type_bed_male = config('const.db.kubun_type_value.bed_male'); // 017
		$data_time_room = $this->get_time_room_booking($time_kubun_type_book_room, $kubun_type_bed_male, $time_bath, $day_book_time, $time_bus, $validate_ss_time);
		$tmp_time_room = [];
		foreach ($data_time_room as $key => $time_room) {
			$tmp_time_room[$time_room->kubun_id][] = $time_room;
		}
		//dd($tmp_time_wt);
		$data_time['time_book_all_room'] = $tmp_time_room;
		$MsKubun = MsKubun::all();
		$data_time['beds'] = $MsKubun->where('kubun_type',$kubun_type_bed_male)->sortBy('sort_no');
		return view('sunsun.front.parts.booking_room',$data_time)->render();
	}*/
	/*public function get_time_booking_all_room ($time_kubun_type, $room_kubun_type, $time_bath, $day_book_time, $time_bus) {
		$sql_get_booking = $this->sql_get_booking_yoyaku();
		$gender = '';
		$time_date_booking = $day_book_time;
		if ($room_kubun_type == '017') {
			$gender = '01';
		} else if ($room_kubun_type == '018') {
			$gender = '02';
		}
		$sql_get_booking = str_replace(':date_booking', $time_date_booking, $sql_get_booking);
		$sql_get_booking = str_replace(':gender_booking', $gender, $sql_get_booking);
		$data_sql = [
			'time_kubun_type' => $time_kubun_type,
			'room_kubun_type' => $room_kubun_type,
			'bed_male' => '017',
			'bed_female' => '018'
		];
		if ($time_bus !== null) {
			$data_sql['time_bus'] = $time_bus;
		}
		$sql_select = "
				-- SELECT time & room
			   SELECT
				mk1.*
				, mk2.kubun_id as kubun_id_room
				, mk2.kubun_value as kubun_value_room
				, mk2.notes as notes_room
				, CASE
					WHEN mk2.kubun_type = :bed_male THEN 'male'
					WHEN mk2.kubun_type = :bed_female THEN 'female'
					ELSE 'other'
					END as gender
				, mk2.kubun_type as gender_type
				, ytm.gender_ytm
				, ytm.course as course_ytm
				, ytm.booking_id
		";
		$sql_bus = "";
		if ($time_bus !== null) {
			$sql_bus = "
				 AND mk1.notes > :time_bus
			";
		}
		$sql_time_path = "";
		if (count($time_bath) > 0) {
			foreach ($time_bath as $time) {
				$time_max = $time['max']; $time_min = $time['min'];
				$sql_time_path .= " AND ( mk1.notes > '$time_max' OR mk1.notes < '$time_min') ";
			}
		}
		$sql_get_check_room_free ="
			, CASE
					WHEN ytm.course IS NULL $sql_bus $sql_time_path THEN 1
					ELSE 0
					END as status_time_validate
		";
		$sql_join = "
			FROM ms_kubun mk1
			INNER JOIN ms_kubun mk2 ON mk2.kubun_type = :room_kubun_type
			LEFT JOIN
				 (
					$sql_get_booking
				 )as ytm
				ON
					(mk1.notes = ytm.service_time_1 AND mk2.kubun_value = ytm.bed_service_1)
					OR (mk1.notes = ytm.service_time_2 AND mk2.kubun_value = ytm.bed_service_2 )
					OR (ytm.course = '03' AND ytm.gender_ytm = '01' AND mk2.kubun_value = ytm.bed_service_2 AND mk1.notes >= ytm.service_time_1 AND mk1.notes <= ytm.service_time_2  )
		";
		$sql_where = "
			WHERE mk1.kubun_type = :time_kubun_type
		";
		$sql_group = "
			GROUP BY mk1.ms_kubun_id, mk1.kubun_type, mk1.kubun_id, mk1.kubun_value, mk1.sort_no, mk1.notes, mk2.kubun_id
					, mk2.kubun_value, mk2.notes, gender, gender_type, status_time_validate, sunsun.mk2.sort_no
					, ytm.booking_id,  ytm.gender_ytm, course_ytm
			ORDER BY  mk2.sort_no, mk1.sort_no
		";
		$sql = "$sql_select
				$sql_get_check_room_free
				$sql_join
				$sql_where
				$sql_group
		";
		//DB::connection()->enableQueryLog();
		$time_request = DB::select($sql, $data_sql);
		//$queries = DB::getQueryLog();
		//dd($queries);
		return $time_request;
	}*/
	public function book_time_room_wt (Request $request) {
		$data = $request->all();
		$repeat_user = json_decode($data['repeat_user'], true);
		$whitening_repeat = $data['whitening_repeat'];
		//$transport = json_decode($data['transport'], true);
		$course = json_decode($data['course'], true);
		$data_get_attr = json_decode($data['data_get_attr'], true);
		//$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
		$validate_time = [];
		$day_book_time = '';
		$validate_ss_time = [];
		if (isset($data['date-value'])) { // date Tắm bình thường 1 day refresh
			$day_book_time = $data['date-value'];
		}
		$sections_booking = $this->get_booking($request);
		if (count($sections_booking) > 0) { // Th add thêm người
			$transport = json_decode($sections_booking['transport'], true);
			$bus_arrive_time_slide = json_decode($sections_booking['bus_arrive_time_slide'], true);
			//dd($sections_booking['info']);
			$day_book_time_ss = $sections_booking['date-value'];
			if ($day_book_time == ''){
				$day_book_time = $day_book_time_ss;
			}
			foreach ($sections_booking['info'] as $key_ss => $value_ss) {
				if (isset($value_ss['whitening_data']) && $value_ss['whitening_data']['json'] != null) {
					$data_whitening = json_decode($value_ss['whitening_data']['json'], true);
					$validate_ss_time[$key_ss]['data']['time'] = $data_whitening['notes'];
				}
			}
			//dd($validate_ss_time);
		} else { // th booking mới
			$transport = json_decode($data['transport'], true);
			$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
		}
		$time_wait_bath_max = 75; // tam trang binh thuong va repeat giong nhau
		$time_wait_bath_min = 75;
		if ($whitening_repeat == '0') { // tam trang repeat
			if ($repeat_user['kubun_id'] == '01') { // first time bath
				$time_wait_bath_min = 45;
		} else {
				$time_wait_bath_min = 30;
		}
		} else { // tam trang lan dau
			if ($repeat_user['kubun_id'] == '01') { // first time bath
				$time_wait_bath_min = 75;
		} else {
				$time_wait_bath_min = 60;
			}
		}
		if ($course['kubun_id'] == config('const.db.kubun_id_value.course.NORMAL')) { // Tắm bình thường
			foreach ($data['time'] as $key => $time_book) {
				if ($time_book['value'] != '0') {
					$validate_time[$key]['max'] = $this->plus_time_string($time_book['value'], $time_wait_bath_max);
					$validate_time[$key]['min'] = $this->minus_time_string($time_book['value'], $time_wait_bath_min);
				}
			}
		} else if ($course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH')
			|| $course['kubun_id'] == config('const.db.kubun_id_value.course.1_DAY_REFRESH_NOON')
		|| $course['kubun_id'] == config('const.db.kubun_id_value.course.REFRESH')) { // 1 day refresh
			//dd($data['time1-value']);
			if ($data['time2-value'] != '0') {
				$validate_time['1_DAY_REFRESH_1']['max'] = $this->plus_time_string($data['time2-value'], $time_wait_bath_max);
				$validate_time['1_DAY_REFRESH_1']['min'] = $this->minus_time_string($data['time2-value'], $time_wait_bath_min);
			}
			if ($data['time1-value'] != '0') {
				$validate_time['1_DAY_REFRESH_2']['max'] = $this->plus_time_string($data['time1-value'], $time_wait_bath_max);
				$validate_time['1_DAY_REFRESH_2']['min'] = $this->minus_time_string($data['time1-value'], $time_wait_bath_min);
			}
			//dd($data);
		} else if ($course['kubun_id'] == config('const.db.kubun_id_value.course.SKIN') || $course['kubun_id'] == config('const.db.kubun_id_value.course.UP')) {
			if ($data['time1-value'] != '0') {
				$validate_time['1_DAY_REFRESH_1']['max'] = $this->plus_time_string($data['time1-value'], $time_wait_bath_max);
				$validate_time['1_DAY_REFRESH_1']['min'] = $this->minus_time_string($data['time1-value'], $time_wait_bath_min);
			}
		} else if ($course['kubun_id'] == config('const.db.kubun_id_value.course.BOTH_ALL_ROOM')) { // All room
			$validate_time['BOTH_ALL_ROOM']['max'] = $this->plus_time_string($data['time_room_value'], $time_wait_bath_max);
			$validate_time['BOTH_ALL_ROOM']['min'] = $this->minus_time_string($data['time_room_value'], $time_wait_bath_min);
		}
		// check time bus
		$time_bus = $this->get_time_bus_customer($repeat_user, $transport, $bus_arrive_time_slide);
		$time_kubun_type_whitening = config('const.db.kubun_type_value.TIME_WHITENING'); //021
		$kubun_type_bed = config('const.db.kubun_type_value.bed_pet'); // 19
		$data_course['whitening_repeat'] = $whitening_repeat;
		$data_time_room = $this->get_time_room_booking ($time_kubun_type_whitening, $kubun_type_bed, $validate_time, $day_book_time, $time_bus, $validate_ss_time, $data_course);
		//$data_time_room = $this->get_time_room_whitening($time_kubun_type_whitening, $kubun_type_bed_male, $validate_time,$repeat_user, $whitening_repeat);
		$tmp_time_wt = [];
		foreach ($data_time_room as $key => $time_room) {
			$tmp_time_wt[$time_room->kubun_id][] = $time_room;
		}
		//dd($tmp_time_wt);
		$data_time['time_whitening'] = $tmp_time_wt;
		$MsKubun = MsKubun::all();
		$data_time['beds'] = $MsKubun->where('kubun_type','019')->sortBy('sort_no');
		return view('sunsun.front.parts.booking_room_wt',$data_time)->render();
	}
   /* public function get_time_room_whitening ($time_kubun_type, $room_kubun_type, $time_bath = null, $repeat_user, $whitening_repeat) {
		$data_sql = [
			'time_kubun_type' => $time_kubun_type,
			'room_kubun_type' => $room_kubun_type,
			'bed_male' => '017',
			'bed_female' => '018'
		];
		if ($time_bath !== null) {
			$data_sql['time_bath'] = $time_bath;
		}
		$data_sql['hand_code'] = $whitening_repeat;
		$sql_select = "
				-- SELECT time & room
			   SELECT
				mk1.*
				, mk2.kubun_id as kubun_id_room
				, mk2.kubun_value as kubun_value_room
				, mk2.notes as notes_room
				, SUBSTRING(mk1.notes, 11) as user_type
				, SUBSTRING(mk1.notes, 1, 4) as time_start
				, SUBSTRING(mk1.notes, 6, 4) as end_time
				, CASE
					WHEN mk2.kubun_type = :bed_male THEN 'male'
					WHEN mk2.kubun_type = :bed_female THEN 'female'
					ELSE 'other'
					END as gender
				, mk2.kubun_type as gender_type
		";
		if ($time_bath !== null) {
			$sql_select .= "
				 , CASE
					WHEN mk1.notes > :time_bath THEN 1
					ELSE 0
					END as status_time_validate
			";
		}
		$sql = "$sql_select
			FROM ms_kubun mk1
			INNER JOIN ms_kubun mk2 ON mk2.kubun_type = :room_kubun_type
			WHERE mk1.kubun_type = :time_kubun_type
				AND SUBSTRING(mk1.notes, 11) = :hand_code
		";
		if ($data_sql['hand_code'] == 0) {
			$sql .= "
				OR SUBSTRING(mk1.notes, 11) = '1'
			";
		}
		$sql .= "
			ORDER BY  mk2.sort_no, mk1.sort_no
		";
		return DB::select($sql, $data_sql);
	}*/
	public function book_time_room_pet (Request $request) {
		$data = $request->all();
		$repeat_user = json_decode($data['repeat_user'], true);
		$course = json_decode($data['course'], true);
		//$data_get_attr = json_decode($data['data_get_attr'], true);
		//$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
		$validate_time = null;
		$time_bath = [];
		$validate_ss_time = [];
		$time_bus = null;
		$day_book_time = '';
		if (isset($data['date-value'])) {
			$day_book_time = $data['date-value'];
		}
		$sections_booking = $this->get_booking($request);
		if (count($sections_booking) > 0) { // Th add thêm người
			$transport = json_decode($sections_booking['transport'], true);
			$bus_arrive_time_slide = json_decode($sections_booking['bus_arrive_time_slide'], true);
			//dd($sections_booking['info']);
			$day_book_time_ss = $sections_booking['date-value'];
			if ($day_book_time == ''){
				$day_book_time = $day_book_time_ss;
			}
			$count_loop = 0;
			foreach ($sections_booking['info'] as $key => $booking_ss) {
				$course_ss = json_decode($booking_ss['course'], true);
				if ($course_ss['kubun_id'] == config('const.db.kubun_id_value.course.PET')) {
					foreach ($booking_ss['time'] as $k_time => $v_time) {
						if ($day_book_time == $day_book_time_ss) {
							$ss_time = json_decode($v_time['json'], true);
							$validate_ss_time[$key][$k_time]['time'] = $ss_time['notes'];
							$validate_ss_time[$key][$k_time]['bed'] = $ss_time['kubun_id_room'];
						}
					}
					//dd($validate_ss_time);
				}
				/**
				 * lấy time người book đầu tiên là chủ của pet
				 * this time shower không trùng time pet
				 */
				if ($count_loop == 0) {
					if ($course_ss['kubun_id'] != config('const.db.kubun_id_value.course.PET')) {
					foreach ($booking_ss['time'] as $k_time => $v_time) {
						if ($day_book_time == $day_book_time_ss && isset($v_time['json'])) {
							$ss_time = json_decode($v_time['json'], true);
								$validate_ss_time['boss_time_pet'][$k_time]['time'] = $ss_time['notes'];
							}
						}
						$count_loop++;
					}
				}
			}
			//dd($validate_ss_time);
		} else { // th booking mới
			$transport = json_decode($data['transport'], true);
			$bus_arrive_time_slide = json_decode($data['bus_arrive_time_slide'], true);
		}
		// check time bus
		$time_bus = $this->get_time_bus_customer($repeat_user, $transport, $bus_arrive_time_slide);
		//Log::debug($current_min_time);
		$MsKubun = MsKubun::all();
		$data_time['beds'] = $MsKubun->where('kubun_type','019')->sortBy('sort_no');
		$time_kubun_type_pet = config('const.db.kubun_type_value.TIME_PET'); //020
		$kubun_type_bed_pet = config('const.db.kubun_type_value.bed_pet'); // 19
		$data_time_room_pet = $this->get_time_room_booking ($time_kubun_type_pet, $kubun_type_bed_pet, $time_bath, $day_book_time, $time_bus, $validate_ss_time);
		//$data_time_room_pet = $this->get_time_room_pet($time_kubun_type_pet, $kubun_type_bed_pet, $validate_time);
		$tmp_time_pet = [];
		foreach ($data_time_room_pet as $key => $time_room) {
			$tmp_time_pet[$time_room->kubun_id][] = $time_room;
		}
		//dd($tmp_time_wt);
		$data_time['time_pet'] = $tmp_time_pet;
		return view('sunsun.front.parts.booking_room_pet',$data_time)->render();
	}
	/*public function get_time_room_pet ($time_kubun_type, $room_kubun_type, $time_bath = null) {
		$data_sql = [
			'time_kubun_type' => $time_kubun_type,
			'room_kubun_type' => $room_kubun_type,
			'bed_male' => '017',
			'bed_female' => '018'
		];
		if ($time_bath !== null) {
			$data_sql['time_bath'] = $time_bath;
		}
		$sql_select = "
				-- SELECT time & room
			   SELECT
				mk1.*
				, mk2.kubun_id as kubun_id_room
				, mk2.kubun_value as kubun_value_room
				, mk2.notes as notes_room
				, SUBSTRING(mk1.notes, 1, 4) as time_start
				, SUBSTRING(mk1.notes, 6, 4) as end_time
				, CASE
					WHEN mk2.kubun_type = :bed_male THEN 'male'
					WHEN mk2.kubun_type = :bed_female THEN 'female'
					ELSE 'other'
					END as gender
				, mk2.kubun_type as gender_type
		";
		if ($time_bath !== null) {
			$sql_select .= "
				 , CASE
					WHEN SUBSTRING(mk1.notes, 1, 4) > :time_bath THEN 1
					ELSE 0
					END as status_time_validate
			";
		}
		$sql = "$sql_select
			FROM ms_kubun mk1
			INNER JOIN ms_kubun mk2 ON mk2.kubun_type = :room_kubun_type
			WHERE mk1.kubun_type = :time_kubun_type
			ORDER BY  mk2.sort_no, mk1.sort_no
		";
		return DB::select($sql, $data_sql);
	}*/
	public function fetch_kubun_data(&$data){
		$MsKubun = MsKubun::all();
		$data['repeat_user'] = $MsKubun->where('kubun_type','001')->sortBy('sort_no');
		$data['transport'] = $MsKubun->where('kubun_type','002')->sortBy('sort_no');
		$data['bus_arrive_time_slide'] = $MsKubun->where('kubun_type','003')->sortBy('sort_no');
		$data['pick_up'] = $MsKubun->where('kubun_type','004')->sortBy('sort_no');
		$data['course'] = $MsKubun->where('kubun_type','005')->sortBy('sort_no');
		$data['gender'] = $MsKubun->where('kubun_type','006')->sortBy('sort_no');
		$data['age_value'] = $MsKubun->where('kubun_type','007')->sortBy('sort_no');
		$data['lunch'] = $MsKubun->where('kubun_type','008')->sortBy('sort_no');
		$data['whitening'] = $MsKubun->where('kubun_type','009')->sortBy('sort_no');
		$data['pet_keeping'] = $MsKubun->where('kubun_type','010')->sortBy('sort_no');
		$data['stay_room_type'] = $MsKubun->where('kubun_type','011')->where('kubun_id','<>','03')->sortBy('sort_no');
		$data['stay_guest_num'] = $MsKubun->where('kubun_type','012')->sortBy('sort_no');
		$data['time_slide'] = $MsKubun->where('kubun_type','013')->sortBy('sort_no');
		$data['time_slide_room'] = $MsKubun->where('kubun_type','014')->sortBy('sort_no');
		$data['lunch_guest_num'] = $MsKubun->where('kubun_type','023')->sortBy('sort_no');
		$data['service_guest_num'] = $MsKubun->where('kubun_type','015')->sortBy('sort_no');
		$data['service_pet_num'] = $MsKubun->where('kubun_type','016')->sortBy('sort_no');
		$data['bed_male'] = $MsKubun->where('kubun_type','017')->sortBy('sort_no');
		$data['bed_female'] = $MsKubun->where('kubun_type','018')->sortBy('sort_no');
		$data['bed_pet'] = $MsKubun->where('kubun_type','019')->sortBy('sort_no');
		$data['time_slide_pet'] = $MsKubun->where('kubun_type','020')->sortBy('sort_no');
		$data['time_slide_wt'] = $MsKubun->where('kubun_type','021')->sortBy('sort_no');
		$data['breakfast'] = $MsKubun->where('kubun_type','022')->sortBy('sort_no');
	}
	public function get_service(Request $request){
		$data = $request->all();
		$this->fetch_kubun_data($data);
		$json = json_decode($data['service']);
		if(count(json_decode($data['course_time'], true)) == 0){
			$data['course_time'] = NULL;
		}else{
			$data['course_time'] = json_decode($data['course_time'], true);
			$date_arr = [];
			foreach($data['course_time'] as $date){
				$date_arr[$date['service_date']] = NULL;
			}
			$weekMap = [
				0 => '日',
				1 => '月',
				2 => '火',
				3 => '水',
				4 => '木',
				5 => '金',
				6 => '土',
			];
			foreach($date_arr as $key => $d){
				$dayOfTheWeek = Carbon::createFromFormat('Ymd', $key)->dayOfWeek;
				$day = Carbon::createFromFormat('Ymd', $key)->format('d');
				$month = Carbon::createFromFormat('Ymd', $key)->format('m');
				$date_arr[$key] = $month . "/" . $day ."(" . $weekMap[$dayOfTheWeek] . ")";
			}
			$weekday = $weekMap[$dayOfTheWeek];
			$data['date_unique_time'] = $date_arr;
			// dd($weekday);
		}
		$data['course_data'] = json_decode($data['course_data'], true);
		if(isset( $data['course_data']['whitening_time'])){
			$data['course_data']['whitening_time-view'] =    substr($data['course_data']['whitening_time'], 0, 2) . ":" .
														substr($data['course_data']['whitening_time'], 2, 2) . "～".
														substr($data['course_data']['whitening_time'], 5, 2) . ":".
														substr($data['course_data']['whitening_time'], 7, 2);
		}
//        dd($data);
		$data["setting"] = Setting::find(1)->pluck('accommodation_flg')->first();
		if ($json->kubun_id == "01") {
			return view('sunsun.front.parts.enzyme_bath',$data)->render();
		} elseif ($json->kubun_id == "02") {
			return view('sunsun.front.parts.oneday_bath',$data)->render();
		} elseif ($json->kubun_id == "03") {
			return view('sunsun.front.parts.enzyme_room_bath',$data)->render();
		} elseif ($json->kubun_id == "04" || $json->kubun_id == "06") { // 2020/06/05
			return view('sunsun.front.parts.fasting_plan',$data)->render();
		} elseif ($json->kubun_id == "05") {
			return view('sunsun.front.parts.pet_enzyme_bath',$data)->render();
		} elseif ($json->kubun_id == "07") {
			return view('sunsun.front.parts.refresh_course',$data)->render();
		} elseif ($json->kubun_id == "08") {
			return view('sunsun.front.parts.skin_course',$data)->render();
		} elseif ($json->kubun_id == "09") {
			return view('sunsun.front.parts.up_course',$data)->render();
		} elseif ($json->kubun_id == "10") {
			return view('sunsun.front.parts.oneday_bath_noon',$data)->render();
		}
	}
	public function complete(Request $request) {
		$data = $request->all();
		// dd($data);
		if(isset($data['bookingID']) == false){
			return redirect()->route('home');
		}
		return view('sunsun.front.complete', $data);
	}
	public function change_value_kana($value) {
		$value = $this->change_space_2_byte($value);
		return mb_convert_kana($value, 'KVA');
	}
	public function change_space_2_byte($value) {
		return preg_replace('/\s+|　/', '　', trim($value));
	}
	public function remove_space($value) {
		return preg_replace('/\s+|　/', '', $value);
	}
	public function get_data_payment_history(&$data_pr,$data_info,$data_age) {
		$plus = ",";
		$info_book = [];
		foreach ($data_age as $i_age => $row_age) {
			if (isset($row_age->age_value) && !empty($row_age->age_value)) {
				$data_info[$i_age][config('const.db.tr_payments_history.AGE_VALUE')] = $row_age->age_value;
			}
		}
		$data_ss = [];
		foreach ($data_pr as $i_pr => $row) {
			if ($row["price"] != 0) {
				$yo_index = $row[config('const.db.tr_payments_history.YOYAKU_INDEX')];
				$split_yo_index = explode(",",$yo_index);
				$count_index = count($split_yo_index);
				$gender = "";
				$age_value = "";
				$repeat_user = "";
				$date_value = "";
				for ($i = 0; $i < $count_index ;$i++) {
					$yo_index_cur = $split_yo_index[$i];
					$info_cur = $data_info[$yo_index_cur];
					if ($gender == "")
						$gender = $info_cur[config('const.db.tr_payments_history.GENDER')];
					else {
						$gender .= $plus . $info_cur[config('const.db.tr_payments_history.GENDER')];
					}
					if ($age_value == "")
						$age_value = $info_cur[config('const.db.tr_payments_history.AGE_VALUE')];
					else {
						$age_value .= $plus . $info_cur[config('const.db.tr_payments_history.AGE_VALUE')];
					}
					if ($repeat_user == "")
						$repeat_user = $info_cur[config('const.db.tr_payments_history.REPEAT_USER')];
					else {
						$repeat_user .= $plus . $info_cur[config('const.db.tr_payments_history.REPEAT_USER')];
					}
					$date_value_info = "";
					if(isset($info_cur['date']) && !empty($info_cur['date']) && count($info_cur['date']) > 0) {
						// $count_date = count($row_info['date']) - 1;
						$date_value_info = "";
						foreach ($info_cur['date'] as $i_date => $row_date) {
							// if ($i_date == 0 || $i_date == $count_date) {
								if(isset($row_date->service_date) && !empty($row_date->service_date)) {
									if (empty($date_value))
										$date_value_info = Carbon::createFromFormat('Ymd', $row_date->service_date)->format('Y/m/d');
									else
										$date_value_info .= $plus .Carbon::createFromFormat('Ymd', $row_date->service_date)->format('Y/m/d');
								}
							// }
						}
					} else if(isset($info_cur['date-view']) && !empty($info_cur['date-view'])) {
						$date_value_info = $info_cur['date-view'];
					}
					if ($date_value == "")
						$date_value = $date_value_info;
					else {
						$date_value .= $plus . $date_value_info;
					}
				}
				if ($gender != "") {
					$gender = $this->changeValueUnique($gender);
					$row[config('const.db.tr_payments_history.GENDER')] = $gender;
				}
				if ($age_value != "") {
					$age_value = $this->changeValueUnique($age_value);
					$row[config('const.db.tr_payments_history.AGE_VALUE')] = $age_value;
				}
				if ($repeat_user != "") {
					$repeat_user = $this->changeValueUnique($repeat_user);
					$row[config('const.db.tr_payments_history.REPEAT_USER')] = $repeat_user;
				}
				Log::debug($date_value);
				if ($date_value != "") {
					$date_value = $this->changeValueUnique($date_value);
					$row[config('const.db.tr_payments_history.DATE_VALUE')] = $date_value;
					Log::debug("set date view");
				}
				$data_ss[$i_pr] = $row;
			}
		}
		return $data_ss;
	}
	public function changeValueUnique($val) {
		$result = "";
		if (empty($val)) return "";
		$arr = explode(",",$val);
		$arr = array_unique($arr);
		$arr = array_filter($arr, function($value) { return !is_null($value) && $value !== ''; });
		return implode(",",$arr);
	}
	public function getDataPrice($booking_id) {
		$yo = Yoyaku::where('booking_id', $booking_id)->first();
		// So sanh thoi gian book, phuong tien, thoi gian don xe bus, co don hay khong
		// Flag check co thay doi may thu ben tren hay khong?
		$change_check = false;
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
		foreach ($admin_customer as $value) {
			if(
				($value->course === '01')
				||($value->course === '02')
				||($value->course === '07')
				||($value->course === '10')
				||($value->course === '03')
				||($value->course === '04')
				||($value->course === '06') // 2020/06/05
			){
				$check_has_note = true;
			}
			if(($value->course === '02') || ($value->course === '04') || ($value->course === '06') || ($value->course === '07') || ($value->course === '10')){
				$check_has_couse_oneday = true;
			}
			if(($value->course === '04')){
				$check_note_mail = true;
			}
		}
		$admin_data['admin_customer'] = $admin_customer;
		$admin_data['change_check'] = $change_check;
		return $admin_data;
	}
	public function save_tr_payments_history($return_booking_id) {
		//create data admin
		$admin_data = "";
		$admin_customer = "";
		$bill_text = null;
		$admin_price = null;
		$admin_data = $this->getDataPrice($return_booking_id);
		$admin_customer = $admin_data['admin_customer'];
		$this->convert_booking_2_value($admin_data['admin_customer'], $admin_data);
		$new_bill = $this->yoyaku_2_bill($admin_customer,$bill_text,$admin_price);
		$data_ss = $this->get_data_payment_history($new_bill,$admin_data["admin_value_customer"],$admin_customer);
		//Save payment history
		if ($data_ss != null) {
			foreach ($data_ss as $row) {
				$ph = new PaymentHistory;
				$ph->booking_id = $return_booking_id;
				if (isset($row[config('const.db.tr_payments_history.GENDER')]))
					$ph->gender = $row[config('const.db.tr_payments_history.GENDER')];
				// if (isset($row[config('const.db.tr_payments_history.AGE_VALUE')]))
				// 	$ph->age_value = $row[config('const.db.tr_payments_history.AGE_VALUE')];
				if (isset($row[config('const.db.tr_payments_history.REPEAT_USER')]))
					$ph->repeat_user = $row[config('const.db.tr_payments_history.REPEAT_USER')];
				if (isset($row[config('const.db.tr_payments_history.DATE_VALUE')]))
					$ph->date_value = $row[config('const.db.tr_payments_history.DATE_VALUE')];
				$ph->product_name = $row["name"];
				$ph->quantity = $row["quantity"];
				$ph->unit = $row["unit"];
				$ph->money = $row["price"];
				$ph->price = $ph->money / $ph->quantity;
				$ph->save();
			}
		}
		$arr_return = [
			'bill_text' => $bill_text
			,'admin_price' => $admin_price
			,'admin_data' => $admin_data
		];
		return $arr_return;
	}
}
