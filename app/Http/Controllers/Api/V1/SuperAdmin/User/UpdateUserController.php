<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\User;

use App\Actions\User\UpdateUserAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws \Throwable
     */
    public function __invoke(UserUpdateRequest $request, User $user): UserResource
    {
        throw_if(
            $user->role->value === Role::SUPER_ADMIN->value,
            new AuthorizationException('Super admin cannot be updated here, Please use profile section !')
        );

        UpdateUserAction::execute((array) $request->validated(), $user);

        return new UserResource($user);
    }
}
