<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Category
 */
class CategoryResource extends JsonApiResource
{
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'created_at' => $this->created_at,
        ];
    }
}
