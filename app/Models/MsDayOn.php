<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MsDayOn extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'ms_day_on';

    protected $primaryKey = 'ms_day_on_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.ms_day_on.DATE_ON'),
            config('const.db.ms_day_on.NOTE_DATE_ON')
        ];
        parent::__construct($attributes);
    }

}
