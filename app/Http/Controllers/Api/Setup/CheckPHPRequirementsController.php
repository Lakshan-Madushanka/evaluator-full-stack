<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CheckPHPExtensions;
use App\Actions\Setup\CheckPHPVersion;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CheckPHPRequirementsController extends Controller
{
    public function checkPHPVersion(CheckPHPVersion $checkPHPVersion): JsonResponse
    {
        return response()->json($checkPHPVersion->execute());
    }

    public function checkExtensions(CheckPHPExtensions $checkPHPExtensions): JsonResponse
    {
        $list = $checkPHPExtensions->execute();

        $isPasses = collect($list)->every(fn($v) => $v === true);;

        return response()->json([
            'list' => $list,
            'is_passed' => $isPasses,
        ]);
    }
}
