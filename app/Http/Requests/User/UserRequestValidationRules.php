<?php

namespace App\Http\Requests\User;

use App\Enums\Role;
use App\Http\Requests\Contracts\RequestValidationRulesContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequestValidationRules implements RequestValidationRulesContract
{
    /**
     * @return array<string, mixed>
     */
    public static function getRules(Request $request): array
    {
        return [
            'name' => [
                'string',
                'required',
                'max:100',
            ],
            'email' => [
                'string',
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
            'role' => [
                'required',
                'int',
                Rule::in([
                    Role::ADMIN->value,
                    Role::REGULAR->value,
                ]),
            ],
        ];
    }

    public static function prepareData(Request $request): void
    {
        /*
         * As regular users aren't meant to login
         * we just assign a random integer
         */

        /** @var string|int $role */
        $role = $request->input('role');

        if ((int) $role === Role::REGULAR->value) {
            $password = Str::random();
            $request->merge([
                'password' => $password,
                'password_confirmation' => $password,
            ]);
        }
    }
}
