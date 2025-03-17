@php
	$disable_booking_date = null;
	if(isset($course_data['service_date_start'])){
		$disable_booking_date = substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
	}
	if( (!isset($course_data['course'])) || ($course_data['course'] != '08') ){
		$course_data = NULL;
	}
	if(isset($pop_data)){
		$pop_data = json_decode($pop_data, true);
	}
	if(!isset($pop_data) || (json_decode($pop_data['course'], true)['kubun_id'] != '08')){
		$pop_data = NULL;
	}
@endphp
<div class="booking-block">
	<div class="collapse collapse-top show">
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
		<div class="booking-field"  style="display:none !important">
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
		@if(isset($add_new_user) === false)
			@php
				$booking_date = '';
				if(isset($course_data['service_date_start'])){
					$booking_date = substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
				}
				if(isset($pop_data['date-value'])){
					$booking_date = substr($pop_data['date-value'], 0, 4).'/'.substr($pop_data['date-value'], 4, 2).'/'.substr($pop_data['date-value'], 6, 2);
				}
			@endphp
			<div class="booking-field {{(isset($request_post['add_new_user']) && $request_post['add_new_user'] == 'on')?'hidden':''}}">
				<div class="booking-field-label  booking-laber-padding">
					<p class="text-left pt-2">{{config('booking.date.label')}}</p>
				</div>
				<input name="date-view" id="date-view" type="hidden" value="">
				<input name="date-value" id="date-value" type="hidden" value="">
				<div class="booking-field-content">
					<div class="timedate-block date-warp">
						@if(isset($disable_booking_date) === false)
							<input id="date" data-format="yyyy/MM/dd" type="text" class="form-control date-book-input bg-white" readonly="readonly" value="{{ $booking_date }}" />
						@else
							<input id="date" data-format="yyyy/MM/dd" type="text" class="form-control" readonly="readonly" disabled value="{{ $disable_booking_date }}" />
						@endif
					</div>
				</div>
			</div>
		@endif
		@php
			if(isset($course_data['service_time_1'])){
				$time1 = substr($course_data['service_time_1'], 0, 2) . ":" . substr($course_data['service_time_1'], 2, 2);
				$time2 = substr($course_data['service_time_2'], 0, 2) . ":" . substr($course_data['service_time_2'], 2, 2);
				$bed1 = substr($course_data['bed'], 0, 1);
				$bed2 = substr($course_data['bed'], 2, 1);
				$temp_time_json = explode('-', $course_data['time_json']);
				$time1_json = $temp_time_json[0];
				$time2_json = $temp_time_json[1];
				$time1_value = $course_data['service_time_1'];
				$time2_value = $course_data['service_time_2'];
			}
			if(isset($pop_data['time1-value'])){
				$time1 = substr($pop_data['time1-value'], 0, 2) . ":" . substr($pop_data['time1-value'], 2, 2);
				$time2 = substr($pop_data['time2-value'], 0, 2) . ":" . substr($pop_data['time2-value'], 2, 2);
				$bed1 = $pop_data['time1-bed'];
				$bed2 = $pop_data['time2-bed'];
				$time1_json = $pop_data['time'][0]['json'];
				$time2_json = $pop_data['time'][1]['json'];
				$time1_value = $pop_data['time1-value'];
				$time2_value = $pop_data['time2-value'];
			}
		@endphp
		<div class="booking-field">
			<div class="booking-laber-padding">
				<p class="text-left pt-2 mb-0">{{config('booking.time.label')}}</p>
			</div>
		</div>
		<div class="booking-field">
			<div class="booking-field-label  booking-laber-padding pl-1">
				<p class="text-left pt-2 pl-3  node-text">{{config('booking.time.laber1')}}</p>
			</div>
			<div class="booking-field-content">
				<div class="timedate-block set-time">
					<input name="time1-value" id="time1-value" class="time_value" type="hidden" value="{{ isset($time1_value)?$time1_value:'0' }}">
					<input name="time1-bed" id="time1-bed" class="time1-bed" type="hidden" value="{{ isset($bed1)?$bed1:'0' }}">
					<input name="time1-view" type="text" class="form-control time js-set-time bg-white"  readonly="readonly" id="time1-view" value="{{ isset($time1)?$time1:'－' }}" data-date_type="shower_1">
					<input name="time[0][json]" class="data-json_input" id="time[0][json]"  type="hidden" value="{{ isset($time1_json)?$time1_json:'' }}">
					<input name="time[0][element]" type="hidden" value="time1-view">
				</div>
			</div>
		</div>
		<div class="booking-field">
			<div class="node-text booking-laber-padding">
				<div id="hint-repeat">※バスの場合、到着時間から30分以内の予約は出来ません。希望時間が選択できない場合は、バス到着時間をご確認ください。</div>
			</div>
		</div>
	</div>
