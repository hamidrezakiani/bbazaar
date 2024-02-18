<?php

namespace App\Filament\Admin\Widgets\Admin;

use App\Models\User;
use App\Models\Order;
use App\Helper\Setting;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\GuestUser;
use App\Models\OrderedProduct;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use App\Models\Currency;

class StateOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    use HasWidgetShield;
    protected static ?string $pollingInterval = '120s';

    protected function getStats(): array
    {
        
        $defaultCurrency = 'AFN';
        // Assuming 1 USD = 0.1 AFN, adjust the conversion rate as per your needs
        $conversionRate = Currency::where('code','USD')->first()?->price;

        $totalProduct = Product::count();
        $totalUser = User::count();
        $totalOrders = Order::count();
        $totalAmount = OrderedProduct::join('orders', 'orders.id', 'ordered_products.order_id')
                    ->join('products as p', function ($join) {
                        $join->on('p.id', '=', 'ordered_products.product_id');
                })->where('orders.cancelled', '!=', 1)
            ->select(DB::raw('SUM(CASE
                        WHEN orders.currency = "'.$defaultCurrency.'" THEN ordered_products.selling * ordered_products.quantity
                        ELSE ordered_products.selling * ordered_products.quantity * "'.$conversionRate.'"
                    END) as amount'))
            ->first();
            
        $totalSales = $totalAmount->amount;
        $totalSales = Setting::currency($totalSales);

        $totalCategory = Category::count();
        $totalBrand = Brand::count();
        $totalCustomer = User::count();
        $totalGuest = GuestUser::count();

        return [
            Stat::make('Products', '')
                ->view('filament.admin.widgets.dashboard.state',
                    compact('totalProduct', 'totalUser', 'totalCustomer', 'totalGuest', 'totalOrders', 'totalSales', 'totalCategory', 'totalBrand')),
        ];
    }
}
