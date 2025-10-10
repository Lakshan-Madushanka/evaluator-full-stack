<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;

class StoreTeamController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(TeamRequest $request): TeamResource
    {

        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $answer = Team::create($validatedInputs);

        return new TeamResource($answer);
    }
}
