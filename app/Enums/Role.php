<?php

namespace App\Enums;

enum Role: int
{
    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case REGULAR = 3;
}
