<?php

namespace App\Filament\Admin\Resources\Ui\HeaderLinkResource\Pages;

use App\Filament\Admin\Resources\Ui\HeaderLinkResource;
use Filament\Resources\Pages\EditRecord;

class EditHeaderLink extends EditRecord
{
    protected static string $resource = HeaderLinkResource::class;

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
