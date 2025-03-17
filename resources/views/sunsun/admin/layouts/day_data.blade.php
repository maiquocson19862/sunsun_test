@if($row == 'wt')
    @if(is_object($time['data']['wt']))
        @if(isset($time['data'][$row]->booking_id))
            <input type="hidden" class="booking-id" value="{{ $time['data'][$row]->booking_id }}">
            <input type="hidden" class="payment_id" value="{{ isset($payments[$time['data'][$row]->booking_id])?$payments[$time['data'][$row]->booking_id]:"" }}">
        @endif
        <span>[{{ $time['data'][$row]->course }}]</span>
        <span>{{ $time['data'][$row]->gender }}</span>
        <span>{{ isset($time['data'][$row]->age_value)?'('.$time['data'][$row]->age_value.'歳)':'' }}</span>
        <br>
        @if(isset($time['data'][$row]->ref_booking_id))
        <span>{{ $time['data'][$row]->name }}同行者様</span>
        @else
        <span>{{ $time['data'][$row]->name }}様</span>
        @endif
        <span class="text-red">{{ $time['data'][$row]->repeat_user }}</span>
    @endif
@else
    @if(is_object($time['data'][$row]))
        @if((isset($time['data'][$row]->fake_booking_flg)) && ($time['data'][$row]->fake_booking_flg == '1'))
            <div class="control-align_center">
                <div>―</div>
            </div>
        @else
            @if(isset($time['data'][$row]->booking_id))
                <input type="hidden" class="booking-id" value="{{ $time['data'][$row]->booking_id }}">
                <input type="hidden" class="time" value="{{ $time['data'][$row]->time }}">
                <input type="hidden" class="payment_id" value="{{ isset($payments[$time['data'][$row]->booking_id])?$payments[$time['data'][$row]->booking_id]:"" }}">
            @endif
            <span>[{{ $time['data'][$row]->course }}{{  config('const.laber.bed')[$time['data'][$row]->turn] }}]</span>
            <span>{{ $time['data'][$row]->gender }}</span>
            <span>{{ isset($time['data'][$row]->age_value)?'('.$time['data'][$row]->age_value.'歳)':'' }}</span>
            <br>
            @if(isset($time['data'][$row]->ref_booking_id))
            <span>{{ $time['data'][$row]->name }}同行者様</span>
            @else
            <span>{{ $time['data'][$row]->name }}様</span>
            @endif
            <span class="text-red">{{ $time['data'][$row]->repeat_user }}</span>
            @if(!isset($time['data'][$row]->ref_booking_id))
                @if(isset($time['data'][$row]->transport))
                <br>
                <span>{{ $time['data'][$row]->transport }}</span>
                <span>{{ $time['data'][$row]->bus_arrive_time_slide }}</span>
                <span class="text-red">{{ $time['data'][$row]->pick_up }}</span>
                @endif
            @endif
            @if(($time['data'][$row]->lunch != NULL)||($time['data'][$row]->whitening != NULL)||($time['data'][$row]->whitening2 != NULL)
            ||($time['data'][$row]->pet_keeping != NULL)||($time['data'][$row]->stay_room_type != NULL)
            ||($time['data'][$row]->core_tuning != NULL)||($time['data'][$row]->tea != NULL))
                @if(isset($time['data'][$row]->lunch))
                <br><span>{{ $time['data'][$row]->lunch }}</span>
                @endif
                @if(isset($time['data'][$row]->tea))
                <br><span>{{ $time['data'][$row]->tea }}</span>
                @endif
                @if(isset($time['data'][$row]->whitening))
                <br><span>{{ $time['data'][$row]->whitening }}</span>
                    @if(isset($time['data'][$row]->whitening_repeat) && $time['data'][$row]->whitening_repeat == "1")
                        <span class="text-red">{{ config('const.text_simple.repeat2') }}</span>
                    @endif
                @endif
                @if(isset($time['data'][$row]->whitening2))
                <br><span>{{ $time['data'][$row]->whitening2 }}</span>
                    @if(isset($time['data'][$row]->whitening_repeat2) && $time['data'][$row]->whitening_repeat2 == "1")
                        <span class="text-red">{{ config('const.text_simple.repeat2') }}</span>
                    @endif
                @endif
                @if(isset($time['data'][$row]->core_tuning))
                <br><span>{{ $time['data'][$row]->core_tuning }}</span>
                @endif
                @if(isset($time['data'][$row]->pet_keeping))
                <br><span>{{ $time['data'][$row]->pet_keeping }}</span>
                @endif
                @if(!isset($time['data'][$row]->ref_booking_id))
                    @if(isset($time['data'][$row]->stay_room_type))
                        <?php
                            $title_stay = "デラックスルーム";
                            $type_room = $time['data'][$row]->stay_room_type;
                            if ($type_room == "03") $title_stay = "ツイン";
                            elseif ($type_room == "04") $title_stay = "シングル";
                        ?>
                        <br><span>{{ $title_stay }}({{ $time['data'][$row]->stay_guest_num }})</span>
                        @if(isset($time['data'][$row]->breakfast))
                            <span>{{ $time['data'][$row]->breakfast }}</span>
                        @endif
                    @endif
                @endif
            @endif
            @if(!isset($time['data'][$row]->ref_booking_id))
                @if(isset($time['data'][$row]->phone))
                <br>
                <span>{{ $time['data'][$row]->phone }}</span>
                <br>
                <span>支払：{{ $time['data'][$row]->payment_method }}</span>
                @endif
            @endif
        @endif
    @endif
@endif