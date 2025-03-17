@extends('sunsun.front.template')
@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}"/>
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}"/>
    <script src="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.css')}}"/>
@endsection
@section('page_title', 'キャンセルポリシー')
@section('main')
    <main class="main-body">
        <div class="booking mb-4">
            <div class="booking-warp index">
                <div id="law">
                    <h3>キャンセルについて</h3>
                    <p>ご予約の日時の変更、キャンセルは、ご予約日前日までに必ずご連絡をお願いします。<br/>
                        (当日のご連絡の無いキャンセルは１００%のキャンセル料を戴きます。ご了承下さい。)
                    </p>
                    <h3>ご予約時間に間に合わない場合</h3>
                    <p>
                        当店は完全予約制になっております。<br/>
                        その為に、ご来店時間の時間厳守をお願いしています。<br/>
                        遅れそうだと分かった時点でご連絡をお願い申し上げます。<br/>
                        １０分以上遅れる場合は、問診や説明を前後させて頂きます。また、状況により入酵時間が短くなったり、入酵出来ない場合がございますので、ご了承くださいますようお願い申し上げます。<br/>
                        ご予約の時間を１５分経過してもご来店されない場合は、キャンセル料が発生します。必ずご連絡をよろしくお願い致します。<br/>
                        (交通渋滞などでやむをえない遅刻もあると思います。出来る限りの調整はさせて頂きます。早めのご連絡をよろしくお願い致します。)
                    </p>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('footer')
    @parent
@endsection