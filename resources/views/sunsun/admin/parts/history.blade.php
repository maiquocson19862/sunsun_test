<div>
    <div class="title-table-time">
        <span class="font-weight-bold">履歴</span>
    </div>
    <div class="history-bar">
        @php
        $i = 0;
        @endphp
        @foreach($history_booking as $hi)
            <div class="history-block @if($i == 0) head @endif">
                <div>
                    @if($hi->course != '05')
                        <div class="text-center">
                            <span>[{{ $hi->course }}]</span>
                            <span>{{ $hi->gender }}</span>
                            <span>{{ isset($hi->age_value)?'('.$hi->age_value.'歳)':'' }}</span>
                        </div>
                    @endif
                    <div class="text-center">
                        @if(isset($hi->ref_booking_id))
                        <span>{{ $hi->name }}同行者様</span>
                        @else
                        <span>{{ $hi->name }}様</span>
                        @endif
                        <span class="text-red">{{ $hi->repeat_user }}</span>
                    </div>
                    @if(isset($hi->booking_id))
                        <input type="hidden" class="booking-id" value="{{ $hi->booking_id }}">
                    @endif
                    @if(!isset($hi->ref_booking_id))
                        @if(isset($hi->transport))
                        <div class="text-center">
                            <span>{{ $hi->transport }}</span>
                            <span>{{ $hi->bus_arrive_time_slide }}</span>
                            <span class="text-red">{{ $hi->pick_up }}</span>
                        </div>
                        @endif
                    @endif
                    @if(($hi->lunch != NULL)||($hi->whitening != NULL)||($hi->whitening2 != NULL)
                        ||($hi->pet_keeping != NULL)||($hi->stay_room_type != NULL)
                        ||($hi->core_tuning != NULL)||($hi->tea != NULL))
                        <div class="text-center">
                            @if(isset($hi->lunch))
                            <span>{{ $hi->lunch }}</span>
                            @endif
                            @if(isset($hi->tea))
                            <span>{{ $hi->tea }}</span>
                            @endif
                            @if(isset($hi->whitening))
                            <span>{{ $hi->whitening }}</span>
                            @endif
                            @if(isset($hi->whitening2))
                            <span>{{ $hi->whitening2 }}</span>
                            @endif
                            @if(isset($hi->core_tuning))
                            <span>{{ $hi->core_tuning }}</span>
                            @endif
                            @if(isset($hi->pet_keeping))
                            <span>{{ $hi->pet_keeping }}</span>
                            @endif
                            @if(!isset($hi->ref_booking_id))
                                @if(isset($hi->stay_room_type))
                                    <?php
                                        $title_stay = "デラックスルーム";
                                        $type_room = $hi->stay_room_type;
                                        if ($type_room == "03") $title_stay = "ツイン";
                                        elseif ($type_room == "04") $title_stay = "シングル";
                                    ?>
                                    <span>{{ $title_stay }}({{ $hi->stay_guest_num }})</span>
                                    @if(isset($hi->breakfast))
                                        <span>{{ $hi->breakfast }}</span>
                                    @endif
                                @endif
                            @endif
                        </div>
                    @endif
                    @if(!isset($hi->ref_booking_id))
                        @if(isset($hi->phone))
                        <div class="text-center">
                            <span>{{ $hi->phone }}</span>
                        </div>
                        <div class="text-center">
                            <span>支払：{{ $hi->payment_method }}</span>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
            @php
            $i++;
            @endphp
        @endforeach
    </div>
</div>