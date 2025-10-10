<?php

namespace App\Http\Controllers\Api\V1\Administrative\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowAuthUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): UserResource
    {
        return new UserResource(Auth::user());
    }
}
