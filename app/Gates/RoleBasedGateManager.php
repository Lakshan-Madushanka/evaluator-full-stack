<?php

namespace App\Gates;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class RoleBasedGateManager
{
    public static function registerGates(): void
    {
        self::defineSuperAdminGate();
        self::defineAdminGate();
        self::defineAdministrativeGate();
    }

    public static function defineSuperAdminGate(): void
    {
        Gate::define('super-admin', function (User $user) {
            return $user->role->value === Role::SUPER_ADMIN->value ?
                Response::allow() :
                Response::denyAsNotFound();
        });
    }

    public static function defineAdminGate(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->role->value === Role::ADMIN->value ?
                Response::allow() :
                Response::denyAsNotFound();
        });
    }

    public static function defineAdministrativeGate(): void
    {
        Gate::define('administrative', function (User $user) {
            $userRoleValue = $user->role->value;

            return $userRoleValue === Role::ADMIN->value || $userRoleValue === Role::SUPER_ADMIN->value ?
                Response::allow() :
                Response::denyAsNotFound();
        });
    }
}
