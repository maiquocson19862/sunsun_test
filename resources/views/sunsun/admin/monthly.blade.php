@extends('sunsun.admin.template')
@section('title', '予約管理サイト（月間表示）')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap-datepicker-master/css/bootstrap-datepicker.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/monthly.css')}}">
@endsection

@section('main')
    <main>
        <div class="container">
            <div class="breadcrumb-sunsun">
                @include('sunsun.admin.layouts.breadcrumb')
            </div>
            <div class="main-head">
                <div class="left-content">
                    <div class="control-view">
                        <div class="control-align_center">
                            <button class="btn btn-block btn-main control-month control-date-left prev-month" href="javascript:void(0)">〈 前月</button>
                        </div>
                        <div class="control-align_center monthly-width__value">
                            <span class="">
                                <input type="text" readonly="readonly"  id="input-current__monthly" class="bg-white  input-date__value" value="{{ $year.'/'.$month }}">
                            </span>
                        </div>
                        <div class="control-align_center">
                            <span class=""   id="button-current__monthly">
                                <i data-time-icon="icon-time" data-date-icon="icon-calendar" class="fa fa-calendar-alt icon-calendar"></i>
                            </span>
                        </div>
                        <div class="control-align_center">
                            <button class="btn btn-block btn-main control-month control-date-right next-month" href="javascript:void(0)">翌月 〉</button>
                        </div>
                    </div>
                </div>
                <div class="right-content">
                    <div>
                        <ul>
                            <li class="text-right">●：全床空き</li>
                            <li class="text-right">▲：空き有</li>
                            <li class="text-right">×：満床</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <div class="main-content__table">
                    <div class="table-header header">
                        <div class="table-col not-select"></div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">月</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">火</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">水</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">木</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">金</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select">
                            <div>
                                <div class="font-bold">土</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-col not-select last-col">
                            <div>
                                <div class="font-bold">日</div>
                                <div class="data-item">
                                    <div class="item head">
                                        <span class="text-blue">男</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-red">女</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-wt">WT</span>
                                    </div>
                                    <div class="item head">
                                        <span class="text-yellow">Pet</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    @foreach($data['week_range'] as $week)
                    <div class="table-header">
                        <div class="table-col not-select">
                            <div class="data-item-head noselect">&#160;</div>
                            <div class="data-item">
                                <div class="item">09:30‐13:30</div>
                            </div>
                            <div class="data-item">
                                <div class="item">13:30‐18:00</div>
                            </div>
                            <div class="data-item">
                                <div class="item">18:00‐20:30</div>
                            </div>
                        </div>
                        @foreach($week as $key => $day)
                        <div class="table-col
                            @php
                                if($key == (count($week) - 1)){
                                    echo ' last-col ';
                                }
                                if($day['full_date'] == NULL){
                                    echo ' not-select ';
                                }
                            @endphp
                            ">
                            @if($day['full_date'] == NULL)
                            <div class="data-item-head noselect">&#160;</div>
                            <div class="data-item">
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                            </div>
                            <div class="data-item">
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                            </div>
                            <div class="data-item">
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                                <div class="item noselect">&#160;</div>
                            </div>
                            @else
                                <?php
                                    $disable_all = \Helper::getDisableAll($day['full_date']);
                                ?>
                                <div class="data-item-head {{ $day['full_date'] }}">
                                    <div class="font-bold">{{ $day['day'] }}</div>
                                    <input type="hidden" class="full_date" value="{{ $day['full_date'] }}" />
                                </div>
                                @if(($key == 2) || ($key == 3) || !empty($disable_all))
                                <div class="data-item">
                                    <div class="item">
                                        <span class="text-blue">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">―</span>
                                    </div>
                                </div>
                                <div class="data-item">
                                    <div class="item">
                                        <span class="text-blue">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">―</span>
                                    </div>
                                </div>
                                <div class="data-item">
                                    <div class="item">
                                        <span class="text-blue">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">―</span>
                                    </div>
                                </div>
                                @else
                                <div class="data-item {{ $day['full_date'] }}">
                                    <div class="item">
                                        <span class="text-blue">@include('sunsun.admin.layouts.monthly_data', ['type' => 'male', 'time' => '0'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">@include('sunsun.admin.layouts.monthly_data', ['type' => 'female', 'time' => '0'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">@include('sunsun.admin.layouts.monthly_data', ['type' => 'wt', 'time' => '0'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">@include('sunsun.admin.layouts.monthly_data', ['type' => 'pet', 'time' => '0'])</span>
                                    </div>
                                </div>
                                <div class="data-item {{ $day['full_date'] }}">
                                    <div class="item">
                                        <span class="text-blue">@include('sunsun.admin.layouts.monthly_data', ['type' => 'male', 'time' => '1'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">@include('sunsun.admin.layouts.monthly_data', ['type' => 'female', 'time' => '1'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">@include('sunsun.admin.layouts.monthly_data', ['type' => 'wt', 'time' => '1'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">@include('sunsun.admin.layouts.monthly_data', ['type' => 'pet', 'time' => '1'])</span>
                                    </div>
                                </div>
                                <div class="data-item {{ $day['full_date'] }}">
                                    <div class="item">
                                        <span class="text-blue">@include('sunsun.admin.layouts.monthly_data', ['type' => 'male', 'time' => '2'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-red">@include('sunsun.admin.layouts.monthly_data', ['type' => 'female', 'time' => '2'])</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-wt">―</span>
                                    </div>
                                    <div class="item">
                                        <span class="text-yellow">―</span>
                                    </div>
                                </div>
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endforeach
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
    <script src="{{asset('sunsun/lib/bootstrap-datepicker-master/locales/bootstrap-datepicker.ja.min.js')}}"
            charset="UTF-8"></script>
    <script src="{{asset('sunsun/admin/js/month.js').config('version_files.html.js')}}"></script>
@endsection

