<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use TiMacDonald\JsonApi\JsonApiResource;

/**
 * @mixin User
 */
class UserResource extends JsonApiResource
{
    /**
     * @return array|mixed[]
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->name,
            'created_at' => $this->created_at,
        ];
    }
}
