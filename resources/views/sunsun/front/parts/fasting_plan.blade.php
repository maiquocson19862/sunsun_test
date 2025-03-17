@php
	if( (!isset($course_data['course'])) || ($course_data['course'] != '04' && $course_data['course'] != '06') ){
		$course_data = NULL;
		$date_unique_time = NULL;
	}
	if(isset($pop_data)){
		$pop_data = json_decode($pop_data, true);
	}
	if(!isset($pop_data) || (json_decode($pop_data['course'], true)['kubun_id'] != '04' && json_decode($pop_data['course'], true)['kubun_id'] != '06')){
		$pop_data = NULL;
	}
	$note_plus = "";
	if  (isset($service)){
		$get_service = json_decode($service);
		if ($get_service->kubun_id == '04')
			$note_plus = "断食コース（初めて）の方には、準備食などの添付ファイルを送らせていただきます。必ずお読みになり、ご来店ください。";
	}
@endphp
<?php // 2020/06/05 ?>
<div class="booking-block">
	<div class="collapse collapse-top show">
		<div class="booking-field">
			<div class="booking-field-label  booking-laber-padding">
			</div>
			<div class="booking-field-content">
				<p class="node-text text-left mb-2">断食プランには、1時間程度のミニ講座が含まれます。</p>
				<?php if (!empty($note_plus)) { ?>
					<p class="node-text text-left mb-2">{{ $note_plus }}</p>
				<?php } ?>
			</div>
		</div>
		<div class="booking-field">
			<div class="booking-field-label  booking-laber-padding">
				<p class="text-left pt-2">{{config('booking.gender.label')}}</p>
			</div>
			<div class="booking-field-content">
				<select name="gender" id="gender" class="form-control">
					@php $blank = true; @endphp
					@foreach($gender as $value)
						@if( $blank === true)
							<option value="0">－</option>
							@php $blank = false; @endphp
						@endif
						@if(isset($course_data['gender']) && ($value->kubun_id == $course_data['gender']))
							<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
						@elseif(isset($pop_data['gender']) && ($value->kubun_id == json_decode($pop_data['gender'], true)['kubun_id']))
							<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
						@else
							<option value='@json($value)'>{{ $value->kubun_value }}</option>
						@endif
					@endforeach
				</select>
			</div>
		</div>
		<div class="booking-field mb-2" style="display:none !important">
			<div class="booking-field-label  booking-laber-padding">
				<p class="text-left pt-2">{{config('booking.age.label')}}</p>
			</div>
			<div class="booking-field-content">
				<div class="age-col age">
					<div class="age-left">
						<select id="age_value"  name="age_value" class="form-control">
							@for($j = 18; $j < 100; $j++ )
								@if(isset($course_data['age_value']) && ($course_data['age_value'] == $j))
									<option selected value='{{ $j }}'>{{ $j }}</option>
								@elseif(isset($pop_data['age_value']) && ($pop_data['age_value'] == $j))
									<option selected value='{{ $j }}'>{{ $j }}</option>
								@else
									<option value='{{ $j }}'>{{ $j }}</option>
								@endif
							@endfor
						</select>
						<div class="age_title">
							<span>才</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		@php
			if(isset($course_data["service_date_start"]) && isset($course_data["service_date_end"])){
				$plan_date_start= substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
				$plan_date_end = substr($course_data['service_date_end'], 0, 4).'/'.substr($course_data['service_date_end'], 4, 2).'/'.substr($course_data['service_date_end'], 6, 2);
			}
			if(isset($pop_data["plan_date_start-value"]) && isset($pop_data["plan_date_end-value"])){
				$plan_date_start= substr($pop_data['plan_date_start-value'], 0, 4).'/'.substr($pop_data['plan_date_start-value'], 4, 2).'/'.substr($pop_data['plan_date_start-value'], 6, 2);
				$plan_date_end = substr($pop_data['plan_date_end-value'], 0, 4).'/'.substr($pop_data['plan_date_end-value'], 4, 2).'/'.substr($pop_data['plan_date_end-value'], 6, 2);
			}
		@endphp
		<div class="row">
			<div class="col-5">
				<p class="text-left pt-2   booking-laber-padding">{{config('booking.range_date_eat.label')}}</p>
			</div>
		</div>
		<input name="plan_date_start-value" id="plan_date_start-value" type="hidden" value="">
		<input name="plan_date_end-value" id="plan_date_end-value" type="hidden" value="">
		<input name="plan_date_start-view" id="plan_date_start-view" type="hidden" value="">
		<input name="plan_date_end-view" id="plan_date_end-view" type="hidden" value="">
		<div class="booking-field booking-room date-range_block {{(isset($request_post['add_new_user']) && $request_post['add_new_user'] == 'on')?'hidden':''}}"  id="choice-range-day">
			<div class="field-start-day date-range_block_left">
				<p class="node-text">開始日</p>
				@if(isset($course_data['booking_id']) === false)
					<input name="plan_date_start" data-format="yyyy/MM/dd" type="text" class=" form-control date-book-input range_date bg-white"  readonly="readonly" id="plan_date_start" value="{{ isset($plan_date_start)?$plan_date_start:'' }}">
				@else
					<input name="plan_date_start" data-format="yyyy/MM/dd" type="text" class=" form-control"  readonly="readonly" disabled id="plan_date_start" value="{{ isset($plan_date_start)?$plan_date_start:'' }}">
				@endif
			</div>
			<div class="date-range_block_center">
				<p class="">&nbsp;</p>
				<p class="character-date pt-2">～</p>
			</div>
			<div class="field-end-day date-range_block_right">
				<p class="node-text">終了日</p>
				@if(isset($course_data['booking_id']) === false)
					<input name="plan_date_end" data-format="yyyy/MM/dd" type="text" class="form-control date-book-input range_date bg-white"  readonly="readonly" id="plan_date_end" value="{{ isset($plan_date_end)?$plan_date_end:'' }}">
				@else
					<input name="plan_date_end" data-format="yyyy/MM/dd" type="text" class="form-control"  readonly="readonly" disabled id="plan_date_end" value="{{ isset($plan_date_end)?$plan_date_end:'' }}">
				@endif
			</div>
		</div>
		<input type="hidden" name="bus_first" id="bus_first" value="0">
		<div>
			<div class="booking-field-100  booking-laber-padding">
				<p class="text-left pt-2">{{config('booking.range_time_eat.label')}}</p>
			</div>
			<div class="booking-field-100">
				<p class="node-text multiple-date mb-1">
					1日2回ずつ入浴時間を選択します
					<br> 入浴の間は2時間以上空けてください。
				</p>
			</div>
			@if(isset($date_unique_time))
			<div class="time-list" value="1">
				@php
				$i = 0;
				@endphp
				@foreach($date_unique_time as $key => $unique_time)
					@php
						$time_start = NULL;
						$time_end = NULL;
						if(is_array($course_time)){
							foreach($course_time as $time){
								if($key == $time['service_date']){
									break;
								}
							}
						}
						$time_start = substr($time['service_time_1'], 0, 2) . ":" . substr($time['service_time_1'], 2, 2);
						$time_end = substr($time['service_time_2'], 0, 2) . ":" . substr($time['service_time_2'], 2, 2);
						$bed_start = substr($time['notes'], 0, 1);
						$bed_end = substr($time['notes'], 2, 3);
						if(isset($time['time_json'])){
							$temp_time_json = explode('-', $time['time_json']);
							$time1_json = isset($temp_time_json[0])?$temp_time_json[0]:'';
							$time2_json = isset($temp_time_json[1])?$temp_time_json[1]:'';
						}
					@endphp
					<div class="booking-field choice-time">
						<input value="0" class="time_index" type="hidden">
						<div class="booking-field-label label-data pt-2">
							<label class="">{{ $unique_time }}</label>
							<input name="date[{{ $i }}][day][view]" value="{{ isset($time['service_date'])?$time['service_date']:'' }}" type="hidden">
							<input name="date[{{ $i }}][day][value]" value="{{ isset($time['service_date'])?$time['service_date']:'' }}" type="hidden">
						</div>
						<div class="booking-field-content date-time">
							<div class="choice-data-time set-time time-start">
								<div class="set-time">
									<input name="date[{{ $i }}][from][value]" type="hidden" class="time_from time_value" readonly="readonly" value="{{ isset($time['service_time_1'])?$time['service_time_1']:'0' }}" />
									<input name="date[{{ $i }}][from][bed]" type="hidden" class="time_bed" readonly="readonly" value="{{ isset($bed_start)?$bed_start:'0' }}" />
									<input name="date[{{ $i }}][from][view]" type="text" class="time form-control js-set-time bg-white" data-date_value="{{ isset($time['service_date'])?$time['service_date']:'' }}" data-date_type="form" readonly="readonly" value="{{ isset($time_start)?$time_start:'－' }}" />
									<input name="time[{{ $i }}][from][json]" type="hidden" class="data-json_input" value="{{ isset($time1_json)?$time1_json:'' }}" />
									<input name="time[{{ $i }}][from][element]" type="hidden" value="time_bath_10" />
								</div>
								<div class="icon-time mt-1">
								</div>
							</div>
							<div class="choice-data-time set-time time-end">
								<div class="set-time">
									<input name="date[{{ $i }}][to][value]" type="hidden" class="time_to time_value" readonly="readonly" value="{{ isset($time['service_time_2'])?$time['service_time_2']:'0' }}" />
									<input name="date[{{ $i }}][to][bed]" type="hidden" class="time_bed" readonly="readonly" value="{{ isset($bed_end)?$bed_end:'0' }}" />
									<input name="date[{{ $i }}][to][view]" type="text" class="time form-control js-set-time bg-white" data-date_value="{{ isset($time['service_date'])?$time['service_date']:'' }}" data-date_type="to" readonly="readonly" value="{{ isset($time_end)?$time_end:'－' }}" />
									<input name="time[{{ $i }}][to][json]" type="hidden" class="data-json_input" value="{{ isset($time2_json)?$time1_json:'' }}" />
									<input name="time[{{ $i }}][to][element]" type="hidden" value="time_bath_11" />
								</div>
								<div class="icon-time mt-1"></div>
							</div>
						</div>
					</div>
				@php
				$i++;
				@endphp
				@endforeach
			</div>
			@elseif(is_array($pop_data['time']) && is_array($pop_data['date']))
				<div class="time-list" value="1">
					@foreach($pop_data['date'] as $key => $unique_time)
						@php
							$time_start = NULL;
							$time_end = NULL;
							$time_start = substr($unique_time['from']['value'], 0, 2) . ":" . substr($unique_time['from']['value'], 2, 2);
							$time_end = substr($unique_time['to']['value'], 0, 2) . ":" . substr($unique_time['to']['value'], 2, 2);
							$bed_start = $unique_time['from']['bed'];
							$bed_end = $unique_time['to']['bed'];
							$time1_json = $pop_data['time'][$key]['from']['json'];
							$time2_json = $pop_data['time'][$key]['to']['json'];
							$time_start_value = $unique_time['from']['value'];
							$time_end_value = $unique_time['to']['value'];
							$date_value = $unique_time['day']['value'];
							$date_view = $unique_time['day']['view'];
						@endphp
						<div class="booking-field choice-time">
							<input value="0" class="time_index" type="hidden">
							<div class="booking-field-label label-data pt-2">
								<label class="">{{ isset($unique_time['day']['view'])?$unique_time['day']['view']:'' }}</label>
								<input name="date[{{ $key }}][day][view]" value="{{ isset($date_view)?$date_view:'' }}" type="hidden">
								<input name="date[{{ $key }}][day][value]" value="{{ isset($date_value)?$date_value:'' }}" type="hidden">
							</div>
							<div class="booking-field-content date-time">
								<div class="choice-data-time set-time time-start">
									<div class="set-time">
										<input name="date[{{ $key }}][from][value]" type="hidden" class="time_from time_value" readonly="readonly" value="{{ isset($time_start_value)?$time_start_value:'0' }}" />
										<input name="date[{{ $key }}][from][bed]" type="hidden" class="time_bed" readonly="readonly" value="{{ isset($bed_start)?$bed_start:'0' }}" />
										<input name="date[{{ $key }}][from][view]" type="text" id="time_bath_{{ $key }}1" class="time form-control js-set-time bg-white" data-date_value="{{ isset($date_value)?$date_value:'' }}" data-date_type="form" readonly="readonly" value="{{ isset($time_start)?$time_start:'－' }}" />
										<input name="time[{{ $key }}][from][json]" type="hidden" class="data-json_input" value="{{ isset($time1_json)?$time1_json:'' }}" />
										<input name="time[{{ $key }}][from][element]" type="hidden" value="time_bath_{{ $key }}1" />
									</div>
									<div class="icon-time mt-1">
									</div>
								</div>
								<div class="choice-data-time set-time time-end">
									<div class="set-time">
										<input name="date[{{ $key }}][to][value]" type="hidden" class="time_to time_value" readonly="readonly" value="{{ isset($time_end_value)?$time_end_value:'0' }}" />
										<input name="date[{{ $key }}][to][bed]" type="hidden" class="time_bed" readonly="readonly" value="{{ isset($bed_end)?$bed_end:'0' }}" />
										<input name="date[{{ $key }}][to][view]" type="text" id="time_bath_{{ $key }}2" class="time form-control js-set-time bg-white" data-date_value="{{ isset($date_value)?$date_value:'' }}" data-date_type="to" readonly="readonly" value="{{ isset($time_end)?$time_end:'－' }}" />
										<input name="time[{{ $key }}][to][json]" type="hidden" class="data-json_input" value="{{ isset($time2_json)?$time2_json:'' }}" />
										<input name="time[{{ $key }}][to][element]" type="hidden" value="time_bath_{{ $key }}2" />
									</div>
									<div class="icon-time mt-1"></div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			@else
			<div class="time-list">
			</div>
			@endif
			<div class="clearfix"></div>
		</div>
		<div class="booking-field">
			<div class="node-text booking-laber-padding">
				<div id="hint-repeat">※バスの場合、到着時間から30分以内の予約は出来ません。希望時間が選択できない場合は、バス到着時間をご確認ください。</div>
			</div>
		</div>
	</div>
