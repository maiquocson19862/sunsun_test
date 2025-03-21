<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class MsKubun extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'ms_kubun';

    protected $primaryKey = 'ms_kubun_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.ms_kubun.KUBUN_TYPE'),
            config('const.db.ms_kubun.KUBUN_ID'),
            config('const.db.ms_kubun.KUBUN_VALUE'),
            config('const.db.ms_kubun.SORT_NO'),
            config('const.db.ms_kubun.NOTES'),
            config('const.db.ms_kubun.TIME_HOLIDAY'),
        ];
        parent::__construct($attributes);
    }
    public static function GetTimeKubunHoliday($data) {
		$datetime = ($data == null) ? Carbon::now() : new Carbon($data["date_search"]);
		$date_search = $datetime->format('Ymd');
		$kubun_pet = \Helper::getKubunTypePet($date_search);
        return MsKubun::selectRaw("kubun_value, time_holiday,
            (case
                when kubun_type = '".$kubun_pet."' then 2
                when kubun_type = '021' then 3
                else 1
            end) as type_holiday")
        ->whereRaw("kubun_type in ('013','014','021','036','".$kubun_pet."')")
        ->distinct()
        ->orderBy("kubun_type")->orderBy("time_holiday")->get();
    }
    public static function GetKubunHotelHoliday() {
        return MsKubun::selectRaw("kubun_value, kubun_id as time_holiday,
                5 as type_holiday
            ")
        ->whereRaw(" kubun_type = '011' and kubun_id <> '01' and kubun_id <> '03' ")->orderBy("kubun_id")->get();
    }
}
