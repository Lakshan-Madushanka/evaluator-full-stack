<?php

namespace App\Http\Controllers\Api\V1\Administrative\Team\User;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Team;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use ReflectionEnumBackedCase;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use TiMacDonald\JsonApi\JsonApiResourceCollection;

class IndexUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Team $team, Request $request): JsonApiResourceCollection
    {
        $users = QueryBuilder::for($team->users())
            ->allowedFilters([
                'name',
                'email',
                AllowedFilter::callback('role', function (Builder $query, $value) {
                    $query->where(
                        'role',
                        // This will return enum value by its name ex: SUPER_ADMIN return 1
                        (new ReflectionEnumBackedCase(Role::class, $value))->getBackingValue()
                    );
                }),
            ])
            ->defaultSort('-id')
            ->allowedSorts('created_at', 'role')
            ->jsonPaginate();

        return UserResource::collection($users);
    }
}