</div>
<div class="booking-line font-weight-bold mt-3" id="div_pet_plus" style="display:none">
	<div class="booking-line-laber">
		<div class="line-laber">オプション</div>
		<div class="line-button">
			<img class=" btn-collapse btn-collapse-between" id="btn-collapse-between"  data-toggle="collapse" data-target=".collapse-between" src="{{ asset('sunsun/svg/plus.svg') }}" alt="Plus" />
		</div>
	</div>
	<!-- <hr class="booking-line-line"> -->
</div>
<div class="collapse collapse-between" id="div_pet" style="display:none">
	<div class="booking-block-between">
		<div class="">
			<div class="booking-field">
				<div class="booking-field-label  booking-laber-padding">
					<p class="text-left pt-2">{{config('booking.pet.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="pet_keeping" id="pet_keeping" class="form-control">
						@foreach($pet_keeping as $value)
							@if(isset($course_data['pet_keeping']) && ($value->kubun_id == $course_data['pet_keeping']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@elseif(isset($pop_data['pet_keeping']) && ($value->kubun_id == json_decode($pop_data['pet_keeping'], true)['kubun_id']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@else
								<option value='@json($value)'>{{ $value->kubun_value }}</option>
							@endif
						@endforeach
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
@if(isset($add_new_user) === false && isset($course_data['ref_booking_id']) === false)
	<div class="booking-line font-weight-bold mt-3">
		<div class="booking-line-laber">
			<div class="line-laber">宿泊</div>
			<div class="line-button">
				<img class=" btn-collapse btn-collapse-finish" id="btn-collapse-finish"  data-toggle="collapse" data-target=".collapse-finish" src="{{ asset('sunsun/svg/plus.svg') }}" alt="Plus" />
			</div>
		</div>
		<!-- <hr class="booking-line-line"> -->
	</div>
	@php
		$room_whitening = true;
		if(isset($course_data["stay_room_type"]) && ($course_data['stay_room_type'] != '01')){
			$room_whitening = false;
			$range_date_start= substr($course_data['stay_checkin_date'], 0, 4).'/'.substr($course_data['stay_checkin_date'], 4, 2).'/'.substr($course_data['stay_checkin_date'], 6, 2);
			$range_date_end = substr($course_data['stay_checkout_date'], 0, 4).'/'.substr($course_data['stay_checkout_date'], 4, 2).'/'.substr($course_data['stay_checkout_date'], 6, 2);
			$range_date_start_value = $course_data['stay_checkin_date'];
			$range_date_end_value = $course_data['stay_checkout_date'];
			$range_date_start_view = $range_date_start;
			$range_date_end_view = $range_date_end;
		}
		if(isset($pop_data["stay_room_type"]) && (json_decode($pop_data['stay_room_type'], true)['kubun_id'] != '01')){
			$room_whitening = false;
			$range_date_start= substr($pop_data['range_date_start-value'], 0, 4).'/'.substr($pop_data['range_date_start-value'], 4, 2).'/'.substr($pop_data['range_date_start-value'], 6, 2);
			$range_date_end = substr($pop_data['range_date_end-value'], 0, 4).'/'.substr($pop_data['range_date_end-value'], 4, 2).'/'.substr($pop_data['range_date_end-value'], 6, 2);
			$range_date_start_value = $pop_data['range_date_start-value'];
			$range_date_end_value = $pop_data['range_date_end-value'];
			$range_date_start_view = $pop_data['range_date_start-view'];
			$range_date_end_view = $pop_data['range_date_end-view'];
		}
	@endphp
	<div class="collapse collapse-finish">
		<div class="booking-block-finish">
			<div class="">
				<div class="booking-field">
					<div class="booking-field-label  booking-laber-padding">
						<p class="text-left pt-2">宿泊<span class="node-text">(部屋ﾀｲﾌﾟ)</span></p>
					</div>
					<div class="booking-field-content">
						<select name="stay_room_type" id="room" class="form-control" <?php if($setting == "0") echo "disabled" ?>>
							@foreach($stay_room_type as $value)
								@if(isset($course_data['stay_room_type']) && ($value->kubun_id == $course_data['stay_room_type']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@elseif(isset($pop_data['stay_room_type']) && ($value->kubun_id == json_decode($pop_data['stay_room_type'], true)['kubun_id']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@else
									<option value='@json($value)'>{{ $value->kubun_value }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>
				<div class="booking-field room" @if($room_whitening) style="display:none;" @endif>
					<div class="booking-field-label  booking-laber-padding">
						<p class="text-left pt-2">{{config('booking.stay_guest_num.label')}}</p>
					</div>
					<div class="booking-field-content">
						<select name="stay_guest_num" id="stay_guest_num" class="form-control">
							@foreach($stay_guest_num as $value)
								@if(isset($course_data['stay_guest_num']) && ($value->kubun_id == $course_data['stay_guest_num']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@elseif(isset($pop_data['stay_guest_num']) && ($value->kubun_id == json_decode($pop_data['stay_guest_num'], true)['kubun_id']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@else
									<option value='@json($value)'>{{ $value->kubun_value }}</option>
								@endif
							@endforeach
						</select>
						<select id="stay_guest_num_temp" style="display : none;">
							@foreach($stay_guest_num as $value)
								@if(isset($course_data['stay_guest_num']) && ($value->kubun_id == $course_data['stay_guest_num']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@elseif(isset($pop_data['stay_guest_num']) && ($value->kubun_id == json_decode($pop_data['stay_guest_num'], true)['kubun_id']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@else
									<option value='@json($value)'>{{ $value->kubun_value }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>
				<div class="booking-field room"  @if($room_whitening) style="display:none;" @endif>
					<div style="width: 100%;">
						<div style="width: 100%;">
							<input name="range_date_start-view" id="range_date_start-view" type="hidden" value="{{ isset($range_date_start_view)?$range_date_start_view:""  }}">
							<input name="range_date_end-view" id="range_date_end-view" type="hidden" value="{{ isset($range_date_end_view)?$range_date_end_view:""  }}">
							<input name="range_date_start-value" id="range_date_start-value" type="hidden" value="{{ isset($range_date_start_value)?$range_date_start_value:""  }}">
							<input name="range_date_end-value" id="range_date_end-value" type="hidden" value="{{ isset($range_date_end_value)?$range_date_end_value:""  }}">
							<div class="range_room booking-room input-daterange  date-range_block" id="choice-range-day">
								<div class="field-start-day date-range_block_left">
									<p class="node-text">{{config('booking.range_date.checkin')}}</p>
									<input name="range_date_start" data-format="yyyy/MM/dd" type="text" class="form-control date-book-input room_range_date bg-white"  readonly="readonly" id="range_date_start" value="{{ isset($range_date_start)?$range_date_start:'' }}">
								</div>
								<div class="field-center date-range_block_center">
									<p>&nbsp;</p>
									<p class="character-date pt-2">～</p>
								</div>
								<div class="field-end-day date-range_block_right">
									<p class="node-text">{{config('booking.range_date.checkout')}}</p>
									<input name="range_date_end" data-format="yyyy/MM/dd" type="text" class="form-control date-book-input room_range_date bg-white"  readonly="readonly" id="range_date_end" value="{{ isset($range_date_end)?$range_date_end:'' }}">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif