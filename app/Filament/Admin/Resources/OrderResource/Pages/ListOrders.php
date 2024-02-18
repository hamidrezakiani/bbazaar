<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->hidden(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')->badge(fn(Order $record) => $record->count()),
            'Paid' => Tab::make()->query(fn($query) => $query->where('payment_done', 1))
                ->badge(fn(Order $record) => $record->where('payment_done', 1)->count()),
            'Unpaid' => Tab::make()->query(fn($query) => $query->where('payment_done', 0))
                ->badge(fn(Order $record) => $record->where('payment_done', 0)->count()),
            'Cash on Delivery' => Tab::make()->query(fn($query) => $query->where('order_method', 2))
                ->badge(fn(Order $record) => $record->where('order_method', 2)->count()),
            'third party' => Tab::make()->query(fn($query) => $query->where('order_method', '!=', 2))
                ->badge(fn(Order $record) => $record->where('order_method', '!=', 2)->count()),
            'pending' => Tab::make()->query(fn($query) => $query->where('status', 1))
                ->badge(fn(Order $record) => $record->where('status', 1)->count()),
            'confirmed' => Tab::make()->query(fn($query) => $query->where('status', 2))
                ->badge(fn(Order $record) => $record->where('status', 2)->count()),
            'picked up' => Tab::make()->query(fn($query) => $query->where('status', 3))
                ->badge(fn(Order $record) => $record->where('status', 3)->count()),
            'On the Way' => Tab::make()->query(fn($query) => $query->where('status', 4))
                ->badge(fn(Order $record) => $record->where('status', 4)->count()),
            'delivered' => Tab::make()->query(fn($query) => $query->where('status', 5))
                ->badge(fn(Order $record) => $record->where('status', 5)->count()),
            'cancelled' => Tab::make()->query(fn($query) => $query->where('status', 6))
                ->badge(fn(Order $record) => $record->where('status', 6)->count()),
        ];
    }

    public function getTableQuery(): ?Builder
    {
        return Order::query()->latest();
    }
}
