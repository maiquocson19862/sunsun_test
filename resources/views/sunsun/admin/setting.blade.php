@extends('sunsun.admin.template')
@section('title', 'Setting')
@section('head')
@parent
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/setting.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap4-datetimepicker/bootstrap-datetimepicker.min.css')}}">

@endsection
@section('main')

<main>
    <div class="container">
        <div class="breadcrumb-sunsun">
            @include('sunsun.admin.layouts.breadcrumb')
        </div>
        <div class="setting">
            <div class="setting-left">
                <select name="setting-type" id="setting-type" class="form-control">
                    @foreach($data as $key => $value)
                    <option value='{{$key}}'>{{$value}}</option>
                    @endforeach
                </select>
                <div class="setting-head" id="setting-head">001 | ご利用</div>
            </div>
            <div class="setting-right">

            </div>
        </div>
    </div>
</main>
@endsection
@section('footer')
    @parent
    <!-- The Modal -->
    <div class="modal" id="setting_update">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal body -->
                <div class="modal-body">

                </div>

                <!-- Modal footer -->
                <div class="modal-footer" style="padding: 10px;">
                    <button type="button" class="btn btn-modal-left text-white color-primary btn-save" id="js-save-time" style="padding: 0.375rem 2rem;">
                    保存
                    </button>
                    <button type="button" class="btn btn-outline-dark  btn-cancel" style="padding: 0.375rem 1rem;">
                    閉じる
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')
    @parent
    <script src="{{asset('sunsun/admin/js/setting.js').config('version_files.html.js')}}"></script>
@endsection
