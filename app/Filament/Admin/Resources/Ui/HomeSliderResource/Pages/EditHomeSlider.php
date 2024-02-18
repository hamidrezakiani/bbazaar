<?php

namespace App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages;

use App\Filament\Admin\Resources\Ui\HomeSliderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomeSlider extends EditRecord
{
    protected static string $resource = HomeSliderResource::class;

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
