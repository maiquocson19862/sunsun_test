@php
	$disable_booking_date = null;
	if(isset($course_data['service_date_start'])){
		$disable_booking_date = substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
	}
	if( (!isset($course_data['course'])) || ($course_data['course'] != '01') ){
		$course_data = NULL;
		$course_time = NULL;
	}
	if(isset($pop_data)){
		$pop_data = json_decode($pop_data, true);
	}
	if(!isset($pop_data) || (json_decode($pop_data['course'], true)['kubun_id'] != '01')){
		$pop_data = NULL;
	}
@endphp
<div class="booking-block">
	<div class="collapse collapse-top show">
		<div class="booking-field">
			<div class="booking-field-label">
			</div>
			<div class="booking-field-content">
				<p class="node-text text-left mt-1 mb-0">入浴時間約20分</p>
				<p class="node-text text-left mb-2">(全体の滞在時間約90分)</p>
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
		@php
			$age_type = 30;
			$age_type = isset($course_data['age_type'])?$course_data['age_type']:$age_type;
			$age_type = isset($pop_data['age_type'])?$pop_data['age_type']:$age_type;
			$show_age_value = true;
			if($age_type != 3 && $age_type != 30){
				$show_age_value = false;
			}
			$val_age_type = isset($course_data['age_type']) ? $course_data['age_type'] : 30;
			$display_old = ($age_type == 1 || $age_type == 2) ? "" : " style='display:none ! important' ";
		@endphp
		<div class="booking-field">
			<div class="booking-field-label  booking-laber-padding">
				<p class="text-left pt-2">{{config('booking.age.label')}}</p>
			</div>
			<div class="booking-field-content">
				<div class="button-age">
					<input id="agecheck" name='age_type' type="hidden" value="{{  isset($age_type)?$age_type:$val_age_type  }}">
					<div class="button-age-left">
						<button type="button" class="btn btn-block form-control text-dark mx-0 agecheck @if($age_type == 1) color-active @else btn-outline-warning   @endif" 
						{!! $display_old !!} value="1">{{config('booking.age.age1')}}</button>
						<button type="button" class="btn btn-block form-control text-dark  margin-top-mini mx-0 agecheck @if($age_type == 30 ||$age_type == 3) color-active @else btn-outline-warning  @endif" value="{{ $val_age_type }}">{{config('booking.age.age3')}}</button>
					</div>
					<div class="button-age-right">
						<button type="button" class="btn btn-block form-control btn-outline-warning text-dark mx-0 agecheck @if($age_type == 2) color-active @else btn-outline-warning   @endif" {!! $display_old !!} value="2">学生<span class="node-text">(中学生以上)</span></button>
						<div class="age-col margin-top-mini" style="display:none !important">
							<div class="age-left"  @if(!$show_age_value) style="visibility: hidden;" @endif>
								<select id="age_value" name="age_value" class="form-control">
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
						<button type="button" class="btn btn-block form-control text-dark  margin-top-mini mx-0 agecheck @if($age_type == 31) color-active @else btn-outline-warning @endif" value="31">{{config('booking.age.age4')}}</button>
					</div>
				</div>
			</div>
		</div>
		@if(isset($add_new_user) === false)
			@php
				$booking_date = '';
				$date_value = null;
				$date_view = null;
				if(isset($course_data['service_date_start'])){
					$booking_date = substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
					$date_value = $course_data['service_date_start'];
					$date_view = $booking_date;
				}
				if(isset($pop_data['date-value'])){
					$booking_date = substr($pop_data['date-value'], 0, 4).'/'.substr($pop_data['date-value'], 4, 2).'/'.substr($pop_data['date-value'], 6, 2);
				}
			@endphp
			<div class="booking-field {{(isset($request_post['add_new_user']) && $request_post['add_new_user'] == 'on')?'hidden':''}} ">
				<div class="booking-field-label  booking-laber-padding">
					<p class="text-left pt-2">{{config('booking.date.label')}}</p>
				</div>
				<input name="date-view" id="date-view" type="hidden" value="{{ isset($date_view)?$date_view:'' }}">
				<input name="date-value" id="date-value" type="hidden" value="{{ isset($date_value)?$date_value:'' }}">
				<div class="booking-field-content">
					@if(isset($disable_booking_date) === false)
						<input id="date" data-format="yyyy/MM/dd" type="text" class="form-control date-book-input bg-white" readonly="readonly" value="{{ $booking_date }}" />
					@else
						<input id="date" data-format="yyyy/MM/dd" type="text" class="form-control" readonly="readonly" disabled value="{{ $disable_booking_date }}" />
					@endif
				</div>
			</div>
		@endif
		@php
			$first_time = NULL;
			$first_time_data = NULL;
			if(is_array($course_time)){
				foreach($course_time as $first_time){
					break;
				}
				if(isset($first_time['service_time_1'])){
					$first_time_data = substr($first_time['service_time_1'], 0, 2) . ":" . substr($first_time['service_time_1'], 2, 2);
				}
			}
		@endphp
		<div class="booking-field">
			<div class="booking-field-label  booking-laber-padding">
				<p class="text-left pt-2">{{config('booking.time.label')}}</p>
			</div>
			@if(is_array($course_time))
				@php
				$i = 0;
				@endphp
				@foreach($course_time as $s_time)
					@php
						$s_time_data = substr($s_time['service_time_1'], 0, 2) . ":" . substr($s_time['service_time_1'], 2, 2);
					@endphp
					@if($i == 0)
					<div class="booking-field-content">
						<div class="timedate-block set-time">
							<input name="time[0][view]" type="text" class="form-control time js-set-time booking-time bg-white" id="error_time_0" readonly="readonly" value="{{ $s_time_data }}" />
							<input name="time[0][value]" class="time_value" id="time[0][value]" type="hidden" value="{{ $s_time['service_time_1'] }}">
							<input name="time[0][bed]" class="time_bed" id="time[0][bed]" type="hidden" value="{{ $s_time['notes'] }}">
							<input name="time[0][gender]" class="time_gender" id="time[0][gender]" type="hidden" value="0">
							<input name="time[0][json]" class="data-json_input" id="time[0][json]" type="hidden" value="{{ $s_time['time_json'] }}">
							<input name="time[0][element]" id="time[0][element]" type="hidden" value="error_time_0">
							<input name="time[0][edit]" id="edit_0_time" type="hidden" value="0">
						</div>
						<div class="time-content">
					@else
						<div class="block-content-1 margin-top-mini">
							<div class="block-content-1-left">
								<div class="timedate-block set-time">
									<input name="time[{{ $i }}][view]" type="text" class="form-control time js-set-time booking-time bg-white" readonly="readonly" value="{{ $s_time_data }}" />
									<input name="time[{{ $i }}][value]" class="time_value" id="time[{{ $i }}][value]" type="hidden" value="{{ $s_time['service_time_1'] }}">
									<input name="time[{{ $i }}][bed]" class="time_bed" id="time[{{ $i }}][bed]" type="hidden" value="{{ $s_time['notes'] }}">
									<input name="time[{{$i}}][json]" class="data-json_input" type="hidden" value="{{ $s_time['time_json'] }}">
									<input name="time[{{$i}}][element]" id="time[{{$i}}][element]" type="hidden" value="error_time_">
									<input name="time[{{$i}}][edit]" id="edit_{{$i}}_time" type="hidden" value="0">
								</div>
							</div>
							<div class="block-content-1-right"><img class="svg-button" src="/sunsun/svg/close.svg" alt="Close" /></div>
						</div>
					@endif
					@php
						$i++;
					@endphp
				@endforeach
					</div>
				</div>
			@elseif($pop_data != null && is_array($pop_data['time']))
						@foreach($pop_data['time'] as $key => $s_time)
							@php
								$s_time_data = substr($s_time['value'], 0, 2) . ":" . substr($s_time['value'], 2, 2);
							@endphp
							@if($key == 0)
								<div class="booking-field-content">
									<div class="timedate-block set-time">
										<input name="time[0][view]" type="text" class="form-control time js-set-time booking-time bg-white"  id="error_time_0" readonly="readonly"  value="{{ $s_time_data }}" />
										<input name="time[0][value]" class="time_value" id="time[0][value]" type="hidden" value="{{ $s_time['value'] }}">
										<input name="time[0][bed]" class="time_bed" id="time[0][bed]" type="hidden" value="{{ $s_time['bed'] }}">
										<input name="time[0][gender]" class="time_gender" id="time[0][gender]" type="hidden" value="0">
										<input name="time[0][json]" class="data-json_input" id="time[0][json]" type="hidden" value="{{ $s_time['json'] }}">
										<input name="time[0][element]" id="time[0][element]" type="hidden" value="error_time_0">
										<input name="time[0][edit]" id="edit_0_time" type="hidden" value="0">
									</div>
									<div class="time-content">
							@else
								<div class="block-content-1 margin-top-mini">
									<div class="block-content-1-left">
										<div class="timedate-block set-time">
											<input name="time[{{ $key }}][view]" type="text" class="form-control time js-set-time booking-time bg-white" id="error_time_{{ $key }}"  readonly="readonly" value="{{ $s_time_data }}" />
											<input name="time[{{ $key }}][value]" class="time_value" id="time[{{ $key }}][value]" type="hidden" value="{{ $s_time['value'] }}">
											<input name="time[{{ $key }}][bed]" class="time_bed" id="time[{{ $key }}][bed]" type="hidden" value="{{ $s_time['bed'] }}">
											<input name="time[{{$key}}][json]" class="data-json_input" type="hidden" value="{{ $s_time['json'] }}">
											<input name="time[{{$key}}][element]" id="time[{{$key}}][element]" type="hidden" value="error_time_{{ $key }}">
											<input name="time[{{$key}}][edit]" id="edit_{{$key}}_time" type="hidden" value="0">
										</div>
									</div>
									<div class="block-content-1-right"><img class="svg-button" src="/sunsun/svg/close.svg" alt="Close" /></div>
									</div>
							 @endif
						@endforeach
						</div>
					  </div>
			@else
			<div class="booking-field-content">
				<div class="timedate-block set-time">
					<input name="time[0][view]" type="text" class="form-control time js-set-time booking-time bg-white" id="error_time_0" readonly="readonly" value="－" />
					<input name="time[0][value]" class="time_value" id="time[0][value]" type="hidden" value="0">
					<input name="time[0][bed]" class="time_bed" id="time[0][bed]" type="hidden" value="0">
					<input name="time[0][gender]" class="time_gender" id="time[0][gender]" type="hidden" value="0">
					<input name="time[0][json]" class="data-json_input"  id="time[0][json]" type="hidden" value="">
					<input name="time[0][element]" id="time[0][element]" type="hidden" value="error_time_0">
				</div>
				<div class="time-content">
				</div>
			</div>
			@endif
		</div>
		<div class="booking-field pb-0">
			<div class="node-text booking-laber-padding">
				<div id="hint-repeat">※バスの場合、到着時間から30分以内の予約は出来ません。希望時間が選択できない場合は、バス到着時間をご確認ください。</div>
				<div>※酵素浴を1日2回利用される場合は[酵素追加]をクリックし、2回目の時間を選択してください。</div>
			</div>
		</div>
		<div class="booking-field">
			<div class="booking-field-label">
			</div>
			<div class="booking-field-content">
				<div class="block-content-2 margin-top-mini">
					<div class="block-content-2-left"></div>
					<div class="block-content-2-right">
						<button type="button" class="btn btn-block form-control color-active text-dark" id="add-time">酵素浴追加</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="booking-line font-weight-bold mt-3">
	<div class="booking-line-laber">
		<div class="line-laber">オプション</div>
		<div class="line-button">
			<img class="btn-collapse btn-collapse-between" id="btn-collapse-between"  data-toggle="collapse" data-target=".collapse-between" src="{{ asset('sunsun/svg/plus.svg') }}" alt="Plus" />
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
			<div class="booking-field">
				<div class="booking-field-label booking-laber-padding">
					<p class="text-left pt-2 custom-font-size">{{config('booking.whitening.label')}}</p>
				</div>
				<div class="booking-field-content">
					<select name="whitening" id="whitening" class="form-control">
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
						<input name='whitening-time_view' type="text" class="form-control time js-set-room_wt bg-white"  readonly="readonly" id="whitening-time_view"  value="{{ $whitening_view }}" />
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
				<div class="booking-field-label booking-laber-padding">
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
					<div class="booking-field-label booking-laber-padding">
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
					<div class="booking-field-label booking-laber-padding">
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
				<div class="booking-field-content room" @if($room_whitening) style="display:none;" @endif>
					<div class="booking-laber-padding">
						<p class="text-left pt-2">{{config('booking.range_date.label')}}</p>
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
						<select name="breakfast" class="form-control">
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