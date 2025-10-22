<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckEnvAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckEnvController extends Controller
{
    public function __invoke(CheckEnvAction $checkENV): JsonResponse
    {
        return response()->json($checkENV->execute());
    }
}
