<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Setting extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'ms_setting';

    protected $primaryKey = 'ms_setting_id';
    // Timestamps
    public $timestamps = false;
    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.ms_setting.ACCOMMODATION_FLG'),
        ];
        parent::__construct($attributes);
    }

}
