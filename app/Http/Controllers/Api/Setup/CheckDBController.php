<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckDBAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckDBController extends Controller
{
    public function migrate(CheckDBAction $checkDBAction): JsonResponse
    {
        return response()->json([
            'migrated_status' => $checkDBAction->execute()
        ]);
    }

    public function info(CheckDBAction $checkDBAction): JsonResponse
    {
        return response()->json($checkDBAction->getInfo());
    }

    public function checkConnection(CheckDBAction $checkDBAction): JsonResponse
    {
        return response()->json($checkDBAction->checkConnection());
    }
}
