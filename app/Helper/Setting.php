<?php

namespace App\Helper;

use App\Models\Currency;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Setting as ModelsSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Setting
{

    public static function siteSetting()
    {
        return \App\Models\Setting::first();
    }

    public static function pagination(): array
    {
        return [
            10,
            25,
            50,
            100
        ];
    }

    public static function sliderType(): array
    {
        return [
            1 => 'Main',
            2 => 'Right Top',
            3 => 'Right Bottom'
        ];
    }

    public static function sliderSourceType($state): string
    {
        return match ($state) {
            1 => 'Category',
            2 => 'Sub Category',
            3 => 'Tags',
            4 => 'Brand',
            5 => 'Product',
            6 => 'url'
        };
    }

    public static function sourceType(): array
    {
        return [
            1 => 'Category',
            2 => 'Sub Category',
            3 => 'Tags',
            4 => 'Brand',
            5 => 'Product',
            6 => 'url'
        ];
    }

    public function admin_id(): int
    {
        return Auth::user()->id;
    }

    public static function bannerType(): array
    {
        return [
            1 => 'BANNER_1',
            2 => 'BANNER_2',
            3 => 'BANNER_3',
            4 => 'BANNER_4',
            5 => 'BANNER_5',
            6 => 'BANNER_6',
            7 => 'BANNER_7',
            8 => 'BANNER_8',
            9 => 'BANNER_9',
        ];
    }

    public static function banner($state): string
    {
        return match ($state) {
            1 => 'BANNER_1',
            2 => 'BANNER_2',
            3 => 'BANNER_3',
            4 => 'BANNER_4',
            5 => 'BANNER_5',
            6 => 'BANNER_6',
            7 => 'BANNER_7',
            8 => 'BANNER_8',
            9 => 'BANNER_9',
        };
    }

    public static function footerLinkType($type)
    {
        return match ($type) {
            1 => 'SERVICE',
            2 => 'ABOUT'
        };
    }

    public static function footerImageLinkType($type)
    {
        return match ($type) {
            1 => 'PAYMENT',
            2 => 'SOCIAL'
        };
    }


    public static function currency($amount): string
    {
        $currency = ModelsSetting::first();
        if ($currency->currency_position == 1) {
            return $currency->currency_icon . number_format($amount,2);
        } else {
            return number_format($amount,2) . $currency->currency_icon;
        }
    }

    public static function dateTime($dateTime){
        return Carbon::parse($dateTime)->format('F j, Y - g:i A');
    }

    function getDollar()
    {
        try {
            $cacheKey = 'getDollar';
            return Utils::cacheRemember($cacheKey, function () {
                return Currency::where('code', 'USD')->first();
            });
        }catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }

}
