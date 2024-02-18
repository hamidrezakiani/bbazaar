<?php

namespace App\Filament\Seller\Widgets;

use App\Enums\Pagination;
use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderedProduct;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TopCategoryStatsOverview extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $topCategory = Category::query()
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')
            ->select(
                'categories.id',
                'categories.title',
                DB::raw("(COUNT(categories.id)) as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )->orderBy('total_price', 'DESC')
            ->where('products.admin_id', auth()->user()->id)
            ->groupBy('categories.id')
            ->limit(Pagination::DASHBOARD)
            ->get();


        $topBrand = Brand::query()->join('products', 'products.brand_id', '=', 'brands.id')
            ->join('ordered_products', 'ordered_products.product_id', '=', 'products.id')->select(
                'brands.id',
                'brands.title',
                DB::raw("(COUNT(brands.id)) as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )->orderBy('total_price', 'DESC')
            ->where('products.admin_id', auth()->user()->id)
            ->groupBy('brands.id')
            ->limit(Pagination::DASHBOARD)
            ->get();

        return [
            Stat::make('Top Category', '')
                ->view('filament.admin.widgets.dashboard.top-category',
                    compact('topCategory', 'topBrand')),
        ];
    }
}
