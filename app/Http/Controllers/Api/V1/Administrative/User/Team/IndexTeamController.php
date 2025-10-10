<?php

namespace App\Http\Controllers\Api\V1\Administrative\User\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexTeamController extends Controller
{
    public function __invoke(User $user): JsonApiResourceCollection
    {
        $teams = QueryBuilder::for($user->teams())
            ->allowedFilters(['name'])
            ->defaultSort('name')
            ->allowedSorts(['name', 'created_at'])
            ->get();

        return TeamResource::collection($teams);
    }
}
