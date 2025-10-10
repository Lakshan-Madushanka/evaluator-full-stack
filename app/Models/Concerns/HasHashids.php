<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Vinkla\Hashids\Facades\Hashids;

trait HasHashids
{
    protected function hashId(): Attribute
    {
        return Attribute::get(
            fn () => Hashids::encode($this->getKey())
        );
    }
}
