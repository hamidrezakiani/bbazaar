<?php

namespace App\Enums;

enum PriceType: int
{
    case FLAT = 1;
    case PERCENT = 2;
}
