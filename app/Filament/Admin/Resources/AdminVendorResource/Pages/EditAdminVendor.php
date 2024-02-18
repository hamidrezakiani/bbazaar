<?php

namespace App\Filament\Admin\Resources\AdminVendorResource\Pages;

use App\Filament\Admin\Resources\AdminVendorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminVendor extends EditRecord
{
    protected static string $resource = AdminVendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
