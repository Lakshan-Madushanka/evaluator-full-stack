<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\User;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Vinkla\Hashids\Facades\Hashids;

class DetachUserController extends Controller
{
    public function __invoke(Team $team, Request $request): Response
    {
        $userIds = collect($request->input('userIds'), [])
            ->transform(function ($userId) {
                return Hashids::decode($userId)[0] ?? null;
            })->unique();

        $request->merge(['userIds' => $userIds->toArray()]);

        $validated = $request->validate([
            'userIds' => ['required', 'array'],
            'userIds.*' => [Rule::exists('users', 'id')],
        ]);

        $team->users()->detach($validated['userIds']);

        return response()->noContent();
    }
}
