<?php

namespace App\Filament\Admin\Resources\AdminVendorResource\Pages;

use App\Filament\Admin\Resources\AdminVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminVendors extends ListRecords
{
    protected static string $resource = AdminVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
