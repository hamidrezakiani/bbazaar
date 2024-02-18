<?php

namespace App\Filament\Admin\Resources\AttributeResource\Pages;

use App\Filament\Admin\Resources\AttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttributes extends ListRecords
{
    protected static string $resource = AttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Attributes')
                ->icon('tabler-circle-plus'),
        ];
    }
}
