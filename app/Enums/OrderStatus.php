<?php

namespace App\Enums;

enum OrderStatus:int
{
    case PENDING = 1;
    case CONFIRMED = 2;
    case PICKED_UP = 3;
    case ON_THE_WAY = 4;
    case DELIVERED = 5;
}
