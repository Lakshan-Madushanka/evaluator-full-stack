<?php

namespace App\Http\Controllers\Api\V1\Administrative\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class ShowCategoryController extends Controller
{
    public function __invoke(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }
}
