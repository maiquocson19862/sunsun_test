@php
    $service_pet_num = json_decode($data['service_pet_num']);
    $notes = $data['notes'];
@endphp
<div class="linex">
    <p>コース: {{ $course->kubun_value }}</p>
    <div class="line1">
    </div>
    <div class="line2">
    </div>
</div>
@if($key == 0)
    <div class="linex">
        予約日: {{ $data['date-view'] }}
        <div class="line1">
        </div>
        <div class="line2">
        </div>
    </div>
@endif
<div class="linex">
    <p>予約時間: {{ $data['time_room'] }}</p>
    <div class="line1">
    </div>
    <div class="line2">
    </div>
</div>
<div class="linex">
    <p>ペット数：{{ $service_pet_num->kubun_value }}</p>
    <div class="line1">
    </div>
    <div class="line2">

    </div>
</div>
<div class="linex">
    <p>ペット種類：{{ $notes }}</p>
    <div class="line1">
    </div>
    <div class="line2">

    </div>
</div>