<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Yoyaku extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'tr_yoyaku';

    protected $primaryKey = 'tr_yoyaku_id';
    // Timestamps
    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.tr_yoyaku.BOOKING_ID'),
            config('const.db.tr_yoyaku.REF_BOOKING_ID'),
            config('const.db.tr_yoyaku.NAME'),
            config('const.db.tr_yoyaku.PHONE'),
            config('const.db.tr_yoyaku.EMAIL'),
            config('const.db.tr_yoyaku.REPEAT_USER'),
            config('const.db.tr_yoyaku.TRANSPORT'),
            config('const.db.tr_yoyaku.BUS_ARRIVE_TIME_SLIDE'),
            config('const.db.tr_yoyaku.REPEAT_USER'),
            config('const.db.tr_yoyaku.TRANSPORT'),
            config('const.db.tr_yoyaku.BUS_ARRIVE_TIME_SLIDE'),
            config('const.db.tr_yoyaku.BUS_TIME_VALUE'),
            config('const.db.tr_yoyaku.PICK_UP'),
            config('const.db.tr_yoyaku.COURSE'),
            config('const.db.tr_yoyaku.GENDER'),
            config('const.db.tr_yoyaku.AGE_TYPE'),
            config('const.db.tr_yoyaku.AGE_VALUE'),
            config('const.db.tr_yoyaku.SERVICE_DATE_START'),
            config('const.db.tr_yoyaku.SERVICE_DATE_END'),
            config('const.db.tr_yoyaku.SERVICE_TIME_1'),
            config('const.db.tr_yoyaku.SERVICE_TIME_2'),
            config('const.db.tr_yoyaku.SERVICE_GUEST_NUM'),
            config('const.db.tr_yoyaku.SERVICE_PET_NUM'),
            config('const.db.tr_yoyaku.LUNCH'),
            config('const.db.tr_yoyaku.LUNCH_GUEST_NUM'),
            config('const.db.tr_yoyaku.WHITENING'),
            config('const.db.tr_yoyaku.PET_KEEPING'),
            config('const.db.tr_yoyaku.STAY_ROOM_TYPE'),
            config('const.db.tr_yoyaku.STAY_GUEST_NUM'),
            config('const.db.tr_yoyaku.STAY_CHECKIN_DATE'),
            config('const.db.tr_yoyaku.STAY_CHECKOUT_DATE'),
            config('const.db.tr_yoyaku.PAYMENT_METHOD'),
            config('const.db.tr_yoyaku.MS_USER_ID'),
            config('const.db.tr_yoyaku.CREATED_AT')
        ];
        parent::__construct($attributes);
    }

    public function get_time() {
        return $this->hasMany('App\Models\YoyakuDanjikiJikan','booking_id','booking_id');
    }

    public function get_transport () {
        return $this
            ->hasOne('App\Models\MsKubun','kubun_id','transport')->where('kubun_type', '002');
    }

    public function get_pick_up () {
        return $this
            ->hasOne('App\Models\MsKubun','kubun_id','pick_up')->where('kubun_type', config('const.db.kubun_type_value.pick_up'));
    }

    public function get_course () {
        return $this
            ->hasOne('App\Models\MsKubun','kubun_id','course')->where('kubun_type', config('const.db.kubun_type_value.course'));
    }

    public function get_gender () {
        return $this
            ->hasOne('App\Models\MsKubun','kubun_id','gender')->where('kubun_type',  config('const.db.kubun_type_value.gender'));
    }

    public function get_bed () {
        // service for bed
        if ($this->get_course()->kubun_id == '05') {
            return $this
                ->hasOne('App\Models\MsKubun','kubun_id','bed')->where('kubun_type', config('const.db.kubun_type_value.bed_pet'));
        }
        // service for man
        if ($this->get_gender()->kubun_id == '01') {
            return $this
                ->hasOne('App\Models\MsKubun','kubun_id','bed')->where('kubun_type',  config('const.db.kubun_type_value.bed_male'));
        }
        // service for women
        if ($this->get_gender()->kubun_id == '02') {
            return $this
                ->hasOne('App\Models\MsKubun','kubun_id','bed')->where('kubun_type',  config('const.db.kubun_type_value.bed_female'));
        }

    }
}
