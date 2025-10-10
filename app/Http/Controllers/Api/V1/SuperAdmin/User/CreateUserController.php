<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UserStoreRequest $request): UserResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();
        $validatedInputs['password'] = Hash::make($validatedInputs['password']);

        $user = User::create($validatedInputs);

        return new UserResource($user);
    }
}
