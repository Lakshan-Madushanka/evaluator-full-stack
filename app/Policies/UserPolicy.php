<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
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

    public function update(User $authUser, User $currentUser): Response
    {
        return $authUser->id === $currentUser->id ?
            Response::allow() :
            Response::denyAsNotFound();
    }
}
