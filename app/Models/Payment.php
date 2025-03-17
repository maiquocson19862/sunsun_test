<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'tr_payments';

    protected $primaryKey = 'id';
    // Timestamps
    public $timestamps = false;
    /**
     * @var string
     */

    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.tr_payments.BOOKING_ID'),
            config('const.db.tr_payments.ACCESS_ID'),
            config('const.db.tr_payments.ACCESS_PASS')
        ];
        parent::__construct($attributes);
    }
}
