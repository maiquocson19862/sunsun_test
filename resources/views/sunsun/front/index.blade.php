@extends('sunsun.front.template')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}"/>
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}"/>
    <script src="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.css')}}"/>
@endsection
@section('page_title', '事前注意')
@section('main')
    <main class="main-body">
        <div class="">
            <div class="booking">
            <div class="booking-warp index">
                        <div class="index-field">
                        </div>
                        <div class="index-field">
                            <div class="index-field-head">
                                <p>ご予約に際して、以下の注意事項をご確認ください。</p>
                            </div>
                            <div class="index-border">
                                <div class="index-content">
                                    <ol>
                                        <li class="new-line"><span class="text-red">新規のお客様</span>は、入酵前に問診、説明等ございますので、<span class="text-red">予約時間の15分前に到着</span>をお願いします。</li>
                                        <li class="new-line">酵素浴は、お着替えなどを含めると、<span class="text-red">入浴自体は1時間ぐらい</span>かかります。お子様やペットを交代で面倒みられる場合、1時間以上間隔をあけてください。</li>
                                        <li class="new-line"><span class="text-red">5才以下のお子様</span>は、他のお客様のご迷惑になる場合がありますので、貸切のご利用をご検討ください。</li>
                                        <li class="new-line">次の動作がお一人では難しい場合、<span class="text-red">お客様で介助</span>をお願いいたします。
                                            <ul class="pl-3">
                                                <li>50cm以上の段差を越える</li>
                                                <li>一人でシャワーを浴びる</li>
                                                <li>一人で立ち上がる</li>
                                            </ul>
                                        </li>
                                        <!-- 2020/05/29 son edit 139 -->
                                        <li class="new-line">ご来店時はマスク着用でお願い致します。</li>
                                        <li class="new-line">ご来店前にご自宅で検温をお願い致します。<br/>
                                            37.4度以上の方は入店を控えていただきます。<br/>ご理解いただけますようお願い致します。
                                        </li>
                                        <!-- 2020/05/29 son edit end -->
                                    </ol>
                                </div>
                            </div>
                            <div class="index-field-foot">
                                <div class="content-right container-checkbox">
                                    <label for="confirm">
                                        <input type="checkbox" class="" id="confirm">
                                        <span class="checkmark index"></span>
                                        <span style="line-height: 27px;">上記注意事項を確認し、同意いたします。</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="foot-confirm">
                        <div class="confirm-button-payment mb-3">
                            <button type="submit" id="confirm-rules" class="btn btn-block btn-booking text-white disabled">予約入力へ</button>
                        </div>
                        <div class="d-flex align-items-center justify-content-center mb-4">
                            <div style="width: 100%; margin: 0 2.5vw;">
                                <a class="center-link" style="float: left" href="{{ route('law') }}">特定商取引法に基づく表記</a>
                                <a class="center-link" style="float: right" href="{{ route('cancellation_policy') }}">キャンセルポリシー</a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </main>
@endsection
@section('footer')
    @parent
@endsection
@section('script')
    @parent
    <script>
        $('#confirm').on('change', function() {
            if($(this).is(":checked")){
                $("#confirm-rules").removeClass("disabled");
            }else{
                $("#confirm-rules").addClass("disabled");
            }
        });
        $('#confirm-rules').click(function(){
            if($(this).hasClass('disabled') == false){
                location.href='/booking';
            }
        });
    </script>
    <script src="{{asset('sunsun/front/js/base.js').config('version_files.html.css')}}"></script>
    <script>
        window.addEventListener("hashchange", function(e) {
            e.preventDefault();
        })
    </script>
@endsection