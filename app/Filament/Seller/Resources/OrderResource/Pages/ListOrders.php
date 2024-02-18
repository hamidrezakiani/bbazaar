<?php

namespace App\Filament\Seller\Resources\OrderResource\Pages;

use App\Filament\Seller\Resources\OrderResource;
use App\Helper\Setting;
use App\Models\Helper\Utils;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function getTableQuery(): ?Builder
    {
//        return Order::query()->where('admin_id',auth()->user()->id);
        $adminId = auth()->user()->id;
        $query = Order::query();
        $query = $query->with('address');
        $query = $query->with('user_info');
        $query = $query->with('cancellation');
        $query = $query->with('address');
        $query = $query->with('user');
        $query = $query->with('guest_user');
        $query = $query->with('ordered_products.shipping_place');;

        $query = $query->whereHas('ordered_products.product', function ($query) use ($adminId) {
            $query->where('admin_id', $adminId);
        });


        $query = $query->with(['ordered_products.product' => function ($subQuery) use ($adminId) {
            $subQuery->where('products.admin_id', $adminId)
                ->select(
                    'products.id',
                    'products.title',
                    'products.image',
                    'products.selling',
                    'products.offered',
                    'products.shipping_rule_id',
                    'products.bundle_deal_id',
                    'products.unit',
                    'products.title'
                );
        }]);

        $query = $query->with('voucher')
            ->with('ordered_products.updated_inventory.inventory_attributes.attribute_value')
            ->with('ordered_products.updated_inventory.inventory_attributes.attribute_value.attribute');


        $query = $query->select('orders.*')->latest();
//        $data = $query->paginate(Setting::pagination());

        $orderIds = [];

        foreach ($query as $item) {
            array_push($orderIds, $item->id);
            $orderedProducts = [];
            foreach ($item->ordered_products as $j) {
                if ($j->product) {
                    array_push($orderedProducts, $j);
                }
            }

            $item['calculated'] = Utils::calcPrice($item);

            unset($item['ordered_products']);
            $item['ordered_products'] = $orderedProducts;
            $item['created'] = Utils::formatDate($item->created_at);
        }


        Order::whereIn('id', $orderIds)->where('viewed', false)->update([
            'viewed' => true
        ]);

        return $query;

    }

}
