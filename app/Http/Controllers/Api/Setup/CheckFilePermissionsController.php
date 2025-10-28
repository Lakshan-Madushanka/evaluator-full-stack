<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckFilePermissionsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckFilePermissionsController extends Controller
{
    public function __invoke(CheckFilePermissionsAction $checkFilePermissions): JsonResponse
    {
        return response()->json($checkFilePermissions->execute());
    }
}
