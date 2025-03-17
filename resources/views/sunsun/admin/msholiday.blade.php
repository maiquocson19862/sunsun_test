@extends('sunsun.admin.template')
@section('title', 'holiday')
@section('head')
    @parent
    <style>
        span.text_span_holiday{
            float: left;
            margin: 0 2%;
            font-size: 13px;
        }
        #holiday_start_date_year,
        #holiday_start_date_month,
        #holiday_start_date_day,
        #holiday_end_date_year,
        #holiday_end_date_month,
        #holiday_end_date_day,
        #holiday_add_date_year,
        #holiday_add_date_month,
        #holiday_add_date_day,
        #holiday_note{
            float: left;
            height: 20px;
            font-size: 13px;
        }
        #holiday_start_date_year,
        #holiday_end_date_year,
        #holiday_add_date_year{
            width: 17%;
        }
        #holiday_start_date_month,
        #holiday_start_date_day,
        #holiday_end_date_month,
        #holiday_end_date_day,
        #holiday_add_date_month,
        #holiday_add_date_day{
            width: 10%;
        }
        .btn_search_user,
        .btn_add_holiday{
            width: 100%;
            float: left;
        }
        .btn_search_user>a,
        .btn_add_holiday>a{
            width: 15%;
            display: inline-block;
            text-align: center;
            background-color: #d7751e;
            border-radius: .25rem;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            padding: .5% 0;
        }
        .result_data_holiday tr>th{
            text-align: center;
            font-size:13px;
        }
        .editbutton{
            width: 100%;
            background-color: #d7751e;
            color: #fff;
            text-align: center;
            padding: 1%;
            border-radius: .25rem;
            cursor: pointer;
            font-size: 13px;
        }
        nav.pagination{
            width: 100%;
        }
        nav.pagination ul{
            width: 100%;
            float: left;
        }
        nav.pagination ul>li{
            width: 30px;
            float: left;
            margin-left: 10px;
        }
        nav.pagination ul>li:nth-child(1){
            margin-left: 0px;
        }
        nav.pagination ul>li>a{
            text-align: center;
            padding: 1%;
            display: block;
            background-color: #bbbf7a;
            text-decoration: none;
            font-size: 14px;
            color: #fff;
        }
        nav.pagination ul>li>a.is-current{
            border:none !important;
            background-color: #fff;
            font-weight: bold;
        }
        .pagination-list>a.pagination-previous,
        .pagination-list>a.pagination-next{
            float: left;
            text-decoration: none;
            width: 30px;
            text-align: center;
            background-color: #bbbf7a;
            color: #fff;
        }
    </style>
