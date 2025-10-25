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
            return response()->json(['exists' => $action->checkAccountExists()]);
    }

    public function create(Request $request, CreateSuperAdminAccountAction $action): JsonResponse
    {
        $rules = UserRequestValidationRules::getRules($request);

        unset($rules['role']);

        $validatedInputs = $request->validate($rules);

        return response()->json($action->execute($validatedInputs));
    }
}
