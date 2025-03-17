<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable;

    // Table Name
    protected $table = 'tr_reminders';

    protected $primaryKey = 'id';
    // Timestamps
    public $timestamps = false;
    /**
     * @var string
     */

    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            config('const.db.tr_reminder.EMAIL_TARGET'),
            config('const.db.tr_reminder.CONTENT'),
            config('const.db.tr_reminder.TIME'),
            config('const.db.tr_reminder.TURN')
        ];
        parent::__construct($attributes);
    }
}
