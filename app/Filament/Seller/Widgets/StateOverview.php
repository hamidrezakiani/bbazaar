<?php

namespace App\Filament\Seller\Widgets;

use App\Helper\Setting;
use App\Models\Brand;
use App\Models\Category;
use App\Models\GuestUser;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StateOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    use HasWidgetShield;

    protected function getStats(): array
    {
        $adminId = Auth::user()->id;
        $totalProduct = Product::where('admin_id', $adminId)->count();

        $totalAmount = OrderedProduct::join('products as p', function ($join) use ($adminId) {
            $join->on('p.id', '=', 'ordered_products.product_id');
            $join->where('p.admin_id', $adminId);
        })->select(DB::raw('SUM(ordered_products.selling * ordered_products.quantity) as amount'))
            ->first();
        $totalSales = $totalAmount->amount;
        $totalSales = Setting::currency($totalSales);

        $query = Order::join('ordered_products as op', function ($join) use ($adminId){
            $join->on('op.order_id', '=', 'orders.id');
            $join->join('products as p', function ($join2) use($adminId) {
                $join2->on('p.id', '=', 'op.product_id');
                $join2->where('p.admin_id', $adminId);
            });
        });
        $totalOrders = $query->count();

        return [
            Stat::make('Products', '')
                ->view('filament.seller.widgets.state',
                    compact('totalProduct', 'totalOrders', 'totalSales')),
        ];
    }
}
