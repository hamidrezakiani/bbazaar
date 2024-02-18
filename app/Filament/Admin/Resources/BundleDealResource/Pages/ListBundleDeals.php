<?php

namespace App\Filament\Admin\Resources\BundleDealResource\Pages;

use App\Filament\Admin\Resources\BundleDealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBundleDeals extends ListRecords
{
    protected static string $resource = BundleDealResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Bundle Deal')
                ->icon('tabler-circle-plus'),
        ];
    }
}
