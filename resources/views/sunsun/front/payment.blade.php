@extends('sunsun.front.template')

@section('head')
    @parent
    <link  rel="stylesheet" href="{{asset('sunsun/front/css/base.css').config('version_files.html.css')}}">
    <link  rel="stylesheet" href="{{asset('sunsun/front/css/booking.css').config('version_files.html.css')}}">
    <link rel="stylesheet" href="{{asset('sunsun/front/css/booking-mobile.css').config('version_files.html.css')}}">
    <script src="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('sunsun/lib/sweetalert2/sweetalert2.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('sunsun/lib/animate.css/animate.min.css')}}"/>
    <script  type="text/javascript" src="{{env('SHOP_SCRIPT_URL')}}" ></script>
    <style>
        th {
            background-image: url("/sunsun/imgs/bg_2.png");
            color: #000;
        }
        tr {
            background-image: url('/sunsun/imgs/bg.png');
            color: #000;
        }
        .price-laber{
            font-weight: bold;
        }

    </style>
@endsection
@section('page_title', '支払い入力')
@section('main')
    <main class="main-body">
        <div class="">
            <form style="display: none" action="/complete" method="POST" id="completeForm">
                @csrf
                <input type="hidden" id="bookingID" name="bookingID" value=""/>
                <input type="hidden" id="tranID" name="tranID" value=""/>
            </form>
            <form action="{{route('.make_payment')}}" method="POST" class="booking">
                @csrf
                <div class="booking-warp payment">
                    <!-- <div class="booking-field">
                        <div class="">
                            <p class="text-md-left pt-2 mb-1 font-weight-bold">個人情報</p>
                        </div>
                    </div> -->
                    <div class="booking-field">
                        <div class="">
                            <p class="text-md-left mb-1 font-weight-bold">{{config('booking.services_used.label')}}</p>
                        </div>
                    </div>
                    <div class="">
                        <table class="table table-bordered">
                            <span style="display: none">mark_remove_space</span>
                            <tbody>
                            @foreach($new_bill as $key => $n_bill)
                                @if($n_bill['quantity'] > 0)
                                <tr>
                                    <td class="text-left">{{ $n_bill['name'] }}</td>
                                    <span style="display: none">mark_colon</span>
                                    <td class="text-right">{{ $n_bill['quantity'].$n_bill['unit'] }}</td>
                                    <span style="display: none">mark_space</span>
                                    <td class="text-right">{{ number_format($n_bill['price']) }}</td>
                                    <span style="display: none">mark_yen_newline</span>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th scope="col" style="width: 50%" class="text-left price-laber">{{config('booking.total.label')}}</th>
                                <th scope="col" style="width: 15%" class="text-right price-laber"></th>
                                <span style="display: none">mark_space</span>
                                <th scope="col" style="width: 35%" class="text-right price-laber">{{ isset($total)?number_format($total):0 }}</th>
                                <span style="display: none">mark_yen</span>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    @include('sunsun.front.parts.payment_form', ['new' => '1'])
                    @include('sunsun.front.parts.payment_method', ['new' => '1', 'check_using_coupon' => $check_using_coupon])

                    <div class="pl-4 pr-1">
                        <p class="text-left pt-2">回数券をお持ちの方へ</p>
                        <p class="text-left">酵素浴以外のメニューは現地でお支払いください。</p>
                        <p class="text-left">回数券の購入は、クレジットカードをご利用いただけません。</p>
                    </div>
                </div>
                <div class="foot-confirm">
                    <div class="confirm-button">
                        <div class="button-left">
                            <button id="btn-home" type="button" class="btn btn-block text-white btn-back">キャンセルする</button>
                        </div>
                        <div class="button-right">
                            <button id="make_payment" type="button" class="btn btn-block btn-booking text-white">予約する</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('script')
    <script>
        function payment_init() {
            Multipayment.init('{{ env("SHOP_ID")  }}');
        }
    </script>
    @parent
    <script  type="text/javascript" src="{{asset('sunsun/front/js/base.js').config('version_files.html.css')}}"></script>
    <script  type="text/javascript" src="{{asset('sunsun/front/js/payment.js').config('version_files.html.css')}}"></script>
@endsection
