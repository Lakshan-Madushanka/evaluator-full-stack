<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckPHPExtensionsAction;
use App\Actions\Setup\CheckPHPVersionAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckPHPRequirementsController extends Controller
{
    public function checkPHPVersion(CheckPHPVersionAction $checkPHPVersion): JsonResponse
    {
        return response()->json($checkPHPVersion->execute());
    }

    public function checkExtensions(CheckPHPExtensionsAction $checkPHPExtensions): JsonResponse
    {
        $list = $checkPHPExtensions->execute();

        $isPasses = collect($list)->every(fn($v) => $v === true);;

        return response()->json([
            'list' => $list,
            'is_passed' => $isPasses,
        ]);
    }
}
