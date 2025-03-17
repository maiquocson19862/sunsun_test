<div class="linex mt-0">
    @if($i != 1)
        <p class="font-weight-bold">予約{{ $i }}</p>
        <br>
    @endif
    @if(isset($change_check) === true)
        <p>ご利用： {{ $admin_value_customer[$i - 1]['repeat_user'] }}</p>
        @if($repeat_user != '02')
            <p>{!! config('const.message.please_15_minus') !!}</p>
        @endif
    @else
        <p>ご利用： {{ $repeat_user->kubun_value }}</p>
        @if($repeat_user->kubun_id != '02')
            <p>{!! config('const.message.please_15_minus') !!}</p>
        @endif
    @endif
</div>
@if(($key == 0) && isset($change_check) === false)
    @php
        $transport = json_decode($customer['transport']);
    @endphp

    @if($transport->kubun_id == '01' )
        <div class="line">
            <div class="line1">
            交通手段 :
            </div>
            <span style="display: none">mark_space</span>
            <div class="line2">
                <p>{{ $transport->kubun_value }}</p>
            </div>
        </div>
    @else
        @php
            $bus_arrive_time_slide = json_decode($customer['bus_arrive_time_slide']);
            $pick_up = json_decode($customer['pick_up']);
        @endphp
        <div class="line">
            <div class="line1">
            交通手段 :
            </div>
            <span style="display: none">mark_space</span>
            <div class="line2">
                <p>{{ $transport->kubun_value }}</p>
                <p>{{ $bus_arrive_time_slide->kubun_value }}</p>
                @if($pick_up->kubun_id == '01')
                    <p>送迎あり</p>
                @else
                    <p>送迎なし</p>
                @endif
            </div>
        </div>
        <div class="line">
            <p>※バスのチケット予約ではありません。</p>
        </div>
    @endif
    <span style="display: none">mark_newline</span>
    @if($course->kubun_id == '01' || $course->kubun_id == '02' || $course->kubun_id == '03' || $course->kubun_id == '07' || $course->kubun_id == '08' || $course->kubun_id == '09' || $course->kubun_id == '10')
    <div class="linex">
        <p>予約日: {{ $data['date-view'] }}</p>
        <div class="line1"></div>
        <div class="line2"></div>
    </div>
    @endif
@elseif(isset($change_check) === true)
    @php
        $transport = $data->transport;
    @endphp

    @if($key == 0)
        @if($transport == '01' )
            <div class="line">
                <div class="line1">
                交通手段 :
                </div>
                <span style="display: none">mark_space</span>
                <div class="line2">
                    <p>{{ $admin_value_customer[$i - 1]['transport'] }}</p>
                </div>
            </div>
        @else
            @php
                $bus_arrive_time_slide = $data->bus_arrive_time_slide;
                $pick_up = $data->pick_up;
            @endphp
            <div class="line">
                <div class="line1">
                交通手段 :
                </div>
                <span style="display: none">mark_space</span>
                <div class="line2">
                    <p>{{ $admin_value_customer[$i - 1]['transport'] }}</p>
                    <p>{{ $admin_value_customer[$i - 1]['bus_arrive_time_slide'] }}</p>
                    @if($pick_up == '01')
                        <p>送迎あり</p>
                    @else
                        <p>送迎なし</p>
                    @endif
                </div>
            </div>
        @endif
        @php
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
        <span style="display: none">mark_newline</span>
        @if($course == '01' || $course == '02' || $course == '03' || $course == '07' || $course == '08' || $course == '09' || $course == '10' )
        <div class="linex">
            <p>予約日: <span style="display: none">mark_space</span>{{ substr($data->service_date_start, 0, 4) . "年" . substr($data->service_date_start, 4, 2) . "月" . substr($data->service_date_start, 6, 2) . "日" ."(" . $weekMap[date('w', strtotime($data->service_date_start))] . ")" }}</p>
            <div class="line1"></div>
            <div class="line2"></div>
        </div>
        @endif
    @endif
@endif
@if(isset($change_check) === true)
    @if($course == '01')
        @include('sunsun.front.sub.confirm1_admin')
    @elseif($course == '02')
        @include('sunsun.front.sub.confirm2_admin')
    @elseif($course == '03')
        @include('sunsun.front.sub.confirm3_admin')
    @elseif($course == '04' || $course == '06')
        @include('sunsun.front.sub.confirm4_admin')
    @elseif($course == '05')
        @include('sunsun.front.sub.confirm5_admin')
    @elseif($course == '07')
        @include('sunsun.front.sub.confirm7_admin')
    @elseif($course == '08')
        @include('sunsun.front.sub.confirm8_admin')
    @elseif($course == '09')
        @include('sunsun.front.sub.confirm9_admin')
    @elseif($course == '10')
        @include('sunsun.front.sub.confirm10_admin')
    @endif
@else
    @if($course->kubun_id == '01')
        @include('sunsun.front.sub.confirm1')
    @elseif($course->kubun_id == '02')
        @include('sunsun.front.sub.confirm2')
    @elseif($course->kubun_id == '03')
        @include('sunsun.front.sub.confirm3')
    @elseif($course->kubun_id == '04' || $course->kubun_id == '06')
        @include('sunsun.front.sub.confirm4')
    @elseif($course->kubun_id == '05')
        @include('sunsun.front.sub.confirm5')
    @elseif($course->kubun_id == '07')
        @include('sunsun.front.sub.confirm7')
    @elseif($course->kubun_id == '08')
        @include('sunsun.front.sub.confirm8')
    @elseif($course->kubun_id == '09')
        @include('sunsun.front.sub.confirm9')
    @elseif($course->kubun_id == '10')
        @include('sunsun.front.sub.confirm10')
    @endif
@endif
<?php // 2020/06/05 ?>