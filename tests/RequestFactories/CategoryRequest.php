<?php

namespace Tests\RequestFactories;

use Illuminate\Support\Str;
use Worksome\RequestFactories\RequestFactory;

class CategoryRequest extends RequestFactory
{
    public function definition(): array
    {
        return ['name' => Str::random()];
    }
}
