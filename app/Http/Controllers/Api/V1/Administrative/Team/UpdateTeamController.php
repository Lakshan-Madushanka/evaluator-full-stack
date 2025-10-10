<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;

class UpdateTeamController extends Controller
{
    public function __invoke(Team $team, TeamRequest $request): TeamResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $team->update($validatedInputs);

        return new TeamResource($team);
    }
}
