@php
    $gender = $data->gender;
    $age_value = $data->age_value;
    $age_type = $data->age_type;
    $weekMap = [
        0 => '日',
        1 => '月',
        2 => '火',
        3 => '水',
        4 => '木',
        5 => '金',
        6 => '土',
    ];
@endphp
<div class="linex">
    <p>性別：{{ $admin_value_customer[$i - 1]['gender'] }}</p>
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
    <p>コース：<span style="display: none">mark_space</span>{{ $admin_value_customer[$i - 1]['course'] }}</p>
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
		<p>{{ substr($data->service_time_1, 0, 2) . ":" . substr($data->service_time_1, 2, 2) }}</p>
	</div>
</div>
@php
    $lunch = ($admin_value_customer[$i - 1]['lunch'] == "無し")?"":"1名";
    $pet_keeping = ($admin_value_customer[$i - 1]['pet_keeping'] == "追加しない")?"":"追加する";
    $whitening2 = $data->whitening2;
    $whitening_repeat2 = ($data->whitening_repeat2 == 1)?config('booking.repeat_user.options.no'):config('booking.repeat_user.options.yes');
    $whitening_data2 = ($whitening2 == '02')?$whitening_repeat2:"";
    $core_tuning = $data->core_tuning;
@endphp
@if(($lunch != "") || ($pet_keeping != "") || $whitening_data2 != "" || $core_tuning == "02")
    <hr class="line-x">
    <span style="display: none">mark_newline</span>
    <div class="line">
        <div class="line1">
        オプション
        </div>
        <div class="line2">
            <p>{{ ($lunch != "")?"ランチ：" . $lunch:"" }}</p>
            <p>{{ ($whitening_data2 != "")?config('booking.whitening2.label')."：". $whitening_data2:"" }}</p>
            <p>{{ ($core_tuning == "02")?config('booking.core_tuning.label'):"" }}</p>
            <p>{{ ($pet_keeping != "")?"ペット預かり：" . $pet_keeping:"" }}</p>
        </div>
    </div>
@endif
@if($key == 0)
    @php
        $stay_room_type = $data->stay_room_type;
        $stay_guest_num = $data->stay_guest_num;

        $breakfast = $data->breakfast;

    @endphp
    @if($stay_room_type !== '01')
        <hr class="line-x">
        <span style="display: none">mark_newline</span>
        <div class="line">
            <div class="line1">
            宿泊
            </div>
            <div class="line2">
                <p>部屋ﾀｲﾌﾟ：{{ $admin_value_customer[$i - 1]['stay_room_type'] }}</p>
                <p>宿泊人数：{{ $admin_value_customer[$i - 1]['stay_guest_num'] }}</p>
                <p>宿泊日</p>
                <div class="line3">
                    <p class="node-text">チェックイン：{{ substr($data->stay_checkin_date, 0, 4) . "年" . substr($data->stay_checkin_date, 4, 2) . "月" . substr($data->stay_checkin_date, 6, 2) . "日(" . $weekMap[date('w', strtotime($data->stay_checkin_date))] . ")" }}</p>
                    <p class="node-text">チェックアウト：{{ substr($data->stay_checkout_date, 0, 4) . "年" . substr($data->stay_checkout_date, 4, 2) . "月" . substr($data->stay_checkout_date, 6, 2) . "日(" . $weekMap[date('w', strtotime($data->stay_checkout_date))] . ")" }} </p>
                </div>
                <p>{{ ($admin_value_customer[$i - 1]['breakfast'] != null)?"モーニング:". $admin_value_customer[$i - 1]['breakfast']:"" }}</p>
            </div>
        </div>
    @endif
@endif