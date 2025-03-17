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
@section('page_title', 'アカウント')
@section('main')
    <main class="main-body">
        <div class="">
            <div class="user-warp">
                {!! Form::open(['action' => ['Sunsun\Auth\AuthUserController@edit'], 'method' => 'POST', 'class' => 'form']) !!}
                @if(isset($success))
                    <div class="alert alert-success">
                        {{ $success }}
                    </div>
                @endif
                <div class="form-group {{ $errors->has('username') ? 'has-error' : ''}}">
                    <div class="form-label">
                        <!-- {!! Form::label('username', '名前（カタカナ）') !!} -->
                        <span>名前</span><span class="node-text">（カタカナ）</span>
                    </div>
                    <div class="form-input">
                        {!! Form::text('username', $user->username, ['class' => 'form-control', 'required' => 'required']) !!}
                        {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('tel') ? 'has-error' : ''}}">
                    <div class="form-label">
                        <!-- {!! Form::label('tel', '電話番号') !!} -->
                        <span>電話番号</span><span class="node-text">（半角、ハイフン不要）</span>
                        <p class="text-md-left pt-2"></p>
                    </div>
                    <div class="form-input">
                        {!! Form::text('tel', $user->tel, ['class' => 'form-control numberphone', 'required' => 'required', 'maxlength' => '11']) !!}
                        {!! $errors->first('tel', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    <div class="form-label">
                        {!! Form::label('email', 'メールアドレス') !!}
                        <p class="text-md-left pt-2"></p>
                    </div>
                    <div class="form-input">
                        {!! Form::text('email', $user->email, ['class' => 'form-control', 'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$', 'disabled' => 'disabled']) !!}
                        <!-- {!! $errors->first('username', '<p class="help-block">:message</p>') !!} -->
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">
                        <a href="{{ route('.change_password') }}" class="no-effect color-link">パスワードの変更</a>
                    </div>
                    <div class="form-input">
                    </div>
                </div>
                <!-- <div class="form-group">
                    <div class="form-label">
                        {!! Form::label('birth_year', '年齢') !!}
                    </div>
                    <div class="form-input">
                        {!! Form::selectRange('birth_year', 1930, 2019, $user->birth_year,['class' => "form-control", 'required' => 'required'] ) !!}
                    </div>
                </div> -->
                <div class="form-group py-4">
                    <div class="form-label">
                    </div>
                    <div class="form-input">
                        {{Form::button('登録', ['type'=> 'submit','class'=>'btn btn-block btn-booking text-white confirm-rules'])}}
                    </div>
                </div>

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
    <script src="{{asset('sunsun/front/js/base.js').config('version_files.html.js')}}"></script>
    <script src="{{asset('sunsun/auth/js/validate-form.js').config('version_files.html.js')}}"></script>
@endsection
