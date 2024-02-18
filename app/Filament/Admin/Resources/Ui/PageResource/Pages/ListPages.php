<?php

namespace App\Filament\Admin\Resources\Ui\PageResource\Pages;

use App\Filament\Admin\Resources\Ui\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Page')
            ->icon('tabler-circle-plus'),
        ];
    }
}
