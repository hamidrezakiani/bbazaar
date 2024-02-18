<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\Brand;
use App\Models\Currency;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopCategoryStatsOverview extends BaseWidget
{
    use HasWidgetShield;
    
    protected static ?int $sort = 5;
    protected static ?string $pollingInterval = '120s';


    protected function getStats(): array
    {
        
        $defaultCurrency = 'AFN';
        // Assuming 1 USD = 0.1 AFN, adjust the conversion rate as per your needs
        $conversionRate = Currency::where('code','USD')->first()?->price;
        
        $topCategory = Category::query()
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->join('orders','orders.id','ordered_products.order_id')
            ->select(
                'categories.id',
                'categories.title',
                'categories.image',
                DB::raw('(COUNT(categories.id)) as total'),
                DB::raw('(SUM(CASE
                    WHEN orders.currency = "'.$defaultCurrency.'" THEN ordered_products.selling
                    ELSE ordered_products.selling * "'.$conversionRate.'"
                END)) as total_price_afn')
            )
            ->orderBy('total_price_afn', 'DESC')
            ->groupBy('categories.id')
            ->get();
            
        // $topCategory = Category::query()->join('products', 'products.category_id', '=', 'categories.id')
        //     ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')->select(
        //         'categories.id',
        //         'categories.title',
        //         DB::raw("(COUNT(categories.id)) as total"),
        //         DB::raw("(SUM(ordered_products.selling)) as total_price")
        //     )->orderBy('total_price', 'DESC')->groupBy('categories.id')->get();

        // $topBrand = Brand::query()->join('products', 'products.brand_id', '=', 'brands.id')
        //     ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')->select(
        //         'brands.id',
        //         'brands.title',
        //         DB::raw("(COUNT(brands.id)) as total"),
        //         DB::raw("(SUM(ordered_products.selling)) as total_price")
        //     )->orderBy('total_price', 'DESC')
        //     ->groupBy('brands.id')
        //     ->get();
        
        $topBrand = Brand::query()
            ->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->join('orders','orders.id','ordered_products.order_id')
            ->select(
                'brands.id',
                'brands.title',
                'brands.image',
                DB::raw('(COUNT(brands.id)) as total'),
                DB::raw('(SUM(CASE
                    WHEN orders.currency = "'.$defaultCurrency.'" THEN ordered_products.selling
                    ELSE ordered_products.selling * "'.$conversionRate.'"
                END)) as total_price_afn')
            )
            ->orderBy('total_price_afn', 'DESC')
            ->groupBy('brands.id')
            ->get();

        return [
            Stat::make('Top Category', '')
                ->view('filament.admin.widgets.dashboard.top-category', compact('topCategory', 'topBrand')),
        ];
    }
}
