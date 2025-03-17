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
        .form-label {
            width: auto;
        }
    </style>
@endsection
@section('page_title', 'ユーザー登録')
@section('main')
    <main class="main-body">
        <div class="">
            <div class="user-warp">
                {!! Form::open(['action' => ['Sunsun\Auth\MsUserController@create'], 'method' => 'POST', 'class' => 'form']) !!}

                <div class="form-group mb-3 {{ $errors->has('username') ? 'has-error' : ''}}">
                    <div class="form-label">
{{--                        {!! Form::label('username', '名前（カタカナ）') !!}--}}
                        <span>名前</span><span class="node-text">（カタカナ）</span>
                    </div>
                    <div class="form-input">
                        {!! Form::text('username', old('username') , ['class' => 'form-control mb-0', 'required' => 'required','inputmode' => 'katakana']) !!}
                        {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group mb-3 {{ $errors->has('tel') ? 'has-error' : ''}}">
                    <div class="form-label">
{{--                        {!! Form::label('tel', '電話番号<p class="node-text">（半角、ハイフン不要）</p>') !!}--}}
                        <span>電話番号</span><span class="node-text">（半角、ハイフン不要）</span>
                    </div>
                    <div class="form-input">
                        {!! Form::text('tel',  old('tel'), ['class' => 'form-control numberphone', 'required' => 'required', 'maxlength' => '11']) !!}
                        {!! $errors->first('tel', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group mb-3 {{ $errors->has('email') ? 'has-error' : ''}}">
                    <div class="form-label">
                        {!! Form::label('email', 'メールアドレス') !!}
                    </div>
                    <div class="form-input">
                        {!! Form::text('email', old('email'), ['class' => 'form-control', 'required' => 'required', 'pattern' => '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$']) !!}
                        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="form-group mb-3 {{ $errors->has('password') ? 'has-error' : ''}}">
                    <div class="form-label">
                        {!! Form::label('password', 'パスワード') !!}
                    </div>
                    <div class="form-input">
                        {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}
                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>

                <!-- <div class="form-group mb-3">
                    <div class="form-label">
                        {!! Form::label('gender', '性別') !!}
                    </div>
                    <div class="form-input">
                        {!! Form::select('gender', ['female' => '女性', 'male' => '男性'], old('gender'), ['class' => "form-control", 'required' => 'required']) !!}
                    </div>
                </div>
                <div class="form-group mb-3">
                    <div class="form-label">
                        {!! Form::label('birth_year', '年齢') !!}
                    </div>
                    <div class="form-input">
                        {!! Form::selectRange('birth_year', 1930, 2019, old('birth_year'),['class' => "form-control", 'required' => 'required'] ) !!}
                    </div>
                </div> -->
                <div class="form-group" style="margin-top: 30px">
                    <div class="form-label">
                    </div>
                    <div class="form-input">
                        {{Form::button('登録', ['type'=> 'submit','class'=>'btn btn-block btn-booking text-white confirm-rules'])}}
                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-3">
                        <a class="center-link" href="/login">ログイン</a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </main>
    <hr class="footer-space">
@endsection

@section('footer')
    @parent
@endsection

@section('script')
    @parent
    <script src="{{asset('sunsun/front/js/base.js').config('version_files.html.js')}}"></script>
    <script src="{{asset('sunsun/auth/js/validate-form.js').config('version_files.html.js')}}"></script>
@endsection

