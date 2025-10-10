<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Team;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Vinkla\Hashids\Facades\Hashids;

class AttachTeamController extends Controller
{
    public function __invoke(User $user, Request $request): Response
    {
        $teamIds = collect($request->input('teamIds'), [])
            ->transform(function ($teamId) {
                return Hashids::decode($teamId)[0] ?? null;
            })->unique()->toArray();

        $request->merge(['teamIds' => $teamIds]);

        $validated = $request->validate([
            'teamIds' => ['required', 'array'],
            'teamIds.*' => [Rule::exists('teams', 'id')],
        ]);

        $existingTeamIds = $user->teams->pluck('id')->toArray();

        $attachableIds = array_diff($teamIds, $existingTeamIds);

        $user->teams()->attach($attachableIds);

        return response(status: ResponseAlias::HTTP_CREATED);
    }
}
