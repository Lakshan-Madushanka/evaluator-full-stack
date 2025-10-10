<?php

namespace App\Http\Controllers\Api\V1\SuperAdmin\User;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class MassDeleteUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  UserStoreRequest  $request
     *
     * @throws \Throwable
     */
    public function __invoke(\Illuminate\Http\Request $request): JsonResponse
    {
        $validatedInputs = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['string'],
        ]);

        $deletableIds = [];

        /*
         * Removing ids that are belongs to super admin
         * as we don't need to delete super admin users
         */

        /**
         * @var string[] $ids
         */
        $ids = $validatedInputs['ids'];

        collect($ids)->each(function (string $hasIid) use (&$deletableIds) {
            $id = Hashids::decode($hasIid);

            if (! isset($id[0])) {
                return;
            }

            /** @var User|null $user */
            $user = User::find($id[0]);

            if ($user && $user->role->value !== Role::SUPER_ADMIN->value) {
                $deletableIds[] = $id[0];
            }
        });

        User::whereIn('id', $deletableIds)->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
