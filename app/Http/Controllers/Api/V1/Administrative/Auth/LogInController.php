<?php

namespace App\Http\Controllers\Api\V1\Administrative\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LogInController extends Controller
{
    /**
     * Handle the incoming request.
     *
     *
     * @throws ValidationException
     */
    public function __invoke(Request $request): UserResource
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['boolean'],
        ]);

        $user = User::whereEmail($credentials['email'])->first()?->makeVisible(['password']);

        if (is_null($user) || ! Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse();
        }

        // we don't need to let login regular users
        $isRoleValid = collect([Role::ADMIN->value, Role::SUPER_ADMIN->value])->contains($user->role->value);

        if ($isRoleValid) {
            $shouldRemember = $credentials['remember'] ?? false;
            Auth::login($user, $shouldRemember);
            $request->session()->regenerate();

            return new UserResource(Auth::user());
        }

        return $this->errorResponse();
    }

    /**
     * @throws ValidationException
     */
    public function errorResponse(): never
    {
        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
