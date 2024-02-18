<?php

namespace App\Filament\Seller\Resources\ProductResource\Pages;

use App\Filament\Seller\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Product')
                ->icon('tabler-circle-plus')
        ];
    }


    public function getTableQuery(): ?Builder
    {
        $adminId = auth()->user()->id;
        return Product::query()->where('admin_id', $adminId)->latest();
    }


}
