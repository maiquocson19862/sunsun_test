<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MsHoliday extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'ms_holiday';

    protected $primaryKey = 'ms_holiday_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.ms_holiday.DATE_HOLIDAY'),
            config('const.db.ms_holiday.TIME_HOLIDAY'),
            config('const.db.ms_holiday.TYPE_HOLIDAY'),
            config('const.db.ms_holiday.NOTE_HOLIDAY')
        ];
        parent::__construct($attributes);
    }

}
