@php
    $disable_booking_date = null;
    if(isset($course_data['service_date_start'])){
        $disable_booking_date = substr($course_data['service_date_start'], 0, 4).'/'.substr($course_data['service_date_start'], 4, 2).'/'.substr($course_data['service_date_start'], 6, 2);
    }
    if( (!isset($course_data['course'])) || ($course_data['course'] != '05') ){
        $course_data = NULL;
    }
    if(isset($pop_data)){
        $pop_data = json_decode($pop_data, true);
    }
    if(!isset($pop_data) || (json_decode($pop_data['course'], true)['kubun_id'] != '05')){
        $pop_data = NULL;
    }
@endphp
<div class="booking-block">

    @if(!isset($add_new_user))
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
            <input name="date-view" id="date-view" type="hidden" value="222">
            <input name="date-value" id="date-value" type="hidden" value="{{ isset($pop_data['date-value'])?$pop_data['date-value']:''  }}">
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
            $time = substr($course_data['service_time_1'], 0, 2) . ":" . substr($course_data['service_time_1'], 2, 2) . "～"
            . substr($course_data['service_time_2'], 0, 2) . ":" . substr($course_data['service_time_2'], 2, 2);
        }
        if(isset($pop_data['time_room_time1'])){
            $time = substr($pop_data['time_room_time1'], 0, 2) . ":" . substr($pop_data['time_room_time1'], 2, 2) . "～"
            . substr($pop_data['time_room_time2'], 0, 2) . ":" . substr($pop_data['time_room_time2'], 2, 2);
        }

        $time_json = '';
        $service_time_1 = '0';
        $service_time_2 = '0';

        if(isset($course_data['time_json']) === true && isset($course_data['service_time_1']) === true && isset($course_data['service_time_2']) === true){
            $time_json = $course_data['time_json'];
            $service_time_1 = $course_data['service_time_1'];
            $service_time_2 = $course_data['service_time_2'];
        }else if(isset($pop_data['time'][0]['json']) === true && isset($pop_data['time_room_time1']) === true && isset($pop_data['time_room_time2']) === true){
            $time_json = $pop_data['time'][0]['json'];
            $service_time_1 = $pop_data['time_room_time1'];
            $service_time_2 = $pop_data['time_room_time2'];
        }

    @endphp
    <div class="booking-field">
        <div class="booking-field-label  booking-laber-padding">
            <p class="text-left pt-2">{{config('booking.time.label')}}</p>
        </div>
        <input name="time-view" id="time-view" type="hidden" value="">
        <input name="time-value" id="time-value" type="hidden" value="1230">
        <div class="booking-field-content">
            <div class="timedate-block set-time">

                <input name="time_room_time1" id="time_room_time1" type="hidden" value="{{ $service_time_1 }}">
                <input name="time_room_time2" id="time_room_time2" type="hidden" value="{{ $service_time_2 }}">
                <input name="time_room" type="text" class="form-control time js-set-room_pet bg-white"  id="time_room_pet_0"   readonly="readonly" id="" value="{{ isset($time)?$time:'－' }}">
                <input name="time[0][json]" class="data-json_input" id="time_room_pet_json" type="hidden" value="{{ $time_json }}">
                <input name="time[0][element]" id="" type="hidden" value="time_room_pet_0">
            </div>

        </div>
    </div>
    <div class="booking-field">
        <div class="booking-field-label  booking-laber-padding">
            <p class="text-left pt-2">{{config('booking.number_pet.label')}}</p>
        </div>
        <div class="booking-field-content">
            <select name="service_pet_num" id="number_pet" class="form-control">
                @foreach($service_pet_num as $value)
                    @if(isset($course_data['service_pet_num']) && ($value->kubun_id == $course_data['service_pet_num']))
                        <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                    @elseif(isset($pop_data['service_pet_num']) && ($value->kubun_id == json_decode($pop_data['service_pet_num'], true)['kubun_id']))
                        <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                    @else
                        <option value='@json($value)'>{{ $value->kubun_value }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="booking-field">
        <div class="booking-field-label  booking-laber-padding">
            <p class="text-left pt-2">{{config('booking.pet_type.label')}}</p>
        </div>
        <div class="booking-field-content">
            @php
                $pet_type = '';
                if(isset($course_data['notes'])){
                    $pet_type = $course_data['notes'];
                }else if(isset($pop_data['notes'])){
                    $pet_type = $pop_data['notes'];
                }
            @endphp
            <textarea class="form-control" maxlength="255" name='notes' rows="3">{{ $pet_type }}</textarea>
        </div>
    </div>
    <div class="booking-field">
        <div class="node-text booking-laber-padding">
            <div id="hint-repeat">※バスの場合、到着時間から30分以内の予約は出来ません。希望時間が選択できない場合は、バス到着時間をご確認ください。</div>
        </div>
    </div>
</div>