<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Enums\Pagination;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProductStatsOverview extends BaseWidget
{
    use HasWidgetShield;
     
    protected static ?int $sort = 7;
    protected static ?string $pollingInterval = '120s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        
        $defaultCurrency = 'AFN';
        $conversionRate = Currency::where('code','USD')->first()?->price;
        
       

        $products = Product::query()
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->join('orders', 'orders.id', '=', 'ordered_products.order_id')
            ->select('products.id', 'products.title', 'products.image',
                DB::raw('(COUNT(products.id)) as total'),
                DB::raw('SUM(CASE
                            WHEN orders.currency = "'.$defaultCurrency.'" THEN ordered_products.selling
                            ELSE 0
                        END) as total_price_afn'),
                DB::raw('SUM(CASE
                            WHEN orders.currency = "USD" THEN ordered_products.selling
                            ELSE 0
                        END) as total_price_usd')
            )
             ->orderBy('total', 'DESC')
            ->groupBy('products.id')
            ->limit(10)
            ->limit(Pagination::DASHBOARD_TOP_PRODUCT)
            ->get();

        // Convert the total price to AFN for products in USD
        foreach ($products as $product) {
            if ($product->currency !== $defaultCurrency) {
                $product->total_price_afn = $product->total_price_afn + ($product->total_price_usd * $conversionRate);
            }
        }
    

        return [
            Stat::make('Top Product', '')
                ->view('filament.admin.widgets.dashboard.top-product',compact('products')),
        ];
    }
}
