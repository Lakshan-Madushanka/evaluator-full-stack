<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexTeamController extends Controller
{
    public function __invoke(): JsonApiResourceCollection
    {
        $teams = QueryBuilder::for(Team::class)
            ->withCount(['users', 'questionnaires'])
            ->allowedFilters(['name'])
            ->defaultSort('name')
            ->allowedSorts(['name', 'created_at'])
            ->get();

        return TeamResource::collection($teams);
    }
}
