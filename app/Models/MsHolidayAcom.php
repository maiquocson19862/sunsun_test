<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MsHolidayAcom extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'ms_holiday_acom';

    protected $primaryKey = 'ms_holiday_acom_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.ms_holiday_acom.DATE_HOLIDAY'),
            config('const.db.ms_holiday_acom.NOTE_HOLIDAY')
        ];
        parent::__construct($attributes);
    }

}
