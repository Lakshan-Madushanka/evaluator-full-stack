<?php

namespace App\Http\Controllers\Api\V1\Administrative\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class UpdateCategoryController extends Controller
{
    public function __invoke(Category $category, CategoryRequest $request): CategoryResource
    {
        /** @var array<string> $validatedInputs * */
        $validatedInputs = $request->validated();

        $category->update($validatedInputs);

        return new CategoryResource($category);
    }
}
