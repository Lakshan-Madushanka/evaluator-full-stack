<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class BetweenFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereBetween($property, $value);
    }
}
