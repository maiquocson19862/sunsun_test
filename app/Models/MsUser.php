<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MsUser extends Authenticatable
{
    use Notifiable;

    protected $attributes;

    protected $fillable;

    // Table Name
    protected $table = 'ms_user';

    // Primary Key
    public $primaryKey = 'ms_user_id';
    // Timestamps
    public $timestamps = true;
    public function __construct(array $attributes = [])
    {
        $this->attributes = [];

        $this->fillable = [
            config('const.db.ms_user.USERNAME'),
            config('const.db.ms_user.TEL'),
            config('const.db.ms_user.EMAIL'),
            config('const.db.ms_user.GENDER'),
            config('const.db.ms_user.BIRTH_YEAR'),
            config('const.db.ms_user.PASSWORD'),
        ];

        parent::__construct($attributes);

    }

    public function is_user () {
        return $this->user_type === config('const.auth.permission.USER');
    }

    public function is_admin () {
        return $this->user_type === config('const.auth.permission.ADMIN');
    }

    public function is_super_admin() {
        return $this->user_type === config('auth.permission.SUPER_ADMIN');
    }
}
