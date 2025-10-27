<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\OptimizeAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class OptimizeController extends Controller
{

    public function __invoke(OptimizeAction $action): JsonResponse
    {
        Cache::put('setup_status', 'completed');

        $status = $action->execute();

        return response()->json(['status' => $status]);
    }
}
