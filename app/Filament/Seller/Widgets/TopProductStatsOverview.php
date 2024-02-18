<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\Pagination;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TopProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 7;
    protected static ?string $pollingInterval = '5s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $products = Product::query()
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->select('products.id', 'products.title','products.image',
                DB::raw('(COUNT(products.id)) as total'),
                DB::raw('(SUM(ordered_products.selling)) as total_price'))
            ->orderBy('total_price', 'DESC')
            ->where('products.admin_id', auth()->user()->id)
            ->groupBy('products.id')
            ->limit(Pagination::DASHBOARD_TOP_PRODUCT)
            ->get();

        return [
            Stat::make('Top Product', '')
                ->view('filament.admin.widgets.dashboard.top-product',compact('products')),
        ];
    }
}
