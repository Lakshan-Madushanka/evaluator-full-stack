<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CreateSuperAdminAccountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequestValidationRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckSuperAdminAccountController extends Controller
{

    public function checkExists(CreateSuperAdminAccountAction $action): JsonResponse
    {
        $accountExists = $action->checkAccountExists();

        return response()->json(['exists' => $accountExists]);
    }

    public function create(Request $request, CreateSuperAdminAccountAction $action): JsonResponse
    {
        $rules = UserRequestValidationRules::getRules($request);

        unset($rules['role']);

        $validatedInputs = $request->validate($rules);

        $user = $action->execute($validatedInputs);

        return response()->json($user);
    }

}
