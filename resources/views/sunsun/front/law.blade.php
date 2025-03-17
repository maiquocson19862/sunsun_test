@extends('sunsun.front.template')

@section('head')
    @parent
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}"/>
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}"/>
    <script src="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.css')}}"/>
@endsection
@section('page_title', '特定商取引法に基づく表記')
@section('main')
    <main class="main-body">
        <div class="booking mb-4">
            <div class="booking-warp index">
                <div id="law">
                    <h2>特定商取引法に基づく表記</h2>
                        <h3>会社名</h3>
                    <p>D.DREAMS合同会社</p>
                        <h3>事業者の名称</h3>
                    <p>伊達久美子</p>
                    <h3>事業者の所在地</h3>
                    <p>郵便番号 ：6560131</p>
                    <p>住所 ：兵庫県南あわじ市広田中筋296-1</p>
                    <h3>事業者の連絡先</h3>
                    <p>電話番号 ： 0799-20-7801</p>
                    <p>営業時間： 10:00〜18:00（金曜日のみ20:00）</p>
                    <p>定休日：水曜日、木曜日</p>
                    <h3>商品価格</h3>
                    <p>商品価格は、表示された金額（表示価格/消費税込）と致します。</p>
                    <h3>代金清算方法</h3>
                    <p>・フロントにて精算：現金またはクレジットカード</p>
                    <p>・クレジットカードにて事前精算：オンライン予約完了時にクレジットカードで一括お支払</p>
                    <p>（カード会社からのご請求はご出発日基準になります）</p>
                    <h3>引渡時期（予約成立時期）</h3>
                    <p>当サイトを利用してご予約をされる場合は、予約完了のご案内がお客様画面上に表示された時点で、ご予約の成立となります。</p>
                    <p>尚、クレジットカードにて事前精算の場合は、クレジット決済が完了した時点でご予約の成立となります。</p>
                    <h3>返品</h3>
                    <p>商品及びサービスの特性上、施設利用後における返品はいたしかねます。</p>
                    <h3>キャンセル料</h3>
                    <p>・予約日前日までのキャンセル： 無料</p>
                    <p>・予約日当日のキャンセル： 一律2,000円</p>
                    <p>・無連絡の場合 ご予約料金の100％</p>
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
