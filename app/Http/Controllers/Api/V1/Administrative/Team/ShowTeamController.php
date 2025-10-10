<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;

class ShowTeamController extends Controller
{
    public function __invoke(Team $team): TeamResource
    {
        return new TeamResource($team);
    }
}
