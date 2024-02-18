<?php

namespace App\Filament\Admin\Resources\Ui\HeaderLinkResource\Pages;

use App\Filament\Admin\Resources\Ui\HeaderLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHeaderLinks extends ListRecords
{
    protected static string $resource = HeaderLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('lg')
                ->label('Header Link')
                ->icon('tabler-circle-plus')
                ->createAnother(false),
        ];
    }
}
