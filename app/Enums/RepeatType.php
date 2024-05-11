<?php

namespace App\Enums;

enum RepeatType: int
{
    case NONE = 0;
    case EVERY_WEEK = 1;
    case EVEN_WEEK = 2;
    case ODD_WEEK = 3;
}
