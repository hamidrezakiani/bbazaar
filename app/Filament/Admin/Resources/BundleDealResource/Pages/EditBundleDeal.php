<?php

namespace App\Filament\Admin\Resources\BundleDealResource\Pages;

use Filament\Actions;
use Filament\Support\Colors\Color;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\BundleDealResource;

class EditBundleDeal extends EditRecord
{
    protected static string $resource = BundleDealResource::class;

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
