<?php

namespace Tests\RequestFactories;

use App\Enums\Role;
use App\Models\User;
use Worksome\RequestFactories\RequestFactory;

class UserRequest extends RequestFactory
{
    public function definition(): array
    {
        $userFactory = User::factory()->make()->makeVisible(['password'])->toArray();
        $userFactory['role'] = Role::ADMIN->value;
        $userFactory['password_confirmation'] = $userFactory['password'];

        return $userFactory;
    }
}
