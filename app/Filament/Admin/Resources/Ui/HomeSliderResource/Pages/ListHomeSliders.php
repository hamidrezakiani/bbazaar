<?php

namespace App\Filament\Admin\Resources\Ui\HomeSliderResource\Pages;

use App\Filament\Admin\Resources\Ui\HomeSliderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeSliders extends ListRecords
{
    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Home Slider')
            ->icon('tabler-circle-plus'),
        ];
    }
}
