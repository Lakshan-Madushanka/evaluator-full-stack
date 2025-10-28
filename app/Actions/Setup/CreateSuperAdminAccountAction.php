<?php

namespace App\Actions\Setup;

use App\Enums\Role;
use App\Models\User;

class CreateSuperAdminAccountAction
{
    public function execute(array $inputs): User|false
    {
        if (! $this->checkAccountExists()) {
            return User::create([
                ...$inputs,
                'role' => Role::SUPER_ADMIN,
            ]);
        }

        return false;
    }

    public function checkAccountExists(): bool
    {
        return User::query()
            ->where('role', Role::SUPER_ADMIN)
            ->exists();
    }
}
