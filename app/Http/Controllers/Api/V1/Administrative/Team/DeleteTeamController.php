<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteTeamController extends Controller
{
    public function __invoke(Team $team): JsonResponse
    {
        $team->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
