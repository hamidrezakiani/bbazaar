<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\FooterLink;
use App\Models\HomeSlider;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Observers\OrderObserver;
use App\Observers\ImageObserver;
use App\Observers\VideoObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SubCategory::observe(ImageObserver::class);
        Category::observe(ImageObserver::class);
        Brand::observe(ImageObserver::class);
        Product::observe(ImageObserver::class);
        Product::observe(VideoObserver::class);
        ProductImage::observe(ImageObserver::class);
        FooterLink::observe(ImageObserver::class);
        Banner::observe(ImageObserver::class);
        HomeSlider::observe(ImageObserver::class);
        Store::observe(ImageObserver::class);
        Order::observe(OrderObserver::class);

    }
}
