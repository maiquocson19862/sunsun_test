<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model{
    protected $fillable;

    // Table Name
    protected $table = 'tr_password_resets';

    protected $primaryKey = 'id';
    // Timestamps
    public $timestamps = true;
    /**
     * @var string
     */

    public function __construct(array $attributes = [])
    {
        $this->fillable = [
            'email',
            'token',
        ];
        parent::__construct($attributes);
    }
}
