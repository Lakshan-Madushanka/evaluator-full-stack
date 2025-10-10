<?php

namespace App\Http\Controllers\Api\V1\Administrative\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Spatie\QueryBuilder\QueryBuilder;

class IndexCategoryController extends Controller
{
    public function __invoke(): \TiMacDonald\JsonApi\JsonApiResourceCollection
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters(['name'])
            ->defaultSort('name')
            ->allowedSorts('name')
            ->get();

        return CategoryResource::collection($categories);
    }
}
