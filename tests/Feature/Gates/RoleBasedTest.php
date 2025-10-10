<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

/**
 * super-admin gate
 */
test('super-admin gate allow super admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::SUPER_ADMIN)->first());
    expect(Gate::allows('super-admin'))->toBeTrue();
});

test('super-admin gate disallow admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::ADMIN)->first());
    expect(Gate::allows('super-admin'))->toBeFalse();
});

test('super-admin gate disallow regular users ', function () {
    Sanctum::actingAs(User::whereRole(Role::REGULAR)->first());
    expect(Gate::allows('super-admin'))->toBeFalse();
});

test('super-admin gate throws 404 response for unauthorized access', function () {
    // Regular user login
    Sanctum::actingAs(User::whereRole(Role::REGULAR)->first());
    try {
        $exception = Gate::authorize('super-admin');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBe(404);
    }

    // admin user login
    Sanctum::actingAs(User::whereRole(Role::ADMIN)->first());
    try {
        $exception = Gate::authorize('super-admin');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBe(404);
    }
});

/**
 * admin gate
 */
test('admin gate allow admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::ADMIN)->first());
    expect(Gate::allows('admin'))->toBeTrue();
});

test('admin gate disallow super admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::SUPER_ADMIN)->first());
    expect(Gate::allows('admin'))->toBeFalse();
});

test('admin gate disallow regular users ', function () {
    Sanctum::actingAs(User::whereRole(Role::REGULAR)->first());
    expect(Gate::allows('admin'))->toBeFalse();
});

test('admin gate throws 404 response for unauthorized access', function () {
    // Regular user login
    Sanctum::actingAs(User::whereRole(Role::REGULAR)->first());
    try {
        $exception = Gate::authorize('admin');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBe(404);
    }

    // admin user login
    Sanctum::actingAs(User::whereRole(Role::SUPER_ADMIN)->first());
    try {
        $exception = Gate::authorize('super-admin');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBe(404);
    }
});

/**
 * administrative gate
 */
/**
 * super-admin gate
 */
test('administrative gate allow super admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::SUPER_ADMIN)->first());
    expect(Gate::allows('administrative'))->toBeTrue();
});

test('administrative gate allow admin users ', function () {
    Sanctum::actingAs(User::whereRole(Role::ADMIN)->first());
    expect(Gate::allows('administrative'))->toBeTrue();
});

test('administrative gate throws 404 response for unauthorized access', function () {
    // Regular user login
    Sanctum::actingAs(User::whereRole(Role::REGULAR)->first());
    try {
        $exception = Gate::authorize('administrative');
    } catch (AuthorizationException $exception) {
        expect($exception->status())->toBe(404);
    }
});
