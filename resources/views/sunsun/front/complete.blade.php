@extends('sunsun.front.template')
@section('head')
    @parent
    <style>
        @media only screen and (min-width: 768px) {
            .mean-bar {
                display: none;
            }
        }
        .breadc, .main-body-head {
            display: none;
        }
        .btn:hover {
            opacity: 0.7;
            color: #ffffff;
        }
        a:hover {
            text-decoration: none;
        }
        .warp-content {
            text-align: center;
        }
        h2.success-title {
            font-size: 1.5rem;
            padding: 20px 0 20px 0;
        }
        p.des-detail {
            text-align: center;
        }
        .success-detail {
            padding: 15px 0 15px 0;
        }
        .btn-back-home {
            background: #d7751e;
            color: #ffffff;
        }
        .mean-container {
            display: inline;
        }
        .main-footer {
            margin-top: 10px;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        p.des-detail.last {
            margin-bottom: 20px;
        }
        .mean-bar {
            margin-bottom: 20px !important;
        }
    </style>
@endsection
@section('page_title', '予約確認')
@section('main')
    <main>
        <div class="container">
            <div class="row  d-flex justify-content-center">
                <div class="col-11">
                    <div class="warp-content">
                        <div class="icon-success">
                            <img src="{{asset('sunsun/imgs/icons/success.svg')}}" alt="success" width="50px">
                        </div>
                        <h2 class="success-title">ご予約が完了しました</h2>
{{--                        <p class="success-detail">Your order number is {{ $bookingID  }}</p>--}}
{{--                        @if(isset($tranID))--}}
{{--                        <p class="success-detail">Your payment number is {{ $tranID  }}</p>--}}
{{--                        @endif--}}
                        <p class="des-detail">ご予約いただき、誠にありがとうございます。</p>
                        <p class="des-detail last">お客様のお越しを楽しみにお待ちしております。</p>
                        <a href="http://sun-sun33.com/"><button class="btn btn-back-home">ホームへ</button></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    @parent

@endsection