@endsection
@section('main')
    <main>
        <div class="container">
            <div class="breadcrumb-sunsun">
                @include('sunsun.admin.layouts.breadcrumb')
            </div>
            <div class="main-head">
                <div class="control_form" style="display: flex; padding:1%; border:1px solid #dee2e6">
                    <form id="searchform" style="width: 100%">
                        <div class="form-group" style="width: 25%; float: left">
                            <div class="date_start_holiday">
                                <span style="font-size: 13px">適用開始日</span>
                            </div>
                            <input type="text" class="input_text_form" name="holiday_start_date_year" id="holiday_start_date_year" maxlength="4"><span class="text_span_holiday">年</span>
                            <input type="text" class="input_text_form" name="holiday_start_date_month" id="holiday_start_date_month" maxlength="2"><span class="text_span_holiday">月</span>
                            <input type="text" class="input_text_form" name="holiday_start_date_day" id="holiday_start_date_day" maxlength="2"><span class="text_span_holiday">日</span>
                        </div>
                        <div class="form-group" style="width: 25%; float: left">
                            <div class="date_end_holiday">
                                <span style="font-size: 13px">適用終了日</span>
                            </div>
                            <input type="text" class="input_text_form" name="holiday_end_date_year" id="holiday_end_date_year" maxlength="4"><span class="text_span_holiday">年</span>
                            <input type="text" class="input_text_form" name="holiday_end_date_month" id="holiday_end_date_month" maxlength="2"><span class="text_span_holiday">月</span>
                            <input type="text" class="input_text_form" name="holiday_end_date_day" id="holiday_end_date_day" maxlength="2"><span class="text_span_holiday">日</span>
                        </div>
                        <div class="btn_search_user" id="searchSubmit">
                            <a href="javascript:void(0)">検索</a>
                        </div>
                    </form>
                </div>
                <!-- add form -->
                <div class="control_form" style="display: flex; padding:1%; margin:2% 0; border:1px solid #dee2e6">
                    <form id="addform" style="width: 100%" method="POST" action="{{route('admin.addmsholiday')}}">
                        @csrf
                        <div class="form-group" style="width: 25%; float: left">
                            <div class="date_add_holiday">
                                <span style="font-size: 13px">適用開始日</span>
                            </div>
                            <input type="text" class="input_text_form" name="holiday_add_date_year" id="holiday_add_date_year" maxlength="4" value="{{ old('holiday_add_date_year') }}"><span class="text_span_holiday">年</span>
                            <input type="text" class="input_text_form" name="holiday_add_date_month" id="holiday_add_date_month" maxlength="2" value="{{ old('holiday_add_date_month') }}"><span class="text_span_holiday">月</span>
                            <input type="text" class="input_text_form" name="holiday_add_date_day" id="holiday_add_date_day" maxlength="2" value="{{ old('holiday_add_date_day') }}"><span class="text_span_holiday">日</span>
                        </div>
                        <div class="form-group" style="width: 25%; float: left">
                            <div class="date_add_note_holiday">
                                <span style="font-size: 13px">注意</span>
                            </div>
                            <input type="text" class="input_text_form" name="holiday_note" id="holiday_note" value="{{ old('holiday_note') }}">
                        </div>
                        <div class="btn_add_holiday">
                            <a href="javascript:void(0)" id="addSubmit">追加</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="main-content">
                <div class="resulttable">
                    @include('sunsun.admin.layouts.holiday_data')
                </div>
            </div>
            <div class="main-footer">
            </div>
        </div>
    </main>
