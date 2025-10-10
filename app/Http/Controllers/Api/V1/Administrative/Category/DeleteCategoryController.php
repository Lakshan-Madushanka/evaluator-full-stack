<?php

namespace App\Http\Controllers\Api\V1\Administrative\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DeleteCategoryController extends Controller
{
    public function __invoke(Category $category): JsonResponse
    {
        $category->delete();

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
