<?php
namespace App\Http\Helpers;
use \Session;
use App\Models\MsHoliday;
use App\Models\MsDayOn;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
class Helper
{
    public static function getDisableAll($date) {
        $disable_all = "";
        $date_holiday = Session::get("date_holiday");
        $date_check = str_replace("/","",$date);
        if (!empty($date_holiday)) {
            foreach ($date_holiday as $key => $value) {
                $where_date = str_replace("/","",$value);
                if ($where_date == $date_check) {
                    $disable_all = "1"; // ngay nghi
                    break;
                }
            }
        }
        return $disable_all;
    }
    public static function setHoliday($list_time, $time, $type, $disable_all) {
        if (empty($disable_all)) {
            if (!empty($list_time)) {
                foreach ($list_time as $row) {
                    // Log::debug($time. " - - - ". $row["time_holiday"]);
                    if  (   ($type==$row["type_holiday"]) &&
                            ($time==$row["time_holiday"] || empty($row["time_holiday"]))
                        )
                        return " bg-dis ";
                }
            }
            return "";
        }
        return " bg-dis";
    }
    public static function getTimeHoliday($date) {
        $date = str_replace("/","",$date);
        return MsHoliday::whereRaw("type_holiday is not null and date_holiday = '$date' ")->get();
    }
    public static function getKubunTypePrice($service_date_start,$created_at = null) {
        $cre_check = ($created_at == null) ? Carbon::now() : new Carbon($created_at);
        $cre_check = $cre_check->format('Ymd');
        if ($cre_check < '20220501') {
            return '030';
        } else if ($service_date_start < '20220601') {
            return '030';
        } else if ($service_date_start < env("DATE_NEW_PET")) {
            return '031';
        } else return '034';
    }
    public static function getDayOn($year = null) {
        $date_selected = [];
        $where_year = "";
        $range_day = MsDayOn::Select("date_on");
        if (!empty($year)) {
            $where_year = " SUBSTR(date_on,1,4) = '$year' ";
            $range_day->whereRaw($where_year);
        }
        $range_day = $range_day->get();
        if (!empty($range_day)) {
            foreach($range_day as $da){
                array_push($date_selected, Carbon::parse($da->date_on)->format('Y/m/d'));
            }
        }
        return $date_selected;
    }
    public static function CheckDayDis($day_week,$date) {
        $return = "";

        if (in_array($day_week, config('const.off_def')) ) {
            $return = " bg-dis ";
        }

        if (empty($date)) return $return;
        $date_selected = [];
        $where_date = "";
        $range_day = MsDayOn::Select("date_on");
        if (!empty($date)) {
            $where_date = " date_on = '$date' ";
            $range_day->whereRaw($where_date);
        }
        $range_day = $range_day->get();
        if (empty($range_day) || count($range_day) == 0) {
            return $return;
        }
        return "";
    }
    public static function getKubunTypePet($service_date_start) {
        if ($service_date_start < env("DATE_NEW_PET")) {
            return config('const.db.kubun_type_value.TIME_PET');
        }
        return config('const.db.kubun_type_value.TIME_PET_NEW');
    }
    public static function getTimeAdmin($service_date_start) {
        if ($service_date_start < env("DATE_NEW_PET")) {
            return config('const.time_admin');
        }
        return config('const.time_admin_new');
    }
}