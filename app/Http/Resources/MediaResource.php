<?php

namespace App\Http\Resources;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin Media
 */
class MediaResource extends JsonApiResource
{
    public function toAttributes($request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'collection' => $this->collection_name,
            'original_url' => $this->original_url,
            'created_at' => $this->created_at,
            'order_column' => $this->order_column,
        ];
    }
}
