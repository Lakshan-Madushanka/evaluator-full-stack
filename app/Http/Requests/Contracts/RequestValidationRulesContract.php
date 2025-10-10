<?php

namespace App\Http\Requests\Contracts;

use Illuminate\Http\Request;

interface RequestValidationRulesContract
{
    /**
     * @return array<string, mixed>
     */
    public static function getRules(Request $request): array;
}
