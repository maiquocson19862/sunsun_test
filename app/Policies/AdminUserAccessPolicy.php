<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\MsUser;

class AdminUserAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function superAdmin(MsUser $ms_user)
    {

        return $ms_user->is_super_admin();
    }


    public function isAdmin(MsUser $ms_user)
    {
        return $ms_user->is_admin();
    }


    public function isUser(MsUser $ms_user)
    {
        return $ms_user->is_user();
    }
}
