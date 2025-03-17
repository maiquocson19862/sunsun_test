@extends('sunsun.admin.template')
@section('title', 'ユーザー 管理')
@section('head')
    @parent
    <style>
        .input_text_form{
            width: 50%;
        }
        .form-control:focus{
            border-color:#49505757;
            box-shadow:none;
        }
        .btn_search_user{
            width: 15%;
            background-color: #d7751e;
            text-align: center;
            border-radius: .25rem;
            margin-left: 10%;
            line-height: 33px;
        }
        .btn_search_user>a{
            color: #fff;
            text-decoration: none
        }
        .result_data_user tr th{
            text-align: center;
            font-size: 13px;
        }
        .result_data_user tr td{
            font-size: 13px;
        }
        .editbutton{
            width: 100%;
            background-color: #d7751e;
            color: #fff;
            text-align: center;
            padding: 1%;
            border-radius: .25rem;
            cursor: pointer;
        }
        #csv_download{
            width: 15%;
            background-color: #bbbf7a;
            border-radius: 3px;
            margin: 1% 0;
        }
        #csv_download>a{
            display: block;
            text-align: center;
            font-size: 13px;
            padding: 2%;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
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
        .table.result_data_user td{
            padding: .4rem
        }
        .btn:hover,
        .btn_search_user:hover{
            opacity:0.7;
        }
        .control-align_center a{
            text-decoration: none;
        }
        td.col-password{
            max-width: 100px;
            overflow: hidden;
            /* text-overflow: ellipsis;
            white-space: nowrap; */
            word-wrap:break-word; /*old browsers */
            overflow-wrap:break-word;
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
                <div class="main-head__top" style="display: flex;justify-content: space-between; margin-bottom:10px;">
                    <div class="">
                    </div>
                    <div class="">
                        <div class="control-view">
                            <div class="control-align_center button-control">
                                <a href="{{route('admin.day')}}">
                                    <button class="btn btn-block btn-main control-date" id="go-weekly">１日表示</button>
                                </a>

                            </div>
                            <div class="control-align_center button-control" style="margin: 0 2vw">
                                <a href="{{route('admin.weekly')}}">
                                    <button class="btn btn-block btn-main control-date" id="go-monthly">週間表示</button>
                                </a>
                            </div>
                            <div class="control-align_center button-control">
                                <a href="{{route('admin.monthly')}}">
                                    <button class="btn btn-block btn-main control-date" id="go-user">月間表示</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="control_form" style="display: flex; padding:1%; border:1px solid #dee2e6">
                    <form id="searchform" style="width: 100%">
                        <div class="form-group" style="display: flex">
                            <label for="staticName" style="width: 15%; float: left; font-size:13px" class="col-form-label">名前</label>
                            <input type="text" style="float: left; width: 20%" class="input_text_form" name="username" id="username">
                        </div>
                        <div class="form-group" style="display: flex">
                            <label for="staticPhone" style="width: 15%; float: left; font-size:13px" class="col-form-label">電話番号</label>
                            <input type="text" style="float: left; width: 20%" class="input_text_form numberphone" name="phone" id="phone" maxlength="11">
                            <input style="margin: 10px 0 10px 110px;" checked="checked" type="checkbox" name="notshowdeleted" value="1"><span style="font-size: 13px; line-height: 33px">&nbsp;削除データは表示しない</span>
                        </div>
                        <div class="form-group" style="display: flex">
                            <label for="staticEmail" style="width: 15%; float: left; font-size:13px" class="col-form-label">メールアドレス</label>
                            <input type="text" style="float: left; width: 20%" class="input_text_form" name="email" id="email">
                            <div class="btn_search_user" id="searchSubmit">
                                <a href="javascript:void(0)">検索</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="main-content">
                <div id="csv_download">
                    <a href="/admin/export">CSVダウンロード</a>
                </div>
                <form id="updateform">
                    <div class="resulttable">
                        @include('sunsun.admin.layouts.user_data')
                    </div>
                </form>
            </div>
            <div class="main-footer">
            </div>
        </div>
    </main>
@endsection
@section('script')
    @parent
    <script>
        var submit = false;
        var open = false;
        (function($) {
            $(document).ready(function(){
                $("#searchSubmit").on("click", function(){
                    if (!submit) {
                        searchSubmit();
                    }
                });
                $('#currentPage').keypress(function(e){
                    if(e.which == 13){
                        var url=url_paginate.value;
                        var page=currentPage.value;
                        var url_active=url+'?page='+page;
                        window.location.href = url_active;
                    }
                });
                $(document).on('click','.pagination_ajax a',function(e){
                    e.preventDefault()
                    var data = $('form#searchform').serializeArray();
                    var page=$(this).attr('href').split('page=')[1];
                    get_data_ajax_paginate(page,data);
                })
            });
        })(jQuery);
        function get_data_ajax_paginate(page,data)
        {
            $.ajax({
                url: $site_url + '/admin/search-paginate?page='+page,
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
        // search
        function searchSubmit() {
            var data = $('form#searchform').serializeArray();
            $.ajax({
                url: $site_url + '/admin/msuser',
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
        // edit
        function editSubmit(id) {
            if(!submit){
                if(open){
                    alert('Complete the current line.');
                }else{
                    open = true;
                    document.getElementById("username_"+id).style.display = 'none';
                    document.getElementById("users_"+id).style.display = 'inline';
                    // document.getElementById("password_"+id).style.display = 'none';
                    // document.getElementById("passwords_"+id).style.display = 'inline';
                    document.getElementById("tel_"+id).style.display = 'none';
                    document.getElementById("tels_"+id).style.display = 'inline';
                    document.getElementById("email_"+id).style.display = 'none';
                    document.getElementById("emails_"+id).style.display = 'inline';
                    document.getElementById("users_"+id+"_delete").style.display = 'none';
                    document.getElementById("users_"+id+"_delete_edit").style.display = 'inline';
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
                document.getElementById("username_"+id).style.display = 'inline';
                document.getElementById("users_"+id).style.display = 'none';
                // document.getElementById("password_"+id).style.display = 'inline';
                // document.getElementById("passwords_"+id).style.display = 'none';
                document.getElementById("tel_"+id).style.display = 'inline';
                document.getElementById("tels_"+id).style.display = 'none';
                document.getElementById("email_"+id).style.display = 'inline';
                document.getElementById("emails_"+id).style.display = 'none';
                document.getElementById("users_"+id+"_delete").style.display = 'inline';
                document.getElementById("users_"+id+"_delete_edit").style.display = 'none';
                document.getElementById("update_"+id).style.display = 'none';
                document.getElementById("cancel_"+id).style.display = 'none';
                document.getElementById(id).style.display = 'inline';
            }
        }
        // update
        function updateSubmit(id) {
            if (!submit) {
                id = id.replace("update_","");
                var username = document.getElementById("users_"+id).value;
                // var password = document.getElementById("passwords_"+id).value;
                var tel = document.getElementById("tels_"+id).value;
                var email = document.getElementById("emails_"+id).value;
                var checkdelete = document.getElementById("users_"+id+"_delete_edit").checked;
                if(username){
                    var cf = confirm('Are you sure you want to update?');
                    if(cf == true){
                        var data = { user_id: id, username: username, /*password: password,*/ tel: tel, email: email, checkdelete: checkdelete};
                        $.ajax({
                            url: $site_url + '/admin/update_user',
                            type: 'POST',
                            data: data,
                            dataType: 'JSON',
                            beforeSend: function beforeSend() {
                                loader.css({
                                    'display': 'block'
                                });
                            },
                            success: function success(html) {
                                if (html.status == true && html.type == 'update' && html.data.status == true) {
                                    window.location.reload();
                                    submit = true;
                                }else if((html.status == false && html.type == 'update')){
                                    alert(html.message);
                                    submit = false;
                                }
                            },
                            complete: function complete() {
                                    loader.css({
                                    'display': 'none'
                                });
                            }
                        });
                    }
                } else {
                }
            }
        }
    </script>
@endsection
