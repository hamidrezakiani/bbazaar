<?php

namespace App\Enums;

enum PaymentMethod: int
{
    case RAZORPAY = 1;
    case CASH_ON_DELIVERY = 2;
    case STRIPE = 3;
    case PAYPAL = 4;
    case FLUTTERWAVE = 5;
    case IYZICO_PAYMENT = 6;
    case BANK = 7;
}
