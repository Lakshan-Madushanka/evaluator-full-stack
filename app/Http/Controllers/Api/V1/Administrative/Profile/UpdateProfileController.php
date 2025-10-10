<?php

namespace App\Http\Controllers\Api\V1\Administrative\Profile;

use App\Actions\User\UpdateUserAction;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Arr;

class UpdateProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws \Throwable
     */
    public function __invoke(UserUpdateRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $validatedInputs = Arr::except((array) $request->validated(), 'role'); // we don't need role to be updated

        /*
         * we only need admin to update their password
         */
        if ($user->role->value === Role::ADMIN->value) {
            $validatedInputs = Arr::only($validatedInputs, ['password']);
        }

        UpdateUserAction::execute($validatedInputs, $user);

        return new UserResource($user);
    }
}
