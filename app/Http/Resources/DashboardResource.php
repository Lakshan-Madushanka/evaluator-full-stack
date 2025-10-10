<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

class DashboardResource extends JsonApiResource
{
    /**
     * Transform the resource into an array.
     */
    public function toAttributes(Request $request): array
    {
        return $this->resource;
    }

    public function toId(Request $request): string
    {
        return '';
    }

    public function toType(Request $request): string
    {
        return 'dashboard';
    }
}
