<?php

namespace App\Enums;

enum Difficulty: int
{
    case EASY = 1;
    case MEDIUM = 2;
    case HARD = 3;

    /**
     * @return string[]
     */
    public static function getNames(): array
    {
        return array_column(self::cases(), 'name');
    }
}
