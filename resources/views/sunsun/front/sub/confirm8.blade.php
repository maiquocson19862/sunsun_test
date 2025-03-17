@php
	$gender = isset($data['gender'])?json_decode($data['gender']):"";
	$age_value = isset($data['age_value'])?$data['age_value']:"";
@endphp
<div class="linex">
	<p>性別：{{ $gender->kubun_value }}</p>
	<div class="line1">
	</div>
	<div class="line2">
	</div>
</div>
<!-- <div class="linex">
	<p>年齢：{{ $age_value }}歳</p>
	<div class="line1">
	</div>
	<div class="line2">
	</div>
</div> -->
<div class="linex">
	<p>コース: {{ $course->kubun_value }}</p>
	<div class="line1">
	</div>
	<div class="line2">
	</div>
</div>
<div class="line">
	<div class="line1">
	予約時間:
	</div>
	<div class="line2">
		<span style="display: none">mark_space</span>
		<p>{{ $data['time1-view'] }}</p>
	</div>
</div>
@php
	$lunch = (json_decode($data['lunch'])->kubun_value == "無し")?"":"1名";
	$whitening = json_decode($data['whitening']);
	$whitening_repeat = ($data['whitening_repeat'] == 1)?config('booking.repeat_user.options.no'):config('booking.repeat_user.options.yes');
	$whitening_data = ($whitening->kubun_id == '02')?$whitening_repeat:"";
	$whitening2 = json_decode($data['whitening2']);
    $whitening_repeat2 = ($data['whitening_repeat2'] == 1)?config('booking.repeat_user.options.no'):config('booking.repeat_user.options.yes');
    $whitening_data2 = ($whitening2->kubun_id == '02')?$whitening_repeat2:"";
    $core_tuning = json_decode($data['core_tuning'])->kubun_id;
@endphp
@if($lunch != "" || $whitening_data2 != "" || $core_tuning == "02")
<hr class="line-x">
<span style="display: none">mark_newline</span>
<div class="line">
	<div class="line1">
	オプション
	</div>
	<div class="line2">
		<p>{{ ($lunch != "")?"ランチ：" . $lunch:"" }}</p>
		<p>{{ ($whitening_data2 != "")?config('booking.whitening2.label')."：".$whitening_data2:"" }}</p>
		<p>{{ ($core_tuning == "02")?config('booking.core_tuning.label'):"" }}</p>
	</div>
</div>
@endif
@if($key == 0)
	@php
		$stay_room_type = isset($data['stay_room_type'])?json_decode($data['stay_room_type']):"";
		$stay_guest_num = isset($data['stay_guest_num'])?json_decode($data['stay_guest_num']):"";
		if(isset($data['breakfast'])){
			$breakfast = (json_decode($data['breakfast'])->kubun_value == "無し")?"":json_decode($data['breakfast'])->kubun_value;
		}
	@endphp
	@if(isset($stay_room_type->kubun_value))
		@if($stay_room_type->kubun_value != config('booking.room.options.no'))
		<hr class="line-x">
		<span style="display: none">mark_newline</span>
		<div class="line">
			<div class="line1">
			宿泊
			</div>
			<div class="line2">
				<p>部屋ﾀｲﾌﾟ：{{ $stay_room_type->kubun_value }}</p>
				<p>宿泊人数：{{ $stay_guest_num->kubun_value }}</p>
				<p>宿泊日</p>
				<div class="line3">
					<p class="node-text">チェックイン：{{ $data['range_date_start-view'] }}</p>
					<p class="node-text">チェックアウト：{{ $data['range_date_end-view'] }}</p>
				</div>
				<p>{{ ($breakfast != "")?"モーニング:". $breakfast:"" }}</p>
			</div>
		</div>
		@endif
	@endif
@endif