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
@section('page_title', __('auth.change_password'))
@section('main')
    <main class="main-body">
        <div class="">
            <div class="user-warp">
                @if(isset($forgot) && $forgot === true)
                    {!! Form::open(['action' => ['Sunsun\Auth\ResetPasswordController@update'], 'method' => 'PUT', 'class' => 'form']) !!}
                @else
                    {!! Form::open(['action' => ['Sunsun\Auth\AuthUserController@changepassword'], 'method' => 'PUT', 'class' => 'form']) !!}
                @endif

                @if(isset($success))
                    <div class="alert alert-success">
                        {{ $success }}
                    </div>
                @endif
                @if(isset($status) && $status === true)
                    <div class="alert alert-success">
                        {{ isset($notify)?$notify:"" }}
                    </div>
                @elseif(isset($status) && $status === false)
                    <div class="alert alert-danger">
                        {{ isset($notify)?$notify:"" }}
                    </div>
                @endif

                <input name="token" type="hidden" value="{{ isset($token)?$token:'' }}">

                @if(isset($notify) === false)
                    @if(isset($forgot) === false || $forgot === false)
                        <div class="form-group">
                            <div class="">
                                <p class="text-md-left pt-2">@lang('auth.old_password')</p>
                            </div>
                            <div class="form-input {{ $errors->has('old_password') ? 'has-error' : ''}}">
                                <input name="old_password" type="password" id="old_password" class="form-control" required autofocus >
                                {!! $errors->first('old_password', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="">
                            <p class="text-md-left pt-2">@lang('auth.new_password')</p>
                        </div>
                        <div class="form-input">
                            <input name="password" type="password" id="password" class="form-control" required>
                        </div>
                    </div>
                    @if(isset($forgot) && $forgot === true)
                        <div class="form-group">
                            <div class="">
                                <p class="text-md-left pt-2">@lang('auth.repeat_password')</p>
                            </div>
                            <div class="form-input">
                                <input name="password_repeat" type="password" id="password_repeat" class="form-control" required>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="">
                        </div>
                        <div class="form-input">
                            <a class="no-effect">
                                <button type="submit" class="btn btn-block btn-booking text-white confirm-rules">@lang('auth.update')</button>
                            </a>
                        </div>
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
