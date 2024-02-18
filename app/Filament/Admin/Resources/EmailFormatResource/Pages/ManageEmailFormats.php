<?php

namespace App\Filament\Admin\Resources\EmailFormatResource\Pages;

use App\Filament\Admin\Resources\EmailFormatResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEmailFormats extends ManageRecords
{
    protected static string $resource = EmailFormatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->slideOver()
            ->modalWidth('xl'),
        ];
    }
}
