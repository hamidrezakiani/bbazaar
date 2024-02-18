<?php

namespace App\Filament\Admin\Resources\ContactUsResource\Pages;

use App\Filament\Admin\Resources\ContactUsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContactUs extends ManageRecords
{
    protected static string $resource = ContactUsResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
