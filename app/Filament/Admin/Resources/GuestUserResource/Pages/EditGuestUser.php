<?php

namespace App\Filament\Admin\Resources\GuestUserResource\Pages;

use App\Filament\Admin\Resources\GuestUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestUser extends EditRecord
{
    protected static string $resource = GuestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
