<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\User;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws \Throwable
     */
    public function __invoke(User $user): JsonResponse
    {
        throw_if(
            $user->role->value === Role::SUPER_ADMIN->value,
            new AuthorizationException('Super admin cannot be deleted !')
        );

        $user->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
