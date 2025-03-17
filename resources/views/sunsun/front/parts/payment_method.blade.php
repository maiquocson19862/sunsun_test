@php
    $check_ref = (isset($data_booking->ref_booking_id) === true)?"display: none;":"";
@endphp
<div class=""  style="{{ $check_ref }}">
    <div class="">
        <p class="pt-3 @if(isset($new) && (!$new)) booking-laber-padding @else font-weight-bold  @endif">{{config('booking.payment_method.label')}}</p>
    </div>
</div>
{{--<div class="pl-4 mt-2">--}}
{{--    <div class="custom-control custom-radio">--}}
{{--        <input type="radio" class="custom-control-input payment-method" id="credit-card" name="payment-method" value="1"--}}
{{--        {{--}}
{{--            isset($data_booking->payment_method)?(($data_booking->payment_method == 1)?'checked':''):''--}}
{{--        }}--}}
{{--        @if(!isset($new) || ($new != '0'))--}}
{{--            checked--}}
{{--        @endif--}}
{{--        />--}}
{{--        <label class="custom-control-label" for="credit-card">クレジットカード</label>--}}
{{--    </div>--}}
{{--</div>--}}
<div id="accordion" class="mb-2"  style="{{ $check_ref }}">
    <div class="card payment">
        <div class="card-header" id="headingOne"  data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                <div class="btn">
                    <input type="radio" class="custom-control-input payment-method" id="credit-card" name="payment-method" value="1"
                           {{
                               isset($data_booking->payment_method)?(($data_booking->payment_method == 1)?'checked':''):''
                           }}
                           @if(!isset($new) || ($new != '0'))
                           checked
                        @endif
                    />
                    <label class="custom-control-label" for="credit-card">クレジットカード</label>
                </div>
            </h5>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body card-payment">
                <div class="booking-field btn-block">
                    <div class="booking-field-label  booking-laber-padding">
                        <span class="pt-2">カード番号<span class="text-red">*</span></span>
                    </div>
                    <div class="booking-field-content">
                        <input type="text" inputmode="decimal" id="card-number" class="form-control" value="" placeholder="半角で入力してください" maxlength="23">
                    </div>
                </div>
                <div class="booking-field btn-block">
                    <div class="booking-field-label  booking-laber-padding">
                        <span class="pt-2">有効期限<span class="text-red">*</span></span>
                    </div>
                    <div class="booking-field-content">
                        <div class="row100">
                            <div class="row50" style="width:30%">
                                <select class="form-control select_mini_padding" id="expire-month">
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                            <div class="rowex" style="width:20%; margin:unset">
                                <div>月／</div>
                            </div>
                            <div class="row50" style="width:38%">
                                <select class="form-control select_mini_padding" id="expire-year">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>
                            </div>
                            <div class="rowex" style="width:12%; margin:unset">
                                <div>年</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="booking-field btn-block">
                    <div class="booking-field-label  booking-laber-padding">
                        <div class="" style="line-height: 130%;">
                            <div>
                                セキュリティ
                            </div>
                            <div>
                                コード<span class="text-red">*</span>
                            </div>
                        </div>
                    </div>
                    <div class="booking-field-content">
                        <div class="rowsecret" style="width:30% !important">
                            <input  type="text" inputmode="decimal"  id="card-secret" class="form-control typing-none" value="" maxlength="4" placeholder="半角">
                        </div>
                        <div class="row100">
                            <div class="row50">
                            </div>
                            <div class="row50 pt-2">
                                <div class="custom-link node-text">セキュリティコードとは？</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card payment" @if((isset($check_using_coupon) === true) && ($check_using_coupon === false)) style="border-bottom: 0px;" @endif>
        <div class="card-header" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            <h5 class="mb-0">
                <div class="btn">
                    <input type="radio" class="custom-control-input payment-method" name="payment-method" value="2"  id="local-cash" @if(isset($new) && (!$new))  {{ isset($data_booking->payment_method)?(($data_booking->payment_method == 2)?'checked':''):'checked' }}   @endif>
                    <label class="custom-control-label" for="local-cash">現地現金</label>
                </div>
            </h5>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
{{--            <div class="card-body">--}}
{{--                Content 2--}}
{{--            </div>--}}
        </div>
    </div>
    @if(((isset($check_using_coupon) === true) && ($check_using_coupon === true)) || (isset($check_using_coupon) === false))
        <div class="card payment" style="border-bottom: 0px;">
            <div class="card-header" id="headingThree" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                <h5 class="mb-0">
                    <div class="btn">
                        <input type="radio" class="custom-control-input payment-method" name="payment-method" value="3"  {{ isset($data_booking->payment_method)?(($data_booking->payment_method == 3)?'checked':''):'' }}  id="coupon">
                        <label class="custom-control-label" for="coupon">回数券</label>
                    </div>
                </h5>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
    {{--            <div class="card-body">--}}
    {{--                Content 3--}}
    {{--            </div>--}}
            </div>
        </div>
    @endif
</div>

<div class="credit-card" @if(isset($new) && (!$new))  style="display:none;"  @endif>
    <div class="cc-block">
        <!-- <div class="credit-card-line">
            <div class="card-number floatinglabel">
                <span>Card Number</span>
                <input type="text" id="card-number" class="form-control typing-none" value="" placeholder="Card Number" maxlength="19">
            </div>
            <div class="card-img d-flex justify-content-center align-items-center">
                <img src="{{ asset('sunsun/svg/cc-blank.svg') }}" class="img-fluid scale-image" alt="">
            </div>
        </div>
        <div class="credit-card-line2">
            <div class="card-expire floatinglabel">
                <span>MM/YY</span>
                <input type="text" id="card-expire" class="form-control typing-none" value="" placeholder="MM/YY" maxlength="5">
            </div>
            <laber class="card-secret floatinglabel">
                <span>CVV</span>
                <input type="password" id="card-secret" class="form-control typing-none" value="" placeholder="CVC" maxlength="3">
            </laber>
        </div> -->
    </div>
</div>
{{--<div class="pl-4 mt-2">--}}
{{--    <div class="custom-control custom-radio">--}}
{{--        <input type="radio" class="custom-control-input payment-method" name="payment-method" value="2"  id="local-cash" @if(isset($new) && (!$new))  {{ isset($data_booking->payment_method)?(($data_booking->payment_method == 2)?'checked':''):'checked' }}   @endif>--}}
{{--        <label class="custom-control-label" for="local-cash">現地現金</label>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="pl-4 mt-2 @if(isset($new) && (!$new))  booking-field  @endif">--}}
{{--    <div class="custom-control custom-radio">--}}
{{--        <input type="radio" class="custom-control-input payment-method" name="payment-method" value="3"  {{ isset($data_booking->payment_method)?(($data_booking->payment_method == 3)?'checked':''):'' }}  id="coupon">--}}
{{--        <label class="custom-control-label" for="coupon">回数券</label>--}}
{{--    </div>--}}
{{--</div>--}}
