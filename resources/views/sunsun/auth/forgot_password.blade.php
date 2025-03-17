@extends('sunsun.front.template')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/lib/checkbox/build.css').config('version_files.html.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}">
    <style>
        .has-error .help-block {
            font-size: 0.85rem;
            color: red;
        }
    </style>
@endsection
@section('page_title', __('auth.forgot_password'))
@section('main')
    <main class="main-body">
        <div class="">
            <div class="user-warp">
                {!! Form::open(['action' => ['Sunsun\Auth\ResetPasswordController@exec'], 'method' => 'POST', 'class' => 'form']) !!}
                @if(isset($status) && $status === true)
                <div class="alert alert-success">
                    {{ isset($notify)?$notify:"" }}
                </div>
                @elseif(isset($status) && $status === false)
                <div class="alert alert-danger">
                    {{ isset($notify)?$notify:"" }}
                </div>
                @endif
                <div class="form-group">
                    <div class="">
                        <p class="text-md-left pt-2">@lang('auth.email')</p>
                    </div>
                    <div class="form-input">
                        <input name="email" type="text" id="email" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="">
                    </div>
                    <div class="form-input">
                        <a class="no-effect">
                            <button type="submit" class="btn btn-block btn-booking text-white confirm-rules">@lang('auth.retrieve_password')</button>
                        </a>
                    </div>
                </div>
                @if(isset($status) && $status === false)
                <div class="form-group text-center pb-0 mb-0 mt-5" style="margin-top: 15px">
                    {!! config('const.message.err_not_user') !!}
                </div>
                <div class="form-group text-center pt-0 mt-0" style="margin-top: 15px">
                    <a href="{{ route('register') }}" class="center-link">ユーザー登録</a>
                </div>
                @endif
                {!! Form::close() !!}
            </div>
        </div>
    </main>
@endsection

@section('footer')
    @parent
@endsection

@section('script')

    @parent
    <script src="{{asset('sunsun/front/js/base.js').config('version_files.html.css')}}"></script>
@endsection
