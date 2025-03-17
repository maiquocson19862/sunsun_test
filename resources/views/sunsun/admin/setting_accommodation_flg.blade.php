@extends('sunsun.admin.template')
@section('title', '設定')
@section('head')
@parent
    <link rel="stylesheet" href="{{asset('sunsun/admin/css/setting_accommodation_flg.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/lib/bootstrap4-datetimepicker/bootstrap-datetimepicker.min.css')}}">
@endsection
@section('main')
<main>
    <div class="container">
        <div class="breadcrumb-sunsun">
            @include('sunsun.admin.layouts.breadcrumb')
        </div>
        <form action="{{route('admin.update_setting')}}" method="POST" id="update_setting">
            @csrf
            <div class="setting">
                <label>宿泊</label>
                <select name="accommodation_flg" id="accommodation_flg" class="form-control">
                    <option value='0' <?php if($data === "0") echo 'selected'; ?>>不可</option>
                    <option value='1' <?php if($data === "1") echo 'selected'; ?>>可能</option>
                </select>
                <button class="btn btn-block btn-main">保存</button>
            </div>
        </form>
    </div>
</main>
@endsection
@section('footer')
    @parent
@endsection