</div>
<div class="booking-line font-weight-bold mt-3">
	<div class="booking-line-laber">
		<div class="line-laber">オプション</div>
		<div class="line-button">
			<img class=" btn-collapse btn-collapse-between" id="btn-collapse-between"  data-toggle="collapse" data-target=".collapse-between" src="{{ asset('sunsun/svg/plus.svg') }}" alt="Plus" />
		</div>
	</div>
	<!-- <hr class="booking-line-line"> -->
</div>
<div class="collapse collapse-between">
	<div class="booking-block-between">
		<div class="">
			<div class="booking-field">
				<div class="booking-field-label booking-laber-padding">
					<p class="text-left pt-2">{{config('booking.lunch.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="lunch" id="lunch" class="form-control">
						@foreach($lunch as $value)
							@if(isset($course_data['lunch']) && ($value->kubun_id == $course_data['lunch']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@elseif(isset($pop_data['lunch']) && ($value->kubun_id == json_decode($pop_data['lunch'], true)['kubun_id']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@else
								<option value='@json($value)'>{{ $value->kubun_value }}</option>
							@endif
						@endforeach
					</select>
					<p class="node-text text-left mt-2 mb-2">{!! config('booking.lunch.note') !!}{!! config('booking.lunch.note_confirm1') !!}</p>
				</div>
			</div>
			<div class="booking-field" style="display: none">
				<div class="booking-field-label  booking-laber-padding">
					<p class="text-left pt-2 custom-font-size">{{config('booking.whitening.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="whitening" id="whitening" class="form-control white-up-skin">
						@foreach($whitening as $value)
							@if(isset($course_data['whitening']) && ($value->kubun_id == $course_data['whitening']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@elseif(isset($pop_data['whitening']) && ($value->kubun_id == json_decode($pop_data['whitening'], true)['kubun_id']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@else
								<option value='@json($value)'>{{ $value->kubun_value }}</option>
							@endif
						@endforeach
					</select>
				</div>
			</div>
			@php
				$display_whitening = true;
				if(isset($course_data["whitening"]) && ($course_data['whitening'] == '02')){
					$display_whitening = false;
				}
				if(isset($pop_data["whitening"]) && (json_decode($pop_data['whitening'], true)['kubun_id'] == '02')){
					$display_whitening = false;
				}
				$display_whitening2 = true;
				if(isset($course_data["whitening2"]) && ($course_data['whitening2'] == '02')){
					$display_whitening2 = false;
				}
				if(isset($pop_data["whitening2"]) && (json_decode($pop_data['whitening2'], true)['kubun_id'] == '02')){
					$display_whitening2 = false;
				}
				$whitening_time = '0';
				$whitening_json = '';
				$whitening_view = '－';
				if(isset($course_data['whitening_time'])  === true && isset($course_data['whitening_time_json'])  === true &&  isset($course_data['whitening_time-view']) === true ){
					$whitening_time = $course_data['whitening_time'];
					$whitening_json = $course_data['whitening_time_json'];
					$whitening_view = $course_data['whitening_time-view'];
				}else if(isset($pop_data['whitening-time_value']) === true && isset($pop_data['whitening_data']['json']) === true && isset($pop_data['whitening-time_view']) === true){
					$whitening_time = $pop_data['whitening-time_value'];
					$whitening_json = $pop_data['whitening_data']['json'];
					$whitening_view = $pop_data['whitening-time_view'];
				}
			@endphp
			<div class="booking-field whitening"  @if($display_whitening) style="display:none;" @endif>
				<div class="booking-field-label booking-laber-padding">
				</div>
				<div class="booking-field-content">
					<div class="node-text">ご利用</div>
					<select name="whitening_repeat" id="whitening_repeat" class="form-control">
						@if(isset($course_data['whitening_repeat']) && ($course_data['whitening_repeat'] == 1))
							<option selected value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($course_data['whitening_repeat']) && ($course_data['whitening_repeat'] == 0))
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option selected value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($pop_data['whitening_repeat']) && ($pop_data['whitening_repeat'] == 1))
							<option selected value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($pop_data['whitening_repeat']) && ($pop_data['whitening_repeat'] == 0))
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option selected value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@else
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@endif
					</select>
					<div class="node-text pt-2">{{ config('booking.whitening.note') }}</div>
				</div>
			</div>
			<div class="booking-field whitening whiteninghd">
				<div class="booking-field-label booking-laber-padding">
				</div>
				<div class="booking-field-content">
					<div class="node-text">{{ config('booking.whitening.label_time') }}</div>
					<div class="timedate-block set-time">
						<input name='whitening-time_view' type="text" class="form-control time js-set-room_wt bg-white" id="whitening-time_view"  readonly="readonly" value="{{ $whitening_view }}" />
						<input name='whitening-time_value' id="whitening-time_value" type="hidden" value="{{ $whitening_time }}"/>
						<input name="whitening_data[json]" class="data-json_input" id="whitening_data[json]" type="hidden" value="{{ $whitening_json  }}">
						<input name="whitening_data[element]" type="hidden" value="whitening-time_view">
					</div>
				</div>
			</div>
			<div class="booking-field option_hd">
				<div class="booking-field-label booking-laber-padding">
					<p class="text-left pt-2 custom-font-size">{{config('booking.whitening2.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="whitening2" id="whitening2" class="form-control">
						@foreach($whitening as $value)
							@if(isset($course_data['whitening2']) && ($value->kubun_id == $course_data['whitening2']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@elseif(isset($pop_data['whitening2']) && ($value->kubun_id == json_decode($pop_data['whitening2'], true)['kubun_id']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@else
								<option value='@json($value)'>{{ $value->kubun_value }}</option>
							@endif
						@endforeach
					</select>
				</div>
			</div>
			<div class="booking-field whitening2"  @if($display_whitening2) style="display:none;" @endif>
				<div class="booking-field-label booking-laber-padding">
				</div>
				<div class="booking-field-content">
					<div class="node-text">ご利用</div>
					<select name="whitening_repeat2" id="whitening_repeat2" class="form-control">
						@if(isset($course_data['whitening_repeat2']) && ($course_data['whitening_repeat2'] == 1))
							<option selected value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($course_data['whitening_repeat2']) && ($course_data['whitening_repeat2'] == 0))
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option selected value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($pop_data['whitening_repeat2']) && ($pop_data['whitening_repeat2'] == 1))
							<option selected value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@elseif(isset($pop_data['whitening_repeat2']) && ($pop_data['whitening_repeat2'] == 0))
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option selected value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@else
							<option value='1'>{{ config('booking.repeat_user.options.no') }}</option>
							<option value='0'>{{ config('booking.repeat_user.options.yes') }}</option>
						@endif
					</select>
				</div>
			</div>
			<div class="booking-field option_hd">
				<div class="booking-field-label booking-laber-padding">
					<p class="text-left pt-2 custom-font-size">{{config('booking.core_tuning.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="core_tuning" id="core_tuning" class="form-control">
						@foreach($whitening as $value)
							@if(isset($course_data['core_tuning']) && ($value->kubun_id == $course_data['core_tuning']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@elseif(isset($pop_data['core_tuning']) && ($value->kubun_id == json_decode($pop_data['core_tuning'], true)['kubun_id']))
								<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
							@else
								<option value='@json($value)'>{{ $value->kubun_value }}</option>
							@endif
						@endforeach
					</select>
				</div>
			</div>
			<div class="booking-field" id="div_pet" style="display:none">
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
				<div class="booking-field room">
					<div class="booking-field-label booking-laber-padding">
						<p class="text-left pt-2">モーニング</p>
					</div>
					<div class="booking-field-content">
						<select name="breakfast" id="breakfast" class="form-control">
							@foreach($breakfast as $value)
								@if(isset($course_data['breakfast']) && ($value->kubun_id == $course_data['breakfast']))
									<option selected value='@json($value)'>{{ $value->kubun_value }}</option>
								@elseif(isset($pop_data['breakfast']) && ($value->kubun_id == json_decode($pop_data['breakfast'], true)['kubun_id']))
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
@endif