<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\OptimizeAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OptimizeController extends Controller
{

    public function __invoke(OptimizeAction $action): JsonResponse
    {
        $status = $action->execute();

        return response()->json(['status' => $status]);
    }
}