@endsection
@section('script')
    @parent
    <script type="text/javascript">
        var submit = false;
        var open = false;
        (function($) {
            $(document).ready(function(){
                var errormsg = '';
                var errors = "<?php echo (session("error_holiday") == null) ? "null" : session("error_holiday") ?>";
                if(errors !== 'null'){
                    errormsg = errors;
                    $(".date_add_holiday>span").css("color","red");   
                    alert(errormsg);
                }
                
                $(document).on("click", "#addSubmit", function(){
                    if(!submit){
                        if (!checkValidAdd()) {
                            var cf = confirm('Are you sure you want to add it?') ;
                            if (cf == true) {
                                $('#addform').submit();
                                submit = true;
                            }
                            
                        }
                    }
                });

                // search
                $("#searchSubmit").on("click", function(){
                    if (!submit) {
                        searchSubmit();
                    }
                });

                // pagination enter
                $('#currentPage').keypress(function(e){
                    if(e.which == 13){
                        var url=url_paginate.value;
                        var page=currentPage.value;
                        var url_active=url+'?page='+page;
                        window.location.href = url_active;
                    }
                });

                // pagination ajax
                $(document).on('click','.pagination_ajax a',function(e){
                    e.preventDefault()
                    var data = $('form#searchform').serializeArray();
                    var page=$(this).attr('href').split('page=')[1];
                    get_data_ajax_paginate(page,data);
                })

            });

            // get data ajax when click paginate search ajax
            function get_data_ajax_paginate(page,data)
            {
                $.ajax({
                    url: $site_url + '/admin/holiday_search_paginate?page='+page,
                    type: 'GET',
                    data: data,
                    dataType: 'JSON',
                    beforeSend: function beforeSend() {
                        loader.css({
                            'display': 'block'
                        });
                    },
                    success: function success(html) {
                        if (html.status == true) {
                            $('.resulttable').html(html.data);
                        }
                    },
                    complete: function complete() {
                            loader.css({
                            'display': 'none'
                        });
                    }
                });
            }

            // check validate date in input form
            function checkValidAdd(){
               var error = false;
               var errorMessage = 'Are you sure you want to add it?';
               $(".date_add_holiday>span").css("color","black");
                if( $("#holiday_add_date_year").val() && $("#holiday_add_date_month").val() && $("#holiday_add_date_day").val() ){
                    if(!isDate($("#holiday_add_date_day").val() +'/'+ $("#holiday_add_date_month").val() +'/'+ $("#holiday_add_date_year").val())){
                        error = true;
                        $(".date_add_holiday>span").css("color","red");
                    }         
                } else {
                   error = true;
                   $(".date_add_holiday>span").css("color","red");
                   $(".date_add_note_holiday>span").css("color","red");
                }
                if(!$("#holiday_note").val()){
                    error = true;
                    $(".date_add_note_holiday>span").css("color","red");    
                } 
                if(error){
                    alert(errorMessage);
                }
                return error;
            }

        })(jQuery);

        // edit
        function editSubmit(id) {
            if(!submit){
                if(open){
                    alert('Complete the current line.');
                }else{
                    open = true;
                    document.getElementById("holiday_"+id+"_date").style.display = 'none';
                    document.getElementById("holidays_"+id+"_date").style.display = 'inline';
                    document.getElementById("holiday_"+id+"_note").style.display = 'none';
                    document.getElementById("holiday_"+id+"_notes").style.display = 'inline';
                    document.getElementById("holiday_"+id+"_delete").disabled = '';
                    document.getElementById("update_"+id).style.display = 'inline';
                    document.getElementById("cancel_"+id).style.display = 'inline';
                    document.getElementById(id).style.display = 'none';
                }
            }
        }

        // cancel
        function cancelSubmit(id) {
            if (!submit) {
                open = false;
                id = id.replace("cancel_","");
                document.getElementById("holiday_"+id+"_date").style.display = 'inline';
                document.getElementById("holidays_"+id+"_date").style.display = 'none';
                document.getElementById("holiday_"+id+"_note").style.display = 'inline';
                document.getElementById("holiday_"+id+"_notes").style.display = 'none';
                document.getElementById("holiday_"+id+"_delete").disabled = 'disabled';
                document.getElementById("update_"+id).style.display = 'none';
                document.getElementById("cancel_"+id).style.display = 'none';
                document.getElementById(id).style.display = 'inline';
            }
        }

        // update
        function updateSubmit(id) {
            var error = false;
            if (!submit) {
                id = id.replace("update_","");
                var holiday_date = document.getElementById("holidays_"+id+"_date").value;
                var holiday_note = document.getElementById("holiday_"+id+"_notes").value;
                var holiday_date_old = $("span#holiday_"+id+"_date").text();
                var checkdelete = document.getElementById("holiday_"+id+"_delete").checked;
                var date = holiday_date.split('/');
                var year = date[0];
                var month = date[1];
                var day = date[2];
                if(holiday_date){
                    var cf = confirm('Are you sure you want to update?');
                    if(cf == true){
                        var check_date = day + "/" + month + "/" + year;
                        if(!isDate(check_date)){
                            error = true;
                            alert('Date is not formatted correctly?');
                        };
                        if(!error){
                            var data = { holiday_id: id, checkdelete : checkdelete, date_holiday: holiday_date, holiday_note: holiday_note, holiday_date_old: holiday_date_old};
                            $.ajax({
                                url: $site_url + '/admin/update_holiday',
                                type: 'POST',
                                data: data,
                                dataType: 'JSON',
                                beforeSend: function beforeSend() {
                                    loader.css({
                                        'display': 'block'
                                    });
                                },
                                success: function success(html) {
                                    if (html.status == true) {
                                        window.location.reload();
                                        submit = true;
                                    }else{
                                        alert('Date exits?');
                                    }
                                },
                                complete: function complete() {
                                        loader.css({
                                        'display': 'none'
                                    });
                                }
                            });
                        }else{
                            alert(errorMessage);
                        }
                    }
                }
            }
        }

        // search
        function searchSubmit() {
            var error = false;
            var holiday_start_date_ = "#holiday_start_date_";
            var holiday_end_date_ = "#holiday_end_date_";
            if( $(holiday_start_date_+"year").val() && $(holiday_start_date_+"month").val() && $(holiday_start_date_+"day").val() && $(holiday_end_date_+"year").val() && $(holiday_end_date_+"month").val() && $(holiday_end_date_+"day").val()){
                if(!isDate($(holiday_start_date_+"day").val() +'/'+ $(holiday_start_date_+"month").val() +'/'+ $(holiday_start_date_+"year").val())){
                    error = true;
                    $(".date_start_holiday>span").css("color","red");
                    $(".date_end_holiday>span").css("color","black");
                }
                if(!isDate($(holiday_end_date_+"day").val() +'/'+ $(holiday_end_date_+"month").val() +'/'+ $(holiday_end_date_+"year").val())){
                    error = true;
                    $(".date_start_holiday>span").css("color","black");
                    $(".date_end_holiday>span").css("color","red");
                }         
            }else{
                error = true;
                $(".date_start_holiday>span").css("color","red");
                $(".date_end_holiday>span").css("color","red");
            }

            if (!error) {
                var data = $('form#searchform').serializeArray();
                $.ajax({
                    url: $site_url + '/admin/ms_holiday',
                    type: 'GET',
                    data: data,
                    dataType: 'JSON',
                    beforeSend: function beforeSend() {
                        loader.css({
                            'display': 'block'
                        });
                    },
                    success: function success(html) {
                        if (html.status == true) {
                            $('.resulttable').html(html.data);
                        }
                    },
                    complete: function complete() {
                            loader.css({
                            'display': 'none'
                        });
                    }
                });
                submit = false;
            }
        }

        function isDate(value) {
            try {
                //Change the below values to determine which format of date you wish to check. It is set to dd/mm/yyyy by default.
                var DayIndex = 0;
                var MonthIndex = 1;
                var YearIndex = 2;
                    
                value = value.replace(/-/g, "/").replace(/\./g, "/"); 
                var SplitValue = value.split("/");
                var OK = true;
                if (!(SplitValue[DayIndex].length == 1 || SplitValue[DayIndex].length == 2)) {
                    OK = false;
                }
                if (OK && !(SplitValue[MonthIndex].length == 1 || SplitValue[MonthIndex].length == 2)) {
                    OK = false;
                }
                if (OK && SplitValue[YearIndex].length != 4) {
                    OK = false;
                }
                
                if (OK) {
                    var Day = parseInt(SplitValue[DayIndex], 10);
                    var Month = parseInt(SplitValue[MonthIndex], 10);
                    var Year = parseInt(SplitValue[YearIndex], 10);
                    
                    //if (OK = ((Year > 1900) && (Year < new Date().getFullYear()))) {
                        if (OK = (Month <= 12 && Month > 0)) {
                            var LeapYear = (((Year % 4) == 0) && ((Year % 100) != 0) || ((Year % 400) == 0));
        
                            if (Month == 2) {
                                OK = LeapYear ? (Day > 0 && Day <= 29) : (Day > 0 && Day <= 28);
                            }
                            else {
                                if ((Month == 4) || (Month == 6) || (Month == 9) || (Month == 11)) {
                                    OK = (Day > 0 && Day <= 30);
                                }
                                else {
                                    OK = (Day > 0 && Day <= 31);
                                }
                            }
                        }
                    //}
                }
                return OK;
            }
            catch (e) {
                return false;
            }
        }
    </script>
@endsection
