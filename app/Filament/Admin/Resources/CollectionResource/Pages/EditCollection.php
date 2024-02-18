<?php

namespace App\Filament\Admin\Resources\CollectionResource\Pages;

use Filament\Actions;
use Filament\Support\Colors\Color;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\CollectionResource;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

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
