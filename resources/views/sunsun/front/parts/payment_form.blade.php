@php
    $check_ref = (isset($data_booking->ref_booking_id) === true)?"display: none;":"";
@endphp
<input type="hidden" name="Token" id="Token"value="">
<input type="hidden" name="Amount" id="Amount"value="{{ isset($bill['total'])?$bill['total']:''  }}">
<div class="booking-field" style="{{ $check_ref }}">
    <div class="booking-field-label @if(isset($new) && (!$new)) {{ 'booking-laber-padding' }} @endif">
        <p class="text-md-left pt-2">{{config('booking.name.label')}}<span class="node-text">{{config('booking.name.node_label')}}</span><span class="text-red">*</span></p>
    </div>
    @php
        $field_name = isset($data_booking->name)?$data_booking->name:'';
        if($field_name == ''){
            $field_name = ((isset($new) && ($new == 1)) && isset($auth_username) && $auth_username != '')?$auth_username:'';
        }
    @endphp
    <div class="booking-field-content">
        <div>
            <input name="name" id="name" type="text" inputmode="katakana" class="form-control date-book-input" placeholder="{{config('booking.name.placeholder')}}" maxlength="255" value="{{ $field_name }}"/>
        </div>
    </div>
</div>
<div class="booking-field" style="{{ $check_ref }}">
    <div class="booking-field-label @if(isset($new) && (!$new)) {{ 'booking-laber-padding' }} @endif">
        <p class="text-md-left" style="line-height: 115%;">{{config('booking.phone.label')}}<span class="text-red">*</span></p>
        <p class="node-text pt-2" style="line-height: 100%;">（携帯番号）</p>
    </div>
    @php
        $field_tel = isset($data_booking->name)?$data_booking->phone:'';
        if($field_tel == ''){
            $field_tel = ((isset($new) && ($new == 1))  && isset($auth_tel) && $auth_tel != '')?$auth_tel:'';
        }
    @endphp
    <div class="booking-field-content">
        <div>
            <input name="phone" id="phone" type="text" inputmode="tel" class="form-control date-book-input numberphone" placeholder="半角、ハイフン不要" maxlength="11" value="{{ $field_tel }}"/>
        </div>
    </div>
</div>
<div class="booking-field"  style="{{ $check_ref }}">
    <div class="booking-field-label @if(isset($new) && (!$new)) {{ 'booking-laber-padding' }} @endif">
        <p class="text-md-left pt-2">{{config('booking.email.label')}}<span class="text-red">*</span></p>
    </div>
    @php
        $field_email = isset($data_booking->name)?$data_booking->email:'';
        if($field_email == ''){
            $field_email = ((isset($new) && ($new == 1)) && isset($auth_email) && $auth_email != '')?$auth_email:'';
        }
        if($field_email == '' && isset($type_admin)){
            $field_email = "arigatoukouso@sun-sun33.com";
        }
    @endphp
    <div class="booking-field-content">
        <div>
            <input name="email" id="email" type="text" inputmode="email" class="form-control date-book-input" maxlength="255" value="{{ $field_email }}"/>
        </div>
    </div>
</div>
