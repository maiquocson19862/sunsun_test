<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class YoyakuDanjikiJikan extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'tr_yoyaku_danjiki_jikan';

    // Timestamps
    public $timestamps = true;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.tr_yoyaku_danjiki_jikan.BOOKING_ID'),
            config('const.db.tr_yoyaku_danjiki_jikan.SERVICE_DATE'),
            config('const.db.tr_yoyaku_danjiki_jikan.SERVICE_TIME_1'),
            config('const.db.tr_yoyaku_danjiki_jikan.SERVICE_TIME_2'),
            config('const.db.tr_yoyaku_danjiki_jikan.NOTES')
        ];
        parent::__construct($attributes);
    }
}
