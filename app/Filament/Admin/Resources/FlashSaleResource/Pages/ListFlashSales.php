<?php

namespace App\Filament\Admin\Resources\FlashSaleResource\Pages;

use App\Filament\Admin\Resources\FlashSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashSales extends ListRecords
{
    protected static string $resource = FlashSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Flash Sales')
                ->icon('tabler-circle-plus')
        ];
    }
}
