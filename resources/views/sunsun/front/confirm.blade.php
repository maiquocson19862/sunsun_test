@extends('sunsun.front.template')

@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}">
@endsection
@section('page_title', '予約確認')
@section('main')
<?php $display = ""; ?>
    <main class="main-body confirm">
        <div class="">
            <form action="{{route('.payment')}}" method="POST" class="booking">
                @csrf
                <div class="booking-warp confirm">
                    <!-- <div class="header-confirm">

                    </div> -->
                    <div class="body-confirm">
                        @if(isset($customer))
                        <div>
                            @php
                            $i = 0;
                            @endphp

                            @foreach($customer['info'] as $key => $data)

                                @if((!isset($data['fake_booking'])) || ($data['fake_booking'] != '1'))
                                    @if($key > 0)
                                        <hr class="line-line">
                                        <span style="display: none">mark_realline</span>
                                    @endif
                                    @php
                                        $course = json_decode($data['course']);
                                        if (($course->kubun_id == "08" || $course->kubun_id == "09") && $i == 0) $display = "style='display:none'";
                                        $repeat_user = json_decode($data['repeat_user']);
                                        $i++;
                                    @endphp
                                    @include('sunsun.front.sub.confirm_temp')
                                @endif
                            @endforeach
                        </div>
                        @elseif(isset($admin_customer))
                        <div>
                            @php
                            $i = 0;
                            @endphp

                            @foreach($admin_customer as $key => $data)

                                @if((!isset($data->fake_booking_flg)) || ($data->fake_booking_flg != '1'))
                                    @if($key > 0)
                                        <hr class="line-line">
                                        <span style="display: none">mark_realline</span>
                                    @endif
                                    @php
                                        $course = $data->course;
                                        if (($course->kubun_id == "08" || $course->kubun_id == "09") && $i == 0) $display = "style='display:none'";
                                        $repeat_user = $data->repeat_user;
                                        $i++;
                                    @endphp
                                    @include('sunsun.front.sub.confirm_temp')
                                @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                <div class="foot-confirm">
                    <div class="confirm-button">
                        <div class="button-left-mix">
                            <button id="btn-back" type="button" class="btn btn-block text-white btn-back">戻る</button>
                        </div>
                        <div class="button-center" {!! $display !!}>
                            <button type="button" class="btn btn-block btn-booking text-white add-new-people btn-confirm-left">予約追加
                            </button>
                        </div>
                        <div class="button-right">
                            <button type="submit" class="btn btn-block btn-booking text-white">お支払い入力へ</button>
                        </div>
                    </div>
                </div>
            </form>
            <form id="back_2_booking" action="{{route('.back_2_booking')}}" method="POST" style="display : none;">
                @csrf
                <input type="submit" value=""/>
            </form>
        </div>
    </main>
@endsection

@section('script')
    @parent
    <script src="{{asset('sunsun/front/js/add_user_booking.js').config('version_files.html.css')}}"></script>
    <script src="{{asset('sunsun/front/js/base.js').config('version_files.html.css')}}"></script>
    <script src="{{asset('sunsun/front/js/confirm.js').config('version_files.html.css')}}"></script>
@endsection