<?php

namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Models\UpdatedInventory;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\ProductResource;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate():void{
//        $product = $this->record;
//        UpdatedInventory::created([
//            'product_id' => $product->product_id,
//            'quantity' => $product->quantity
//        ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAnotherFormAction()->color('danger'),
            $this->getCancelFormAction(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        $record = $this->getRecord();
        return $this->getResource()::getUrl('edit',[$record]);
    }

}
