@php
    if(isset($pop_data) === false){
        $pop_data = [];
    }
@endphp
<div class="">
    <form  @if(isset($data_booking)) action="{{route('admin.update_booking')}}" @else action="{{route('.confirm')}}" @endif method="POST" class="booking">
        <div class="">
            @csrf
            <div class="booking-warp booking" style="background-image: url('{{ asset('sunsun/imgs/bg.png') }}');">
                @if (isset($customer['date-view']))
                <input type="hidden" name="date-value-new" id="date-value-new" value="{{ $customer['date-value'] }}">
                @endif
                @if(isset($add_new_user) && $add_new_user == 'on')
                    <div class="data-field-day">
                        <span>
                                @if (isset($customer['date-view']))
                                    {{$customer['date-view']}}
                                @else
                                    {{$customer['date-view-from']}} <br>
                                    {{$customer['date-view-to']}}
                                @endif
                        </span>  &nbsp;  &nbsp;
                        <span>
                        予約追加
                        </span>
                    </div>
                @endif

                @if(isset($booking_id))
                    <input type="hidden" name="booking_id" id="booking_id" value="{{ $booking_id }}">
                @endif

                <!-- <div class="booking-line font-weight-bold">
                    <div class="booking-line-laber">
                    </div>
                    <hr class="booking-line-line">
                </div> -->

                <div class="booking-block-top">
                    @if(isset($new) && (!$new))
                        @include('sunsun.front.parts.payment_form', ['new' => '0', 'data_booking' => $data_booking])
                        @include('sunsun.front.parts.payment_method', ['new' => '0', 'data_booking' => $data_booking])
                    @else
                        @include('sunsun.front.parts.booking_modal')
                    @endif

                    <!-- <img class=" btn-collapse btn-collapse-top" id="btn-collapse-top"  data-toggle="collapse" data-target=".collapse-top" src="{{ asset('sunsun/svg/hide.svg') }}" alt="Plus" /> -->
                    <div class="collapse collapse-top show" id="">
                        <div class="booking-field">
                            <div class="booking-field-label booking-laber-padding">
                                <p class="text-left pt-2">{{config('booking.repeat_user.label')}}</p>
                            </div>
                            <div class="booking-field-content">
                                <select name="repeat_user" id="repeat_user" class="form-control">
                                    @foreach($repeat_user as $value)
                                        @if(isset($data_booking->repeat_user) && ($value->kubun_id == $data_booking->repeat_user))
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @elseif(isset($pop_data['repeat_user']) && json_decode($pop_data['repeat_user'], true)['kubun_id'] ==  $value->kubun_id)
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @else
                                            <option value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(isset($data_booking->ref_booking_id) === false)
                            <div class="booking-field {{(isset($add_new_user) && $add_new_user == 'on')?'hidden':''}}">
                                <div class="booking-field-label booking-laber-padding">
                                    <p class="text-left pt-2">{{config('booking.transport.label')}}</p>
                                </div>
                                <div class="booking-field-content">
                                    <select name="transport" id='transport' class="form-control">
                                        @foreach($transport as $value)
                                            @if(isset($data_booking->transport) && ($value->kubun_id == $data_booking->transport))
                                                <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                            @elseif(isset($pop_data['transport']) && json_decode($pop_data['transport'], true)['kubun_id'] ==  $value->kubun_id)
                                                <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                            @else
                                                <option value='@json($value)'>{{ $value->kubun_value }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            @foreach($transport as $value)
                                @if(isset($data_booking->transport) && ($value->kubun_id == $data_booking->transport))
                                    <input type="hidden" name="transport" value='@json($value)' />
                                @endif
                            @endforeach
                        @endif
                        <div class="booking-field bus" @if((isset($data_booking->transport) && ($data_booking->transport == '02')) || (isset($pop_data['transport']) && (json_decode($pop_data['transport'], true)['kubun_id'] == '02'))) style="display:flex;" @else style="display:none;"   @endif>
                            <div class="booking-field-label booking-laber-padding">
                                <p class="text-left pt-2">{{config('booking.bus_arrive_time_slide.label')}}</p>
                            </div>
                            <div class="booking-field-content">

                                <div class="dropdown">
                                    <button class="btn btn-border dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <div class="bus-time">
                                            <div class="bus-time-data">
                                                @php
                                                $first_bus_time = NULL;
                                                if(isset($data_booking->bus_arrive_time_slide)){
                                                    foreach ($bus_arrive_time_slide as  $bus_time) {
                                                        if($bus_time->kubun_id == $data_booking->bus_arrive_time_slide){
                                                            $first_bus_time = $bus_time;
                                                            break;
                                                        }
                                                    }
                                                }elseif(isset($pop_data['bus_arrive_time_slide'])){
                                                    foreach ($bus_arrive_time_slide as  $bus_time) {
                                                        if($bus_time->kubun_id == json_decode($pop_data['bus_arrive_time_slide'], true)['kubun_id']){
                                                            $first_bus_time = $bus_time;
                                                            break;
                                                        }
                                                    }
                                                }else{
                                                    foreach ($bus_arrive_time_slide as $first_bus_time) {
                                                        if ($first_bus_time->kubun_id == '01') continue;
                                                        break;
                                                    }
                                                }
                                                @endphp
                                                <input type="hidden" name="bus_arrive_time_slide" id="bus_arrive_time_slide" value='@json($first_bus_time)'>
                                                <div class="text-left" id="bus_time_first">{{ substr($first_bus_time->kubun_value, 0, 8) }}</div>
                                                <div class="text-left node-text" id="bus_time_second">{{ trim(str_replace(substr($first_bus_time->kubun_value, 0, 8), '', $first_bus_time->kubun_value)) }}</div>
                                            </div>
                                            <div class="bus-time-icon">
                                                <i class="fas fa-long-arrow-alt-down"></i>
                                            </div>
                                        </div>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @php
                                        $i = false;
                                        @endphp
                                        @foreach($bus_arrive_time_slide as $value)
                                            @php
                                                $check_dis = "";
                                                if ($value->kubun_id ==  '03'
                                                || $value->kubun_id == '06'
                                                || $value->kubun_id == '09'
                                                || $value->kubun_id == '13'
                                                || $value->kubun_id == '18'
                                                || $value->kubun_id == '01') {
                                                    $check_dis = "dis_time_bus";
                                                }
                                                if(!empty($check_dis)) {
                                                    if (!isset($data_booking) || $data_booking == null)
                                                        continue;
                                                }
                                            @endphp
                                            @if(isset($data_booking->bus_arrive_time_slide))
                                                @if($value->kubun_id == $data_booking->bus_arrive_time_slide)
                                                    <li class="dropdown-item @if($i) {{ 'body-content' }} @endif active {{ $check_dis }}">
                                                        <input type="hidden" class="bus_arrive_time_slide" value='@json($value)'>
                                                        <div class="bus_time_first">{{ substr($value->kubun_value, 0, 8) }}</div>
                                                        <div class="bus_time_second node-text">{{ trim(str_replace(substr($value->kubun_value, 0, 8), '', $value->kubun_value)) }}</div>
                                                    </li>
                                                @else
                                                    <li class="dropdown-item @if($i) body-content @endif {{ $check_dis }}">
                                                        <input type="hidden" class="bus_arrive_time_slide" value='@json($value)'>
                                                        <div class="bus_time_first">{{ substr($value->kubun_value, 0, 8) }}</div>
                                                        <div class="bus_time_second node-text">{{ trim(str_replace(substr($value->kubun_value, 0, 8), '', $value->kubun_value)) }}</div>
                                                    </li>
                                                @endif
                                            @elseif(isset($pop_data['bus_arrive_time_slide']))
                                                @if($value->kubun_id == json_decode($pop_data['bus_arrive_time_slide'], true)['kubun_id'])
                                                    <li class="dropdown-item @if($i) {{ 'body-content' }} @endif active {{ $check_dis }}">
                                                        <input type="hidden" class="bus_arrive_time_slide" value='@json($value)'>
                                                        <div class="bus_time_first">{{ substr($value->kubun_value, 0, 8) }}</div>
                                                        <div class="bus_time_second node-text">{{ trim(str_replace(substr($value->kubun_value, 0, 8), '', $value->kubun_value)) }}</div>
                                                    </li>
                                                @else
                                                    <li class="dropdown-item @if($i) body-content @endif {{ $check_dis }}">
                                                        <input type="hidden" class="bus_arrive_time_slide" value='@json($value)'>
                                                        <div class="bus_time_first">{{ substr($value->kubun_value, 0, 8) }}</div>
                                                        <div class="bus_time_second node-text">{{ trim(str_replace(substr($value->kubun_value, 0, 8), '', $value->kubun_value)) }}</div>
                                                    </li>
                                                @endif
                                            @else
                                                <li class="dropdown-item @if($i) body-content @else active @endif {{ $check_dis }}">
                                                    <input type="hidden" class="bus_arrive_time_slide" value='@json($value)'>
                                                    <div class="bus_time_first">{{ substr($value->kubun_value, 0, 8) }}</div>
                                                    <div class="bus_time_second node-text">{{ trim(str_replace(substr($value->kubun_value, 0, 8), '', $value->kubun_value)) }}</div>
                                                </li>
                                            @endif
                                            @php
                                            $i = true;
                                            @endphp
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="booking-field bus" @if((isset($data_booking->transport) && ($data_booking->transport == '02')) || (isset($pop_data['transport']) && (json_decode($pop_data['transport'], true)['kubun_id'] == '02'))) style="display:flex;" @else style="display:none;"   @endif>
                            <div class="booking-field-label booking-laber-padding">
                                <p class="text-left pt-2">{{config('booking.pick_up.label')}}</p>
                            </div>
                            <div class="booking-field-content">
                                <select name="pick_up" class="form-control">
                                    @foreach($pick_up as $value)
                                        @if(isset($data_booking->pick_up) && ($value->kubun_id == $data_booking->pick_up))
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @elseif(isset($pop_data['pick_up']) && json_decode($pop_data['pick_up'], true)['kubun_id'] ==  $value->kubun_id)
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @else
                                            <option value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <p class="text-left mt-2 node-text">バスの方は洲本ICのバス停に送迎を行います。</p>
                                <p class="text-left mb-2 node-text">※バスのチケット予約ではありません。</p>
                            </div>
                        </div>
                        <div class="booking-field">
                            <div class="booking-field-label booking-laber-padding">
                                <p class="text-left pt-2">{{config('booking.course.label')}}</p>
                            </div>
                            <div class="booking-field-content">
                                <select name="course" id="course" class="form-control">
                                    @foreach($course as $value)
                                        @if(isset($data_booking->course) && ($value->kubun_id == $data_booking->course))
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                        @elseif(isset($pop_data['course']) && json_decode($pop_data['course'], true)['kubun_id'] ==  $value->kubun_id)
                                            <option selected value='@json($value)'>{{ $value->kubun_value }}</option>
                                            <?php // 2020/06/05 ?>
                                        @elseif(isset($data_booking->course) && ($data_booking->course != "04" || $data_booking->course != "06") && ($value->kubun_id == "04" || $value->kubun_id == "06"))
                                            <option disabled>{{ $value->kubun_value }}</option>
                                        @else
                                            @if ($value->kubun_id != "07" || (isset($data_booking->course) && $data_booking->course == "07"))
                                                <option value='@json($value)'>{{ $value->kubun_value }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="ref_booking_id" id="ref_booking_id" value="{{ isset($data_booking->ref_booking_id)?$data_booking->ref_booking_id:'' }}">

                @php
                $picked_course = '';
                if(isset($data_booking->course)){
                    foreach($course as $value){
                        if($value->kubun_id == $data_booking->course){
                            $picked_course = $value;
                            break;
                        }
                    }
                }else if(isset($pop_data['course'])){
                    foreach($course as $value){
                        if($value->kubun_id == json_decode($pop_data['course'], true)['kubun_id']){
                            $picked_course = $value;
                            break;
                        }
                    }
                }else{
                    foreach ($bus_arrive_time_slide as  $picked_course) {
                        break;
                    }
                }
                @endphp
                <input type="hidden" id="pick_course" value='@json($picked_course)'>
                <input type="hidden" id="course_data" value='{{ isset($data_booking)?json_encode($data_booking):"" }}'>
                <input type="hidden" id="course_time" value='{{ isset($data_time)?json_encode($data_time):"[]" }}'>
                <input type="hidden" id="pop_data" value='{{ isset($pop_data)&&!empty($pop_data)?json_encode($pop_data):"" }}'>
                <div class="service-warp">

                </div>


            </div>
            <div class="foot-confirm">
                @if(isset($data_booking))
                        <div class="history-button">
                            <div class="left-button">
                                <div class="index-field-foot">
                                    <div class="content-right container-checkbox">
                                        <label for="confirm">
                                            <input type="checkbox" name="send_email" class="" id="confirm">
                                            <span class="checkmark index"></span>
                                            <span style="line-height: 27px;">確認メールを送信する</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="right-button">
                                @if(isset($history_booking) && (count($history_booking) != 0))
                                    <div class="show_history" style="text-decoration: underline;">変更履歴</div>
                                @endif
                            </div>
                        </div>
                    <div class="confirm-button">
                        @if(isset($booking_id))
                            <div class="button-left">
                                <button type="button" class="btn btn-block text-white btn-delete">削除</button>
                            </div>
                        @endif
                        <div class="button-center">
                            <button type="button" class="btn btn-block text-white btn-cancel btn-cancel-left">キャンセル</button>
                        </div>
                        <div class="button-right">
                            <button type="button" class="btn btn-block text-white btn-update">更新</button>
                        </div>
                    </div>
                @else
                    <div class="confirm-button">
                        @if(isset($add_new_user) && $add_new_user == 'on')
                            <div class="button-left-mix">
                                <button id="btn-back" type="button" class="btn btn-block text-white btn-back">戻る</button>
                            </div>
                        @else
                            <div class="button-left">
                                <button id="btn-home" type="button" class="btn btn-block text-white btn-back">キャンセル</button>
                            </div>
                        @endif
                        <div class="button-center">
                            @if(isset($add_new_user) && $add_new_user == 'on')
                                <input type="hidden" name="add_new_user" value="on">
                            @endif
                            <button type="button" class="btn btn-block text-white btn-booking btn-confirm-left add-new-people">予約追加</button>
                        </div>
                        <div class="button-right">
                            <button type="submit" class="btn btn-block text-white btn-booking">予約確認へ</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </form>
    <form id="back_2_booking" action="{{route('.back_2_booking')}}" method="POST" style="display : none;">
        @csrf
        <input type="submit" value=""/>
    </form>
</div>
<script type="text/javascript">
    var _type_admin = "<?php echo isset($type_admin) ? $type_admin : '' ?>";
    var _sex_admin = "<?php echo isset($sex_admin) ? $sex_admin : '' ?>";
    var _date_admin = "<?php echo isset($date_admin) ? $date_admin : '' ?>";
    var _time_admin = "<?php echo isset($time_admin) ? $time_admin : '' ?>";
    var _bed_admin = "<?php echo isset($bed_admin) ? $bed_admin : '' ?>";
</script>
