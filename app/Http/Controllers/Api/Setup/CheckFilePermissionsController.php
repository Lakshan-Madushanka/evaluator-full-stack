<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckFilePermissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckFilePermissionsController extends Controller
{
    public function __invoke(CheckFilePermissions $checkFilePermissions): JsonResponse
    {
        return response()->json($checkFilePermissions->execute());
    }
}
