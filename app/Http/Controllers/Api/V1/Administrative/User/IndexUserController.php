<?php

namespace App\Http\Controllers\Api\V1\Administrative\User;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
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
    public function __invoke(Request $request): JsonApiResourceCollection
    {
        $users = QueryBuilder::for(User::class)
            ->select(['id', 'email', 'name', 'role', 'created_at'])
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
