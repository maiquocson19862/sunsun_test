<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
	protected $fillable;
	protected $table = 'tr_payments_history';
	protected $primaryKey = 'tr_payments_history_id';
	public $timestamps = false;

	public function __construct(array $attributes = [])
	{
		$this->fillable = [
			config('const.db.tr_payments_history.BOOKING_ID'),
			config('const.db.tr_payments_history.REPEAT_USER'),
			config('const.db.tr_payments_history.GENDER'),
			config('const.db.tr_payments_history.AGE_VALUE'),
			config('const.db.tr_payments_history.DATE_VALUE'),
			config('const.db.tr_payments_history.PRICE'),
			config('const.db.tr_payments_history.PRODUCT_NAME'),
			config('const.db.tr_payments_history.QUANTITY'),
			config('const.db.tr_payments_history.UNIT'),
			config('const.db.tr_payments_history.MONEY')
		];
		parent::__construct($attributes);
	}
}
