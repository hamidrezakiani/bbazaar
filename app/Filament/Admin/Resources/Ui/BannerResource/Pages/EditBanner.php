<?php

namespace App\Filament\Admin\Resources\Ui\BannerResource\Pages;

use App\Filament\Admin\Resources\Ui\BannerResource;
use Filament\Resources\Pages\EditRecord;

class EditBanner extends EditRecord
{
    protected static string $resource = BannerResource::class;

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
