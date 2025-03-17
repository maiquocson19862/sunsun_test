@extends('sunsun.admin.template')
@section('title', '売上リスト')
@section('admincss')
	<link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap-datepicker-master/css/bootstrap-datepicker.css')}}">
@endsection
@section('head')
	@parent
	<style>
		.container {
			max-width: 1440px;
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
			margin-left: 2vw;
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
    		word-break: break-all;
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
		.font_label {
			font-size:13px;
		}
		.input_date {
			float: left;
			width: 11vw;
		}
		.input_del {
			margin: 5px 0 10px 32.4vw;
		}
		.control_form {
			padding:1rem;
		}
		.icon_date {
			padding: 4px .2vw;
		}
		.txt_r {
			text-align: right;
		}
		.txt_c {
			text-align: center;
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
				<div class="control_form" style="display: flex; padding:1% 1% .5% 1%; border:1px solid #dee2e6">
					<form id="searchform" style="width: 100%">
						<div class="form-group" style="display: flex">
							<label for="staticName" style="width: 3vw; float: left;" class="col-form-label font_label">予約日</label>
							<span class="input_date">
								<input class="bg-white input-date__value" id="input-start__date" name="date_start" readonly="readonly" type="text" style="opacity: 0; width: 1px; position: absolute;" value="{{ $data_search['date_start'] }}" />
								<input class="bg-white input-date__value" id="input-start__view" readonly="readonly" type="text" value="{{ $data_search['date_start'].'('.$data_search['date_start_view'].')' }}" />
							</span>
							<span class="icon_date" id="button-start__date">
								<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="fa fa-calendar-alt icon-calendar"></i>
							</span>
							<label style="width: 2vw; float: left; text-align: center;" class="col-form-label font_label">～</label>
							<span class="input_date">
								<input class="bg-white input-date__value" id="input-end__date" name="date_end" readonly="readonly" type="text" style="opacity: 0; width: 1px; position: absolute;" value="{{ $data_search['date_end'] }}" />
								<input class="bg-white input-date__value" id="input-end__view" readonly="readonly" type="text"
									value="{{ $data_search['date_end'].'('.$data_search['date_end_view'].')' }}" />
							</span>
							<span class="icon_date" id="button-end__date">
								<i data-time-icon="icon-time" data-date-icon="icon-calendar" class="fa fa-calendar-alt icon-calendar"></i>
							</span>
							<div class="btn_search_user" id="searchSubmit">
								<a href="javascript:void(0)">検索</a>
							</div>
						</div>
						<div style="display: flex">
							<?php $set_check = isset($data_search['notshowdeleted']) ? "checked='checked'" : ''; ?>
							<input class="input_del" {{ $set_check }} type="checkbox" name="notshowdeleted" value="1">
							<span class="font_label">&nbsp;削除データは表示しない</span>
						</div>
					</form>
				</div>
			</div>
			<div class="main-content">
				<div id="csv_download">
					<a href="/admin/export_sales_list">CSVダウンロード</a>
				</div>
				<form id="updateform">
					<div class="resulttable">
						@include('sunsun.admin.layouts.sales_list_data')
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
	<script src="{{asset('sunsun/lib/bootstrap-datepicker-master/js/moment.min.js')}}" charset="UTF-8"></script>
	<script src="{{asset('sunsun/lib/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js')}}"></script>
	<script src="{{asset('sunsun/lib/bootstrap-datepicker-master/locales/bootstrap-datepicker.ja.min.js')}}" charset="UTF-8"></script>
	<script src="{{asset('sunsun/admin/js/sales_list.js').config('version_files.html.js')}}"></script>
@endsection
