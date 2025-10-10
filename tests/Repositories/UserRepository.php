<?php

namespace Tests\Repositories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public static function getRandomUsers(array $role = [Role::REGULAR], int $limit = 10): Collection
    {
        $query = User::query();

        collect($role)->each(function (Role $role) use ($query) {
            $query->orWhere('role', $role->value);
        });

        return $query->limit($limit)->get();
    }

    public static function getRandomUser(Role $role = Role::REGULAR): User
    {
        return User::whereRole($role->value)->inRandomOrder()->first();
    }
}
