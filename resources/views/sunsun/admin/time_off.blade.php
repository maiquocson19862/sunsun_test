@extends('sunsun.admin.template')
@section('title', '予約不可設定')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap-datepicker-master/css/bootstrap-datepicker.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/holiday.css')}}">
    <link rel="stylesheet" href="{{asset('css/on-off-switch.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/time_off.css').config('version_files.html.css') }}">
@endsection
@section('main')
    <main>
        <div class="container">
            <div class="breadcrumb-sunsun">
                @include('sunsun.admin.layouts.breadcrumb')
            </div>
            <div class="main-head">
                <div class="main-head__top" style="display: flex;justify-content: space-between;">
                    <div class="">
                    </div>
                    <div class="">
                        <div class="control-view">
                            <div class="control-align_center button-control">
                                <button class="btn btn-block btn-main control-date" id="go-day">一日表示</button>
                            </div>
                            <div class="control-align_center button-control">
                                <button class="btn btn-block btn-main control-date" id="go-weekly">週間表示</button>
                            </div>
                            <div class="control-align_center button-control">
                                <button class="btn btn-block btn-main control-date" id="go-monthly">月間表示</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="main-content" style="display: flex">
                <div class="main-content-left-date">
                    <div id="show_current_date"></div>
                </div>
                <div class="main-content-result">
                    <form id="formSubmit" style="width:100%" method="post" action="{{ route('admin.submit_time_off') }}">
                        <input type="hidden" name="date_search" id="date_search" value="<?php echo date('Y-m-d'); ?>">
                        @csrf
                        <div class="col-items">
                            <div class="row-title">酵素浴（男性）</div>
                            <div class="row-content" id="div1">
                            </div>
                        </div>
                        <div class="col-items">
                            <div class="row-title">酵素浴（女性）</div>
                            <div class="row-content" id="div4">
                            </div>
                        </div>
                        <div class="col-items">
                            <div class="row-title">ペット酵素浴</div>
                            <div class="row-content" id="div2">
                            </div>
                        </div>
                        <div class="col-items">
                            <div class="row-title">{{ config('booking.whitening.label') }}</div>
                            <div class="row-content" id="div3">
                            </div>
                        </div>
                        <div class="col-items">
                            <div class="row-title">宿泊</div>
                            <div class="row-content" id="div5">
                            </div>
                        </div>
                        <div class="btn-submit">
                            <div id="submitButton">
                                <a href="javascript:void(0)" >保存</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="main-footer">
            </div>
        </div>
    </main>
@endsection
@section('script')
    @parent
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/js/moment.min.js')}}" charset="UTF-8"></script>
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/locales/bootstrap-datepicker.ja.min.js')}}" charset="UTF-8"></script>
    <script type="text/javascript" src="{{asset('sunsun/lib/togglebutton/on-off-switch.js')}}"></script>
    <script type="text/javascript" src="{{asset('sunsun/lib/togglebutton/on-off-switch-onload.js')}}"></script>
    <script type="text/javascript">
        var list_kubun = <?php echo json_encode($list_kubun); ?>;
        var list_holiday = <?php echo json_encode($list_holiday); ?>;
        var count_list = list_kubun.length;
        var count_holiday = list_holiday.length;
        var listCombo = [];
        var first_page = true;
    </script>
    <script src="{{asset('sunsun/admin/js/time_off.js').config('version_files.html.js')}}"></script>
@endsection