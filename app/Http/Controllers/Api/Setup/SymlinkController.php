<?php

namespace App\Http\Controllers\Api\Setup;

use App\Actions\Setup\CreateSymlinkAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SymlinkController extends Controller
{
    public function __invoke(CreateSymlinkAction $action): JsonResponse
    {
        $status = $action->execute();

        return response()->json(['status' => $status]);
    }
}
