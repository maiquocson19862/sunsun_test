@extends('sunsun.admin.template')
@section('title', '予約不可設定')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap-datepicker-master/css/bootstrap-datepicker.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/day_off.css').config('version_files.html.js') }}">
@endsection
@section('main')
    <main>
        <div class="container">
            <div class="breadcrumb-sunsun">
                @include('sunsun.admin.layouts.breadcrumb')
            </div>
            <div class="main-head">
                <div class="control-view">
                    <div class="control-input__date">
                        <input type="text" id="input-current__date"><span>年</span>
                    </div>
                    <div class="control-align_center">
                        <span class="" id="button-current__date">
                            <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="fa fa-calendar-alt icon-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="main-content" style="display: flex">
                <div class="main-content-result">
                    <form id="formSubmitDayOff" method="post" action="{{ route('admin.submit_day_off_acom') }}">
                        @csrf
                        <div class="col-items-date">
                            <div id="date_01"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_02"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_03"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_04"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_05"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_06"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_07"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_08"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_09"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_10"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_11"></div>
                        </div>

                        <div class="col-items-date">
                            <div id="date_12"></div>
                        </div>

                        <div class="btn-submit">
                            <div id="submitButton">
                                <a href="javascript:void(0)">保存</a>
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
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/locales/bootstrap-datepicker.ja.min.js')}}" charset="UTF-8"></script>
    <script type="text/javascript">
        var list_holiday = <?php echo json_encode($list_holiday); ?>;
        var count_holiday = list_holiday.length;
        var url_ajax = $site_url +'/admin/ajax_day_off_acom';
        var url_submit = $site_url +'/admin/submit_day_off_acom';
    </script>
    <script src="{{asset('sunsun/admin/js/day_off.js').config('version_files.html.js')}}"></script>
@endsection
